<?php

declare(strict_types=1);

namespace App\Email;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PasswordResetSender
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator,
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
                'member_display_name' => $user->getPublicName(),
                'reset_password_url' => $this->urlGenerator->generate('reset_password', [
                    'token' => $resetToken->getToken()
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ])
        ;

        $this->mailer->send($email);
    }
}
