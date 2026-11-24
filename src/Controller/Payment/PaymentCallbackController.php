<?php

declare(strict_types=1);

namespace App\Controller\Payment;

use App\Bridge\Helloasso\PaymentReturnType;
use App\Entity\Payment;
use App\Entity\User;
use App\Service\Payment\PaymentHandler;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/payment/{id}/post_payment/back', name: 'payment_callback_back', requirements: ['id' => Requirement::UUID], defaults: ['type' => 'back'])]
#[Route('/payment/{id}/post_payment/error', name: 'payment_callback_error', requirements: ['id' => Requirement::UUID], defaults: ['type' => 'error'])]
#[Route('/payment/{id}/post_payment/return', name: 'payment_callback_return', requirements: ['id' => Requirement::UUID], defaults: ['type' => 'return'])]
class PaymentCallbackController extends AbstractController
{
    public function __construct(
        private readonly PaymentHandler $paymentHandler,
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
            $this->logger->info('Payment have been refused.', [
                'payment' => $payment,
                'code' => $code,
            ]);

            $this->addFlash('danger', "Le paiement a été refusé et ton inscription n'a pas pu être validée.");

            return $this->fail($payment);
        }

        if (!($order = $this->paymentHandler->getCheckoutIntent($payment)->getOrder()) instanceof \Helloasso\Models\Statistics\OrderDetail) {
            $this->addFlash('danger', "Impossible de vérifier que ton paiement est bien passé. Si tu es certain·e que c'est bien le cas, contacte notre équipe avec ton nom / prénom / dates de réservation et en leur copiant ce message.");

            return $this->fail($payment);
        }

        $this->paymentHandler->handlePaymentSuccess($payment, $order);

        if ($payment->getRegistration() instanceof \App\Entity\Registration) {
            $this->addFlash('success', 'Ta participation a bien été enregistrée ! 🥳');

            return $this->redirectToRoute('profile_registrations');
        }

        $this->addFlash('success', 'Ton adhésion a bien été réglée ! 🥳');

        return $this->redirectToRoute('profile_memberships');
    }

    private function fail(Payment $payment): RedirectResponse
    {
        if (!($registration = $payment->getRegistration()) instanceof \App\Entity\Registration) {
            return $this->redirectToRoute('profile_memberships');
        }

        $this->paymentHandler->handlePaymentFailure($payment);

        return $this->redirectToRoute('event_register', [
            'slug' => $registration->getEvent()->getSlug(),
        ]);
    }
}
