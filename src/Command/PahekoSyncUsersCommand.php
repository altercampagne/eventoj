<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Service\Paheko\UserSynchronizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'eventoj:paheko:sync:users',
    description: 'Sync users from the Database with Paheko',
)]
class PahekoSyncUsersCommand
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserSynchronizer $userSynchronizer,
    ) {
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Argument(description: "The email of the user to sync. If null, all users will be sync'ed.")]
        ?string $email = null,
    ): int {
        $io = new SymfonyStyle($input, $output);
        if (null !== $email) {
            if (null === $user = $this->em->getRepository(User::class)->findOneByEmail($email)) {
                throw new \InvalidArgumentException("User with email {$email} not found!");
            }

            $this->userSynchronizer->sync($user);

            $io->success("User have been sync'ed on Paheko!");

            return Command::SUCCESS;
        }

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
