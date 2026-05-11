<?php

declare(strict_types=1);

namespace App\Service\Registration;

use App\Entity\Registration;
use App\Service\Payment\PaymentRefundHandler;
use Doctrine\ORM\EntityManagerInterface;

readonly class RegistrationCanceller
{
    public function __construct(
        private PaymentRefundHandler $paymentRefundHandler,
        private EntityManagerInterface $em,
    ) {
    }

    public function cancel(Registration $registration, bool $cancelledByAdmin = false): RegistrationCancellationResult
    {
        if (!$registration->canBeCanceled()) {
            throw new \InvalidArgumentException('Given registration cannot be canceled');
        }

        $refundedPayments = [];
        foreach ($registration->getPayments() as $payment) {
            if ($payment->isApproved()) {
                $this->paymentRefundHandler->refund($payment, cancelRegistrationIfExists: false, cancelledByAdmin: $cancelledByAdmin);
                $refundedPayments[] = $payment;
            }
        }

        $registration->cancel();

        $this->em->persist($registration);
        $this->em->flush();

        return new RegistrationCancellationResult(refundedPayments: $refundedPayments);
    }
}
