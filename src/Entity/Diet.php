<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum Diet: string implements TranslatableInterface
{
    case OMNIVORE = 'omnivore';
    case VEGETARIAN = 'vegetarian';
    case VEGAN = 'vegan';

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        return match ($this) {
            self::OMNIVORE => 'Omnivore',
            self::VEGETARIAN => 'Végétarien',
            self::VEGAN => 'Végétalien',
        };
    }
}
