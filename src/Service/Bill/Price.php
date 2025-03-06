<?php

declare(strict_types=1);

namespace App\Service\Bill;

final readonly class Price
{
    public int $minimumAmount;

    public int $breakEvenAmount;

    public int $supportAmount;

    public int $minimumAmountPerDay;

    public int $breakEvenAmountPerDay;

    public int $supportAmountPerDay;

    private function __construct(
        float $minimumAmount,
        float $breakEvenAmount,
        float $supportAmount,
        public float $days,
    ) {
        $this->minimumAmount = (int) round($minimumAmount / 100) * 100;
        $this->breakEvenAmount = (int) round($breakEvenAmount / 100) * 100;
        $this->supportAmount = (int) round($supportAmount / 100) * 100;

        if (0 < $days) {
            $this->minimumAmountPerDay = (int) round($minimumAmount / $days);
            $this->breakEvenAmountPerDay = (int) round($breakEvenAmount / $days);
            $this->supportAmountPerDay = (int) round($supportAmount / $days);
        } else {
            $this->minimumAmountPerDay = 0;
            $this->breakEvenAmountPerDay = 0;
            $this->supportAmountPerDay = 0;
        }
    }

    public static function fixed(float $amount, float $days): self
    {
        return new self($amount, $amount, $amount, $days);
    }

    public static function adjustable(float $minimumAmount, float $breakEvenAmount, float $supportAmount, float $days): self
    {
        return new self($minimumAmount, $breakEvenAmount, $supportAmount, $days);
    }
}
