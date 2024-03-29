<?php

declare(strict_types=1);

namespace App\Controller\Payment;

use App\Bridge\Helloasso\PaymentReturnType;
use App\Entity\Payment;
use App\Entity\User;
use App\Service\Payment\RegistrationPaymentHandler;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/payment/{id}/post_payment/back', name: 'payment_callback_back', defaults: ['type' => 'back'], requirements: ['id' => Requirement::UUID_V4])]
#[Route('/payment/{id}/post_payment/error', name: 'payment_callback_error', defaults: ['type' => 'error'], requirements: ['id' => Requirement::UUID_V4])]
#[Route('/payment/{id}/post_payment/return', name: 'payment_callback_return', defaults: ['type' => 'return'], requirements: ['id' => Requirement::UUID_V4])]
class PaymentCallbackController extends AbstractController
{
    public function __construct(
        private readonly RegistrationPaymentHandler $registrationPaymentHandler,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request, Payment $payment, PaymentReturnType $type): Response
    {
        /** @var User $payer */
        $payer = $this->getUser();

        if ($payment->getPayer() !== $payer) {
            throw $this->createNotFoundException('Current user is not the payer of the given payment.');
        }

        if (PaymentReturnType::Back === $type) {
            return $this->fail($payment);
        }

        if (PaymentReturnType::Error === $type) {
            $this->logger->error('An error occurred during payment.', [
                'payment' => $payment,
                'error' => $request->query->getString('error', 'No error found in query!'),
            ]);

            return $this->fail($payment);
        }

        if ('succeeded' !== $code = $request->query->getString('code')) {
            $this->logger->error('Payment have been refused.', [
                'payment' => $payment,
                'code' => $code,
            ]);

            $this->addFlash('danger', 'Le paiement a été refusé et ton inscription n\'a pas pu être validée.');

            return $this->fail($payment);
        }

        $this->registrationPaymentHandler->handlePaymentSuccess($payment);

        $this->addFlash('success', 'Ta participation a bien été enregistrée ! 🥳');

        return $this->redirectToRoute('profile_registrations');
    }

    private function fail(Payment $payment): RedirectResponse
    {
        $this->registrationPaymentHandler->handlePaymentFailure($payment);

        return $this->redirectToRoute('event_register', [
            'slug' => $payment->getRegistration()->getEvent()->getSlug(),
        ]);
    }
}
