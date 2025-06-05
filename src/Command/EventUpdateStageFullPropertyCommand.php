<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'eventoj:event:update-stage-full-property',
    description: 'Update the "is_full" property for all stages',
)]
class EventUpdateStageFullPropertyCommand
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $events = $this->em->getRepository(Event::class)->findAll();
        foreach ($events as $event) {
            foreach ($event->getStages() as $stage) {
                $stage->updateIsFullProperty();
            }

            $this->em->persist($event);
        }

        $this->em->flush();

        $io->success('All events have been updated!');

        return Command::SUCCESS;
    }
}
