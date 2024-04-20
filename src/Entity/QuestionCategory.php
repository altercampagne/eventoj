<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum QuestionCategory: string implements TranslatableInterface
{
    case GENERAL = 'general';
    case CAT1 = 'cat1';
    case CAT2 = 'cat2';
    case CAT3 = 'cat3';
    case CAT4 = 'cat4';
    case CAT5 = 'cat5';
    case CAT6 = 'cat6';
    case CAT7 = 'cat7';
    case CAT8 = 'cat8';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $this->value;
    }
}
