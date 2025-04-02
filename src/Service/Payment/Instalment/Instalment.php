<?php

declare(strict_types=1);

namespace App\Service\Payment\Instalment;

final readonly class Instalment
{
    public function __construct(
        public \DateTimeImmutable $date,
        public int $amount,
    ) {
    }
}
