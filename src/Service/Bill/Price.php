<?php

declare(strict_types=1);

namespace App\Service\Bill;

final readonly class Price
{
    public int $minimumAmountPerDay;
    public int $breakEvenAmountPerDay;
    public int $supportAmountPerDay;

    private function __construct(
        public int $minimumAmount,
        public int $breakEvenAmount,
        public int $supportAmount,
        public int $days,
    ) {
        $this->minimumAmountPerDay = (int) round($minimumAmount / $days);
        $this->breakEvenAmountPerDay = (int) round($breakEvenAmount / $days);
        $this->supportAmountPerDay = (int) round($supportAmount / $days);
    }

    public static function fixed(int $amount, int $days): self
    {
        return new self($amount, $amount, $amount, $days);
    }

    public static function adjustable(int $minimumAmount, int $breakEvenAmount, int $supportAmount, int $days): self
    {
        return new self($minimumAmount, $breakEvenAmount, $supportAmount, $days);
    }
}
