<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum PaymentStatus: string implements TranslatableInterface
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
    case EXPIRED = 'expired';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::APPROVED => 'Approuvé',
            self::FAILED => 'Échoué',
            self::REFUNDED => 'Remboursé',
            self::EXPIRED => 'Expiré',
        };
    }
}
