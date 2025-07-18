<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Document\EventPicture;
use App\Service\Media\ImageStorageManipulator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'eventoj:event-picture:check-existence',
    description: 'Check if event pictures exists on remote storage.',
)]
final readonly class EventPictureCheckExistenceCommand
{
    public function __construct(
        private EntityManagerInterface $em,
        private ImageStorageManipulator $imageStorageManipulator,
    ) {
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Option(description: "Check all event pictures, even if they've been already checked.")]
        bool $force = false,
    ): int {
        $io = new SymfonyStyle($input, $output);

        if ($force) {
            if (!$io->confirm('Checking all event pictures can take a long time, are you sure?')) {
                return Command::SUCCESS;
            }

            $eventPictures = $this->em->getRepository(EventPicture::class)->findAll();
        } else {
            $eventPictures = $this->em->getRepository(EventPicture::class)->findBy(['existsOnRemoteStorageAt' => null]);
        }

        $count = \count($eventPictures);
        if (0 === $count) {
            $io->success('All existing pictures have already been checked.');

            return Command::SUCCESS;
        }

        $io->progressStart($count);
        foreach ($eventPictures as $eventPicture) {
            if (!$this->imageStorageManipulator->exists($eventPicture)) {
                // If the picture have been recently created, it might by still
                // uploading so we don't delete it immediately.
                if ($eventPicture->getCreatedAt() > new \DateTimeImmutable('-30 minutes')) {
                    continue;
                }

                $this->em->remove($eventPicture);
            } else {
                $eventPicture->existsOnRemoteStorage();
                $this->em->persist($eventPicture);
            }

            $io->progressAdvance();
        }

        $io->progressFinish();

        $this->em->flush();

        $io->success('All new pictures have been checked.');

        return Command::SUCCESS;
    }
}
