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

    public function isBefore(Meal $meal): bool
    {
        return match ($this) {
            self::BREAKFAST => Meal::BREAKFAST !== $meal,
            self::LUNCH => Meal::DINNER === $meal,
            self::DINNER => false,
        };
    }

    public function isAfter(Meal $meal): bool
    {
        return match ($this) {
            self::BREAKFAST => false,
            self::LUNCH => Meal::BREAKFAST === $meal,
            self::DINNER => Meal::DINNER !== $meal,
        };
    }
}
