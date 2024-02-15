<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum StageDifficulty: string implements TranslatableInterface
{
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::EASY => 'Facile',
            self::MEDIUM => 'Moyenne',
            self::HARD => 'Difficile',
        };
    }
}
