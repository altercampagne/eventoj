<?php

declare(strict_types=1);

namespace App\Controller\Registration;

use App\Entity\Registration;
use App\Service\Payment\PaymentRefundHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/registration/{id}/cancel', name: 'registration_cancel', methods: 'POST')]
class CancelController extends AbstractController
{
    public function __construct(
        private readonly PaymentRefundHandler $paymentRefundHandler,
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

        $this->paymentRefundHandler->refundRegistrationOnly($payment);

        $this->addFlash('warning', 'Ton inscription a bien été annulée. On espère te voir à un autre évènement !');

        return $this->redirectToRoute('profile_registrations');
    }
}
