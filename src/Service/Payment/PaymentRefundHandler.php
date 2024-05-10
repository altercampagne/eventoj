<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Payment;
use App\Service\Paheko\PaymentSynchronizer;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PaymentRefundHandler
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PaymentSynchronizer $paymentSynchronizer,
    ) {
    }

    public function fullRefund(Payment $payment): void
    {
        if (null !== $registration = $payment->getRegistration()) {
            if (!$registration->isCanceled()) {
                $registration->cancel();
                $this->em->persist($registration);
            }
        }

        foreach ($payment->getMemberships() as $membership) {
            if ($membership->isCanceled()) {
                continue;
            }

            $membership->cancel();
            $this->em->persist($membership);
        }

        $payment->refund();

        $this->em->persist($payment);
        $this->em->flush();

        $this->paymentSynchronizer->sync($payment);
    }

    public function refundRegistrationOnly(Payment $payment): void
    {
        if (null === $registration = $payment->getRegistration()) {
            throw new \RuntimeException('Cannot partially refund a payment without registration.');
        }

        $amountToRefund = $payment->getRegistrationOnlyAmount();

        $payment->refund($amountToRefund);
        $registration->cancel();

        $this->em->persist($payment);
        $this->em->persist($registration);
        $this->em->flush();

        $this->paymentSynchronizer->sync($payment);
    }
}
