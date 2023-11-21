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
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
    ) {}

    public function send(User $user): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'security_verify_email',
            (string) $user->id,
            $user->email,
            ['id' => (string) $user->id]
        );

        $email = (new TemplatedEmail())
            ->from(new Address('contact@altercampagne.net', 'Altercampagne'))
            ->to(new Address($user->email, $user->name))
            ->subject('Merci de confirmer ton adresse mail.')
            ->htmlTemplate('emails/email_confirmation.html.twig')
            ->context([
                'user' => $user,
                'signedUrl' => $signatureComponents->getSignedUrl(),
                'expiresAtMessageKey' => $signatureComponents->getExpirationMessageKey(),
                'expiresAtMessageData' => $signatureComponents->getExpirationMessageData(),
            ])
        ;

        $this->mailer->send($email);
    }
}
