<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum QuestionCategory: string implements TranslatableInterface
{
    case REGISTRATION = '1registration';
    case PRICE = '2price';
    case CANCELATION = '3cancelation';
    case CHILDREN = '4children';
    case STAGES = '5stages';
    case DAILY_LIFE = '6daily_life';
    case JOIN_THE_TOUR = '7join_the_tour';
    case BIKES = '8bikes';
    case GLOSSARY = '9glossary';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::REGISTRATION => 'Inscriptions',
            self::PRICE => 'Tarif',
            self::CANCELATION => 'Annulation',
            self::CHILDREN => 'Enfants',
            self::STAGES => 'Étapes',
            self::DAILY_LIFE => 'Vie quotidienne',
            self::JOIN_THE_TOUR => 'Rejoindre le tour',
            self::BIKES => 'Vélos',
            self::GLOSSARY => 'Lexique',
        };
    }
}
