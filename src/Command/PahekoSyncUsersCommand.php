<?php

declare(strict_types=1);

namespace App\Command;

use App\Bridge\Paheko\UserSynchronizer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'eventoj:paheko:sync:users',
    description: 'Sync users from the Database with Paheko',
)]
class PahekoSyncUsersCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserSynchronizer $userSynchronizer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'The email to send. If null, all mails will be sent.')
        ;
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->em->getRepository(User::class)->findAll();

        $io->progressStart(\count($users));

        foreach ($users as $user) {
            $this->userSynchronizer->sync($user);
            $io->progressAdvance();
        }

        $io->progressFinish();
        $io->success("All users have been sync'ed on Paheko!");

        return Command::SUCCESS;
    }
}
