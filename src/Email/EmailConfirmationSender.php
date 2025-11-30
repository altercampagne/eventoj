<?php

declare(strict_types=1);

namespace App\Email;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailConfirmationSender
{
    public function __construct(
        private readonly VerifyEmailHelperInterface $verifyEmailHelper,
        private readonly MailerInterface $mailer,
    ) {
    }

    public function send(User $user): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'security_verify_email',
            (string) $user->getId(),
            $user->getEmail(),
            ['id' => (string) $user->getId()]
        );

        $email = (new TemplatedEmail())
            ->from(new Address('contact@altercampagne.net', 'Altercampagne'))
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->subject('Merci de confirmer ton adresse mail.')
            ->htmlTemplate('emails/email_confirmation.html.twig')
            ->context([
                'member_display_name' => $user->getPublicName(),
                'confirmation_url' => $signatureComponents->getSignedUrl(),
            ])
        ;

        $this->mailer->send($email);
    }
}
