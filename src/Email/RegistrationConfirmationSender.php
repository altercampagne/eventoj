<?php

declare(strict_types=1);

namespace App\Email;

use App\Entity\Registration;
use App\Repository\QuestionRepository;
use App\Service\Media\UploadedImageUrlGenerator;
use App\Service\PriceFormatter;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class RegistrationConfirmationSender
{
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator,
        private UploadedImageUrlGenerator $uploadedImageUrlGenerator,
        private PriceFormatter $priceFormatter,
        private TranslatorInterface $translator,
        private QuestionRepository $questionRepository,
    ) {
    }

    public function send(Registration $registration): void
    {
        if (!$registration->isConfirmed()) {
            throw new \RuntimeException('Cannot send a confirmation email for a registration which is not confirmed!');
        }

        $user = $registration->getUser();
        $event = $registration->getEvent();

        if (null === $faqCancellation = $this->questionRepository->findOneBySlug('est-ce-que-je-peux-annuler-ma-participation')) {
            throw new \RuntimeException('Question "est-ce-que-je-peux-annuler-ma-participation" is not found.');
        }

        $dateFormatter = new \IntlDateFormatter('fr_FR', dateType: \IntlDateFormatter::LONG, timeType: \IntlDateFormatter::NONE);

        $email = (new TemplatedEmail())
            ->from(new Address('contact@altercampagne.net', 'Altercampagne'))
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->subject('Ton inscription est validée ! 🥳')
            ->htmlTemplate('emails/registration_confirmation.html.twig')
            ->context([
                'member_display_name' => $user->getPublicName(),
                'event_name' => $event->getName(),
                /* @phpstan-ignore argument.type */
                'event_start_date' => $dateFormatter->format($event->getFirstStage()?->getDate()),
                /* @phpstan-ignore argument.type */
                'event_end_date' => $dateFormatter->format($event->getLastStage()?->getDate()),
                'event_image_url' => $this->uploadedImageUrlGenerator->getImageUrl($event->getPicture(), width: 150, height: 150),
                'registration_url' => $this->urlGenerator->generate('profile_registrations', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'registration_price' => $this->priceFormatter->format($registration->getPrice()),
                'registration_cancelation_date' => $dateFormatter->format($registration->getCancelationDate()),
                /* @phpstan-ignore argument.type */
                'arrival_date' => $dateFormatter->format($registration->getStartAt()),
                'arrival_first_meal' => $registration->getFirstMeal()->trans($this->translator),
                /* @phpstan-ignore argument.type */
                'departure_date' => $dateFormatter->format($registration->getEndAt()),
                'departure_last_meal' => $registration->getLastMeal()->trans($this->translator),
                'faq_cancellation' => $faqCancellation->getAnswer(),
            ])
        ;

        $this->mailer->send($email);
    }
}
