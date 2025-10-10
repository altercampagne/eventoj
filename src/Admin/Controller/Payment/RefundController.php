<?php

declare(strict_types=1);

namespace App\Admin\Controller\Payment;

use App\Admin\Security\Permission;
use App\Entity\Payment;
use App\Service\Payment\PaymentRefundHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::PAYMENT_REFUND->value, 'payment')]
#[Route('/payments/{id}/refund', name: 'admin_payment_refund', requirements: ['id' => Requirement::UUID], methods: 'POST')]
class RefundController extends AbstractController
{
    public function __construct(
        private readonly PaymentRefundHandler $paymentRefundHandler,
    ) {
    }

    public function __invoke(Payment $payment): Response
    {
        if (!$payment->isApproved()) {
            $this->addFlash('error', 'Le paiement ne peut pas être remboursé.');

            return $this->redirectToRoute('admin_payment_show', ['id' => $payment->getId()]);
        }

        $this->paymentRefundHandler->refund($payment);

        $this->addFlash('info', 'Le paiement a été remboursé avec succès !');

        return $this->redirectToRoute('admin_payment_show', ['id' => $payment->getId()]);
    }
}
