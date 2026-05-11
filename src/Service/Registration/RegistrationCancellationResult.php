<?php

declare(strict_types=1);

namespace App\Service\Registration;

use App\Entity\Payment;

final readonly class RegistrationCancellationResult
{
    public function __construct(
        /** @var list<Payment> */
        private array $refundedPayments = [],
    ) {
    }

    public function haveBeenRefunded(): bool
    {
        return [] !== $this->refundedPayments;
    }
}
