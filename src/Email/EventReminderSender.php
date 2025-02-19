<?php

declare(strict_types=1);

namespace App\Email;

use App\Entity\Registration;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EventReminderSender
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    public function send(Registration $registration): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('contact@altercampagne.net', 'Altercampagne'))
            ->to(new Address($registration->getUser()->getEmail(), $registration->getUser()->getFullName()))
            ->subject('C\'est bientôt l\'heure du départ ! 🥳🚲')
            ->htmlTemplate('emails/event_reminder.html.twig')
            ->context([
                'registration' => $registration,
            ])
        ;

        $this->mailer->send($email);
    }
}
