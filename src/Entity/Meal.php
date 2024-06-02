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
        return $this->compare($meal) < 0;
    }

    public function isAfter(Meal $meal): bool
    {
        return $this->compare($meal) > 0;
    }

    public function compare(Meal $meal): int
    {
        if ($this === $meal) {
            return 0;
        }

        return match ($this) {
            self::BREAKFAST => -1,
            self::LUNCH => Meal::BREAKFAST === $meal ? 1 : -1,
            self::DINNER => 1,
        };
    }

    /**
     * @return Meal[]
     */
    public function getPreviousMeals(bool $includeSelf = false): array
    {
        $meals = match ($this) {
            self::BREAKFAST => [],
            self::LUNCH => [Meal::BREAKFAST],
            self::DINNER => [Meal::BREAKFAST, Meal::LUNCH],
        };

        if ($includeSelf) {
            $meals[] = $this;
        }

        return $meals;
    }

    /**
     * @return Meal[]
     */
    public function getFollowingMeals(bool $includeSelf = false): array
    {
        $meals = $includeSelf ? [$this] : [];

        return match ($this) {
            self::BREAKFAST => array_merge($meals, [Meal::LUNCH, Meal::DINNER]),
            self::LUNCH => array_merge($meals, [Meal::DINNER]),
            self::DINNER => $meals,
        };
    }
}
