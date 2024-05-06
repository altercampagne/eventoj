<?php

declare(strict_types=1);

namespace App\Admin\Controller\Payment;

use App\Admin\Security\Permission;
use App\Entity\Payment;
use App\Service\Payment\PaymentHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::PAYMENT_SYNC_WITH_HELLOASSO->value, 'payment')]
#[Route('/payments/{id}/sync_with_helloasso', name: 'admin_payment_sync_with_helloasso', requirements: ['id' => Requirement::UUID_V4], methods: 'POST')]
class SyncWithHelloassoController extends AbstractController
{
    public function __construct(
        private readonly PaymentHandler $paymentHandler,
    ) {
    }

    public function __invoke(Payment $payment): Response
    {
        if (!$this->paymentHandler->isPaymentSuccessful($payment)) {
            $this->addFlash('info', 'Le paiement n\'est pas accepté chez Helloasso, cas non implémenté pour le moment.');

            return $this->redirectToRoute('admin_payment_show', ['id' => $payment->getId()]);
        }

        if ($payment->isApproved()) {
            $this->addFlash('success', 'Le paiement est acecpté des deux côtés !');

            return $this->redirectToRoute('admin_payment_show', ['id' => $payment->getId()]);
        }

        $this->paymentHandler->handlePaymentSuccess($payment);

        $this->addFlash('warning', 'Le paiement n\'était pas approuvé chez nous mais maintenant, c\'est bien le cas ! :ok_hand:');

        return $this->redirectToRoute('admin_payment_show', ['id' => $payment->getId()]);
    }
}
