<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Service\PriceFormatter;
use Twig\Attribute\AsTwigFilter;

class PriceExtension
{
    public function __construct(
        private readonly PriceFormatter $priceFormatter,
    ) {
    }

    #[AsTwigFilter('format_price')]
    public function formatPrice(int|float $amount): string
    {
        return $this->priceFormatter->format((int) round($amount, 0));
    }
}
