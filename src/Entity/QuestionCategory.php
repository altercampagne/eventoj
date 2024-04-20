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
    case CAT9 = 'cat9';
    case CAT10 = 'cat10';
    case CAT11 = 'cat11';
    case CAT12 = 'cat12';
    case CAT13 = 'cat13';
    case CAT14 = 'cat14';
    case CAT15 = 'cat15';
    case CAT16 = 'cat16';
    case CAT17 = 'cat17';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $this->value;
    }
}
