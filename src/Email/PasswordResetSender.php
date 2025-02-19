<?php

declare(strict_types=1);

namespace App\Email;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

class PasswordResetSender
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    public function send(User $user, ResetPasswordToken $resetToken): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('contact@altercampagne.net', 'Altercampagne'))
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->subject('Demande de réinitialisation de mot de passe')
            ->htmlTemplate('emails/reset_password.html.twig')
            ->context([
                'user' => $user,
                'resetToken' => $resetToken,
            ])
        ;

        $this->mailer->send($email);
    }
}
