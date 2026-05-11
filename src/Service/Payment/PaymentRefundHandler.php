<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Payment;
use App\Service\Paheko\PaymentSynchronizer;
use Doctrine\ORM\EntityManagerInterface;
use Helloasso\Enums\PaymentState;
use Helloasso\HelloassoClient;
use Helloasso\Models\Statistics\OrderPayment;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class PaymentRefundHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private HelloassoClient $helloassoClient,
        private PaymentSynchronizer $pahekoPaymentSynchronizer,
        private LoggerInterface $logger,
        #[Autowire(param: 'kernel.environment')]
        private string $environment,
    ) {
    }

    public function refund(Payment $payment, bool $cancelRegistrationIfExists = true, bool $cancelledByAdmin = false): void
    {
        $this->refundOnHelloasso($payment, $cancelledByAdmin);

        $payment->refund();
        $this->em->persist($payment);

        if ($cancelRegistrationIfExists) {
            if (null !== $registration = $payment->getRegistration()) {
                $registration->cancel();
                $this->em->persist($registration);
            }
        }

        foreach ($payment->getMemberships() as $membership) {
            $membership->cancel();
            $this->em->persist($membership);
        }

        $this->em->flush();

        $this->pahekoPaymentSynchronizer->sync($payment);
    }

    private function refundOnHelloasso(Payment $payment, bool $cancelledByAdmin): void
    {
        if ('test' === $this->environment) {
            $this->logger->notice('Refund asked but in test env, we do not call Helloasso.', [
                'payment' => $payment,
            ]);

            return;
        }

        $checkoutIntent = $this->helloassoClient->checkout->retrieve((int) $payment->getHelloassoCheckoutIntentId());
        if (null === $order = $checkoutIntent->getOrder()) {
            $this->logger->debug('Found a payment which is validated in DB but which do not have an order on Helloasso!', [
                'payment' => $payment,
            ]);

            throw new \RuntimeException('Given payment does not have an order associated on HelloAsso.');
        }

        $refundablePayments = array_filter($order->getPayments(), static fn (OrderPayment $orderPayment): bool => PaymentState::Authorized === $orderPayment->getState());

        if ([] === $refundablePayments) {
            $this->logger->info('No refundable payment found on HelloAsso when refunding a DB payment.', [
                'payment' => $payment,
                'checkout_intent_id' => $checkoutIntent->getId(),
            ]);

            return;
        }

        foreach ($refundablePayments as $refundablePayment) {
            $comment = $cancelledByAdmin
                ? "Remboursement automatique suite à l'annulation de la participation par un·e admin."
                : "Remboursement automatique suite à l'annulation de la participation par la personne concernée.";

            $this->helloassoClient->payment->refund($refundablePayment->getId(), [
                'comment' => $comment,
                'cancelOrder' => 'true', // Mandatory to avoid any future payment
            ]);
        }
    }
}
