<?php

declare(strict_types=1);

namespace App\Service\PriceCalculation;

final class Bill
{
    /**
     * @var array<string, int>
     */
    private array $lines = [];

    private int $amount = 0;

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function addLine(string $label, int $amount): void
    {
        $this->lines[$label] = $amount;
        $this->amount += $amount;
    }

    /**
     * @return array<string, int>
     */
    public function getLines(): array
    {
        return $this->lines;
    }
}
