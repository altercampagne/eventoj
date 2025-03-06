<?php

declare(strict_types=1);

namespace App\Admin\Controller\Payment;

use App\Admin\Security\Permission;
use App\Entity\Payment;
use App\Service\Payment\PaymentHandler;
use App\Service\Payment\PaymentRefundHandler;
use Doctrine\ORM\EntityManagerInterface;
use Helloasso\Enums\PaymentState;
use Helloasso\HelloassoClient;
use Helloasso\Models\Statistics\OrderDetail;
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
        private readonly HelloassoClient $helloassoClient,
        private readonly PaymentHandler $paymentHandler,
        private readonly PaymentRefundHandler $paymentRefundHandler,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Payment $payment): Response
    {
        if (null === $id = $payment->getHelloassoCheckoutIntentId()) {
            return $this->return('error', 'Given payment does not contains an Helloasso checkout intent ID.', $payment);
        }

        $checkoutIntent = $this->helloassoClient->checkout->retrieve((int) $id);
        if (null === $order = $checkoutIntent->getOrder()) {
            return $this->return('warning', 'Aucun paiement trouvé chez Helloasso, paiement en erreur ?', $payment);
        }

        $payment->setApprovedAt(\DateTimeImmutable::createFromMutable($order->getDate()));
        $this->em->persist($payment);
        $this->em->flush();

        if ($this->isOrderRefunded($order)) {
            if ($payment->isRefunded()) {
                return $this->return('success', 'Le paiement est remboursé chez Helloasso et chez nous !', $payment);
            }

            $this->paymentRefundHandler->fullRefund($payment);

            return $this->return('warning', 'Le paiement était remboursé chez Helloasso mais pas chez nous. C\'est maintenant corrigé !', $payment);
        }

        if ($payment->isApproved()) {
            return $this->return('success', 'Le paiement est acecpté des deux côtés !', $payment);
        }

        $this->paymentHandler->handlePaymentSuccess($payment, $order);

        return $this->return('warning', 'Le paiement n\'était pas approuvé chez nous mais maintenant, c\'est bien le cas !', $payment);
    }

    private function isOrderRefunded(OrderDetail $order): bool
    {
        foreach ($order->getPayments() as $payment) {
            if (PaymentState::Refunded === $payment->getState()) {
                return true;
            }

            if (PaymentState::Refunding === $payment->getState()) {
                return true;
            }
        }

        return false;
    }

    private function return(string $type, string $message, Payment $payment): Response
    {
        $this->addFlash($type, $message);

        return $this->redirectToRoute('admin_payment_show', ['id' => $payment->getId()]);
    }
}
