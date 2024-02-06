<?php

declare(strict_types=1);

namespace App\Command;

use App\Email\EventReminderSender;
use App\Entity\RegistrationStatus;
use App\Entity\Stage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'eventoj:reminder',
    description: 'This command send a reminder before arrival of participants to give them latest informations.',
)]
class ReminderCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventReminderSender $eventReminderSender,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $stages = $this->getEligibleStagesForReminder();

        $sentMail = 0;
        foreach ($stages as $stage) {
            foreach ($stage->getConfirmedStagesRegistrations() as $stageRegistration) {
                $this->eventReminderSender->send($stageRegistration->getRegistration());
                ++$sentMail;
            }
        }

        $io->success("$sentMail reminder emails have been sent!");

        return Command::SUCCESS;
    }

    /**
     * @return Stage[]
     */
    private function getEligibleStagesForReminder(): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('s')
            ->from(Stage::class, 's')
            ->innerJoin('s.stagesRegistrations', 'sr')
            ->innerJoin('sr.registration', 'r')
            ->andWhere('s.date = :start_date')
            ->andWhere('r.status = :status')
            ->setParameter('start_date', new \DateTimeImmutable('+170 days'))
            ->setParameter('status', RegistrationStatus::CONFIRMED)
        ;

        return $qb->getQuery()->getResult();
    }
}
