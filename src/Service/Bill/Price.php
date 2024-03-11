<?php

declare(strict_types=1);

namespace App\Service\Bill;

final readonly class Price
{
    private function __construct(
        public int $minimumAmount,
        public int $breakEvenAmount,
        public int $supportAmount,
    ) {
    }

    public static function fixed(int $amount): self
    {
        return new self($amount, $amount, $amount);
    }

    public static function adjustable(int $minimumAmount, int $breakEvenAmount, int $supportAmount): self
    {
        return new self($minimumAmount, $breakEvenAmount, $supportAmount);
    }
}
