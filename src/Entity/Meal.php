<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum Meal: string implements TranslatableInterface
{
    case BREAKFAST = 'breakfast';
    case LUNCH = 'lunch';
    case DINNER = 'dinner';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::BREAKFAST => 'Petit-déjeuner',
            self::LUNCH => 'Déjeuner',
            self::DINNER => 'Diner',
        };
    }
}
