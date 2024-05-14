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
    name: 'eventoj:event:update-booked-seats',
    description: 'Update seats availability for all stages',
)]
class EventUpdateBookedSeatsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $events = $this->em->getRepository(Event::class)->findAll();
        foreach ($events as $event) {
            $event->updateBookedSeats();

            $this->em->persist($event);
        }
        $this->em->flush();

        $io->success('All events have been updated!');

        return Command::SUCCESS;
    }
}
