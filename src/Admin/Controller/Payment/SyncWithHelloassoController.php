<?php

declare(strict_types=1);

namespace App\Admin\Controller\Payment;

use App\Admin\Security\Permission;
use App\Entity\Payment;
use App\Service\Helloasso\PaymentSynchronizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::PAYMENT_SYNC_WITH_HELLOASSO->value, 'payment')]
#[Route('/_admin/payments/{id}/sync_with_helloasso', name: 'admin_payment_sync_with_helloasso', requirements: ['id' => Requirement::UUID], methods: 'POST')]
class SyncWithHelloassoController extends AbstractController
{
    public function __construct(
        private readonly PaymentSynchronizer $paymentSynchronizer,
    ) {
    }

    public function __invoke(Payment $payment): Response
    {
        $syncReport = $this->paymentSynchronizer->sync($payment);

        $level = $syncReport->nothingHasBeenDone() ? 'success' : ($syncReport->isWarning() ? 'danger' : 'warning');

        $this->addFlash($level, $syncReport->message);

        return $this->redirectToRoute('admin_payment_show', ['id' => $payment->getId()]);
    }
}
