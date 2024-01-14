<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Registration;
use App\Entity\User;
use App\Enum\HelloassoPaymentReturnType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/registration/{id}/post_payment/back', name: 'event_registration_post_payment_back', defaults: ['type' => 'back'])]
#[Route('/registration/{id}/post_payment/error', name: 'event_registration_post_payment_error', defaults: ['type' => 'error'])]
#[Route('/registration/{id}/post_payment/return', name: 'event_registration_post_payment_return', defaults: ['type' => 'return'])]
class PostPaymentController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request, Registration $registration, HelloassoPaymentReturnType $type): Response
    {
        /** @var User $payer */
        $payer = $this->getUser();

        if ($registration->getUser() !== $payer) {
            throw $this->createNotFoundException('Current user is not the owner of the given reservation.');
        }

        if (HelloassoPaymentReturnType::Back === $type) {
            return $this->cancel($registration);
        }

        if (HelloassoPaymentReturnType::Error === $type) {
            $this->logger->error('An error occurred during payment.', [
                'registration' => $registration,
                'error' => $request->query->getString('error', 'No error found in query!'),
            ]);

            return $this->cancel($registration);
        }

        if ('succeeded' !== $code = $request->query->getString('code')) {
            $this->logger->error('Payment have been refused.', [
                'registration' => $registration,
                'code' => $code,
            ]);

            $this->addFlash('error', 'Le paiement a été refusé et ton inscription n\'a pas pu être validée.');

            return $this->cancel($registration);
        }

        // TODO: Call Helloasso API to ensure the Checkout intent linked to the registration really is paid.

        $registration->confirm();

        $this->em->persist($registration);
        $this->em->flush();

        return $this->redirectToRoute('event_registration_payment_successful', ['id' => $registration->getId()]);
    }

    private function cancel(Registration $registration): RedirectResponse
    {
        $registration->cancel();

        $this->em->persist($registration);
        $this->em->flush();

        return $this->redirectToRoute('event_register', [
            'slug' => $registration->getEvent()->getSlug(),
            'id' => $registration->getId(),
        ]);
    }
}
