<?php

declare(strict_types=1);

namespace App\Controller\Registration;

use App\Entity\Registration;
use App\Service\Payment\PaymentRefundHandler;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/registration/{id}/cancel', name: 'registration_cancel', methods: 'POST')]
class CancelController extends AbstractController
{
    public function __construct(
        private readonly PaymentRefundHandler $paymentRefundHandler,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(Registration $registration): Response
    {
        if (!$registration->canBeCanceled()) {
            throw $this->createNotFoundException();
        }

        if (null === $payment = $registration->getApprovedPayment()) {
            throw new \RuntimeException('Cannot cancel a registration without an approved payment!');
        }

        try {
            $this->paymentRefundHandler->refund($payment);

            $this->addFlash('warning', 'Ton inscription a bien été annulée. On espère te voir à un autre évènement !');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Une erreur est survenue lors de l\'annulation de ton inscription. Si le problème persiste, n\'hésite pas à nous contacter.');

            $this->logger->error('An error occurred when canceling registration.', [
                'registration' => $registration,
                'payment' => $payment,
                'exception' => $e,
            ]);
        }

        return $this->redirectToRoute('profile_registrations');
    }
}
