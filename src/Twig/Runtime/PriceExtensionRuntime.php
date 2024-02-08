<?php

declare(strict_types=1);

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use Twig\Extra\Intl\IntlExtension;

class PriceExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly IntlExtension $intlExtension,
    ) {
    }

    public function formatPrice(int $amount): string
    {
        return $this->intlExtension->formatCurrency($amount / 100, 'EUR', [
            'fraction_digit' => 0,
        ]);
    }
}
