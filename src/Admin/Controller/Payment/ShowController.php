<?php

declare(strict_types=1);

namespace App\Admin\Controller\Payment;

use App\Admin\Security\Permission;
use App\Entity\Payment;
use App\Service\Payment\PaymentHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::PAYMENT_VIEW->value, 'payment')]
#[Route('/payments/{id}', name: 'admin_payment_show', requirements: ['id' => Requirement::UUID])]
class ShowController extends AbstractController
{
    public function __construct(
        private readonly PaymentHandler $paymentHandler,
    ) {
    }

    public function __invoke(Payment $payment): Response
    {
        if (null !== $payment->getHelloassoCheckoutIntentId() && $this->isGranted(Permission::DEBUG->value, $payment)) {
            $checkoutIntent = $this->paymentHandler->getCheckoutIntent($payment);
        }

        return $this->render('admin/payment/show.html.twig', [
            'payment' => $payment,
            'checkoutIntent' => $checkoutIntent ?? null,
        ]);
    }
}
