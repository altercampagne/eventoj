<?php

declare(strict_types=1);

namespace App\Twig\Runtime;

use App\Service\PriceFormatter;
use Twig\Extension\RuntimeExtensionInterface;

class PriceExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly PriceFormatter $priceFormatter,
    ) {
    }

    public function formatPrice(int|float $amount): string
    {
        return $this->priceFormatter->format((int) round($amount, 0));
    }
}
