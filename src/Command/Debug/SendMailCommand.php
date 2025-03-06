<?php

declare(strict_types=1);

namespace App\Command\Debug;

use App\Email\EmailConfirmationSender;
use App\Email\EventReminderSender;
use App\Email\PasswordResetSender;
use App\Entity\Registration;
use App\Entity\ResetPasswordRequest;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\When;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[AsCommand(
    name: 'eventoj:debug:send-mail',
    description: 'This command is useful to send emails for debug purpose.',
)]
#[When(env: 'dev')]
#[When(env: 'test')]
class SendMailCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly EmailConfirmationSender $emailConfirmationSender,
        private readonly PasswordResetSender $passwordResetSender,
        private readonly EventReminderSender $eventReminderSender,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'The email to send. If null, all mails will be sent.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ?string $email */
        $email = $input->getArgument('email');

        $io = new SymfonyStyle($input, $output);

        match ($email) {
            'email_confirmation' => $this->sendEmailConfirmation(),
            'password_reset' => $this->sendPasswordReset(),
            'event_reminder' => $this->sendEventReminder(),
            null => $this->sendAllMails(),
            default => throw new \InvalidArgumentException("Unknown mail \"{$email}\"."),
        };

        $io->success(null !== $email ? "Mail \"{$email}\" sent!" : 'Mails sent!');

        return Command::SUCCESS;
    }

    private function sendAllMails(): void
    {
        $this->sendEmailConfirmation();
        $this->sendPasswordReset();
        $this->sendEventReminder();
    }

    private function sendEmailConfirmation(): void
    {
        if (null === $user = $this->em->getRepository(User::class)->findOneByEmail('admin@altercampagne.net')) {
            throw new \RuntimeException('No user found. Did you correctly load fixtures?');
        }

        $this->emailConfirmationSender->send($user);
    }

    private function sendPasswordReset(): void
    {
        if (null === $user = $this->em->getRepository(User::class)->findOneByEmail('admin@altercampagne.net')) {
            throw new \RuntimeException('No user found. Did you correctly load fixtures?');
        }

        $passwordResetRequests = $this->em->getRepository(ResetPasswordRequest::class)->findBy(['user' => $user]);
        foreach ($passwordResetRequests as $request) {
            $this->em->remove($request);
        }

        $this->em->flush();

        $resetToken = $this->resetPasswordHelper->generateResetToken($user);

        $this->passwordResetSender->send($user, $resetToken);
    }

    private function sendEventReminder(): void
    {
        $this->eventReminderSender->send($this->em->getRepository(Registration::class)->findAll()[0]);
    }
}
