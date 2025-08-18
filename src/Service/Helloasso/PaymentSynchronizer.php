<?php

declare(strict_types=1);

namespace App\Service\Helloasso;

use App\Entity\Payment;
use App\Service\Payment\PaymentHandler;
use App\Service\Payment\PaymentRefundHandler;
use Doctrine\ORM\EntityManagerInterface;
use Helloasso\Enums\PaymentState;
use Helloasso\HelloassoClient;
use Helloasso\Models\Statistics\OrderDetail;

final readonly class PaymentSynchronizer
{
    public function __construct(
        private HelloassoClient $helloassoClient,
        private PaymentHandler $paymentHandler,
        private PaymentRefundHandler $paymentRefundHandler,
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * Update the given payment according to Helloasso informations if needed.
     */
    public function sync(Payment $payment): PaymentSyncReport
    {
        if (null === $id = $payment->getHelloassoCheckoutIntentId()) {
            return PaymentSyncReport::nothingDone('Given payment does not contains an Helloasso checkout intent ID.');
        }

        $checkoutIntent = $this->helloassoClient->checkout->retrieve((int) $id);
        if (null === $order = $checkoutIntent->getOrder()) {
            if ($payment->isExpired()) {
                return PaymentSyncReport::nothingDone("Pas d'order chez Helloasso mais c'est normal pour un paiement expiré.");
            }

            if ($payment->isPending()) {
                $payment->expire();

                $this->em->persist($payment);
                $this->em->flush();

                return PaymentSyncReport::expired("L'order associé n'existe plus côté HelloAsso, le paiement a été marqué comme expiré.");
            }

            return PaymentSyncReport::warning("Aucun order trouvé chez Helloasso et ce n'était pas prévu, merci de contacter Babounet !");
        }

        $payment->setHelloassoOrderId((string) $order->getId());
        $payment->setApprovedAt(\DateTimeImmutable::createFromMutable($order->getDate()));

        $this->em->persist($payment);
        $this->em->flush();

        if ($this->isOrderRefunded($order)) {
            if ($payment->isRefunded()) {
                return PaymentSyncReport::nothingDone('Le paiement est remboursé chez Helloasso et chez nous !');
            }

            $this->paymentRefundHandler->refund($payment);

            return PaymentSyncReport::updated('Le paiement était remboursé chez Helloasso mais pas chez nous. C\'est maintenant corrigé !');
        }

        if ($payment->isApproved()) {
            return PaymentSyncReport::nothingDone('Le paiement est accepté des deux côtés !');
        }

        $this->paymentHandler->handlePaymentSuccess($payment, $order);

        return PaymentSyncReport::updated('Le paiement n\'était pas approuvé chez nous mais maintenant, c\'est bien le cas !');
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
}
