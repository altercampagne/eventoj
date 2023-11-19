<?php

declare(strict_types=1);

namespace App\Command;

use App\Email\EmailConfirmationSender;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\When;

#[AsCommand(
    name: 'altercampagne:send-mail',
    description: 'This command is useful to send emails for debug purpose.',
)]
#[When(env: 'dev')]
class SendMailCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EmailConfirmationSender $emailConfirmationSender,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'The email to send. It null, all mails will be sent.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ?string $email */
        $email = $input->getArgument('email');

        $io = new SymfonyStyle($input, $output);

        match ($email) {
            'email_confirmation' => $this->sendEmailConfirmation(),
            null => $this->sendAllMails(),
            default => throw new \InvalidArgumentException("Unknown mail \"$email\"."),
        };

        $io->success(null !== $email ? "Mail \"$email\" sent!" : 'Mails sent!');

        return Command::SUCCESS;
    }

    private function sendAllMails(): void
    {
        $this->sendEmailConfirmation();
    }

    private function sendEmailConfirmation(): void
    {
        if (null === $user = $this->entityManager->getRepository(User::class)->findOneByEmail('admin@altercampagne.net')) {
            throw new \RuntimeException('No user found. Did you correctly load fixtures?');
        }

        $this->emailConfirmationSender->send($user);
    }
}
