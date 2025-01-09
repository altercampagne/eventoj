<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum AlternativeCategoryTheme implements TranslatableInterface
{
    case Alimentation;
    case Culture;
    case WellBeing;
    case Citizenship;
    case Education;
    case Habitat;
    case Transport;
    case Social;

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::Alimentation => 'Écologie, Alimentation, Énergie',
            self::Culture => 'Culture, médiés et lien social',
            self::WellBeing => 'Bien-être physique et personnel',
            self::Citizenship => 'Citoyennetés, droits, solidarités',
            self::Education => 'Éducation, formation',
            self::Habitat => 'Habitat et urbanisme',
            self::Transport => 'Modes de déplacement',
            self::Social => 'Économie sociale et solidaire',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Alimentation => '#8fce00',
            self::Culture => '#c90076',
            self::WellBeing => '#6a329f',
            self::Citizenship => '#16537e',
            self::Education => '#2986cc',
            self::Habitat => '#898989',
            self::Transport => '#ce7e00',
            self::Social => '#898989',
        };
    }

    /**
     * @return AlternativeCategory[]
     */
    public function getCategories(): array
    {
        return match ($this) {
            self::Alimentation => [AlternativeCategory::AgricultureAlimentation, AlternativeCategory::ClimateEnergy, AlternativeCategory::WaterNatureBiodiversity],
            self::Culture => [AlternativeCategory::Culture, AlternativeCategory::MeetingSpace, AlternativeCategory::MediaDigital],
            self::WellBeing => [AlternativeCategory::Healthcare, AlternativeCategory::Seniors, AlternativeCategory::Disability, AlternativeCategory::WellBeing],
            self::Citizenship => [AlternativeCategory::Solidarity, AlternativeCategory::CitizenshipRightsInclusion, AlternativeCategory::CollectiveIntelligence],
            self::Education => [AlternativeCategory::Education, AlternativeCategory::Sensibilisation, AlternativeCategory::Formation],
            self::Habitat => [AlternativeCategory::HabitatHousing, AlternativeCategory::UrbanDevelopment],
            self::Transport => [AlternativeCategory::Transport],
            self::Social => [AlternativeCategory::ZeroWaste, AlternativeCategory::AlternativeTradingSystems, AlternativeCategory::EthicalInvestment, AlternativeCategory::ProducersSuppliers, AlternativeCategory::Shops],
        };
    }
}
