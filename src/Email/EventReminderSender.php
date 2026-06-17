<?php

declare(strict_types=1);

namespace App\Email;

use App\Entity\Registration;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EventReminderSender
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UrlGeneratorInterface $urlGenerator,
        #[Autowire(env: 'ASSOCIATION_PHONE_NUMBER')]
        private readonly string $defaultPhoneNumber,
    ) {
    }

    public function send(Registration $registration): void
    {
        $event = $registration->getEvent();

        $email = new TemplatedEmail()
            ->from(new Address('contact@altercampagne.net', 'Altercampagne'))
            ->to(new Address($registration->getUser()->getEmail(), $registration->getUser()->getFullName()))
            ->subject('C\'est bientôt l\'heure du départ ! 🥳🚲')
            ->htmlTemplate('emails/event_reminder.html.twig')
            ->context([
                'member_display_name' => $registration->getUser()->getPublicName(),
                'event_name' => $event->getName(),
                'event_url' => $this->urlGenerator->generate('event_show', [
                    'slug' => $event->getSlug(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'association_phone_number' => $event->getPhoneNumber() ?? $this->defaultPhoneNumber,
            ])
        ;

        $this->mailer->send($email);
    }
}
