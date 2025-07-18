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
    name: 'eventoj:event-picture:delete-if-missing',
    description: 'Delete event picture which does not exists on remote storage.',
)]
final readonly class EventPictureDeleteIfMissingCommand
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

        $io->progressStart(\count($eventPictures));
        foreach ($eventPictures as $eventPicture) {
            if (!$this->imageStorageManipulator->exists($eventPicture)) {
                $this->em->remove($eventPicture);
            }

            $io->progressAdvance();
        }

        $io->progressFinish();

        $this->em->flush();

        $io->success('All unexisting pictures have been removed.');

        return Command::SUCCESS;
    }
}
