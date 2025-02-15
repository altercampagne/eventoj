<?php

declare(strict_types=1);

namespace App\Service;

use Twig\Extra\Intl\IntlExtension;

final readonly class PriceFormatter
{
    public function __construct(
        private readonly IntlExtension $intlExtension,
    ) {
    }

    public function format(int $amount): string
    {
        $formatedAmount = $this->intlExtension->formatCurrency($amount / 100, 'EUR', [
            'fraction_digit' => 0,
        ]);

        if (0 === $amount % 100) {
            return $formatedAmount;
        }

        return '~'.$formatedAmount;
    }
}
