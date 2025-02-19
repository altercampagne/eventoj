<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Twig\Runtime\PriceExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PriceExtension extends AbstractExtension
{
    #[\Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_price', [PriceExtensionRuntime::class, 'formatPrice']),
        ];
    }
}
