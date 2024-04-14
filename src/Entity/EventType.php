<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum EventType: string implements TranslatableInterface
{
    case AT = 'AT';
    case ADT = 'ADT';
    case BT = 'BT';
    case EB = 'EB';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::AT => 'AlterTour',
            self::ADT => 'Alter-D-Tour',
            self::BT => 'BièreTour',
            self::EB => 'Échappée Belle',
        };
    }

    public function getFAQQuestionSlug(): string
    {
        return match ($this) {
            self::AT => 'quest-ce-quun-altertour',
            self::EB => 'quest-ce-quune-echappe-belle',
            self::ADT => 'quest-ce-quun-alter-d-tour',
            self::BT => 'quest-ce-quun-bieretour',
        };
    }
}
