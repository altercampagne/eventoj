<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum StageType: string implements TranslatableInterface
{
    case BEFORE = 'before';
    case AFTER = 'after';
    case CLASSIC = 'classic';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::BEFORE => 'Before',
            self::AFTER => 'After',
            self::CLASSIC => 'Classique',
        };
    }
}
