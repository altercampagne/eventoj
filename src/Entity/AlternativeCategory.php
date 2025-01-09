<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Categories comes from Transiscope.
 *
 * @see https://transiscope.gogocarto.fr/map
 */
enum AlternativeCategory: string implements TranslatableInterface
{
    // Ecology, alimentation & energy
    case AgricultureAlimentation = 'agriculture_alimentation';
    case ClimateEnergy = 'climate_energy';
    case WaterNatureBiodiversity = 'water_nature_biodiversity';

    // Culture, medias & social links
    case Culture = 'culture';
    case MeetingSpace = 'meeting_space';
    case MediaDigital = 'media_digital';

    // Physical and personal well-being
    case Healthcare = 'healthcare';
    case Seniors = 'seniors';
    case Disability = 'disability';
    case WellBeing = 'well_being';

    // Citizenship, rights, solidarity
    case Solidarity = 'solidarity';
    case CitizenshipRightsInclusion = 'citizenship_rights_inclusion';
    case CollectiveIntelligence = 'collective_intelligence';

    // Education, formation
    case Education = 'education';
    case Sensibilisation = 'sensibilisation';
    case Formation = 'formation';

    // Habitat and urban planning
    case HabitatHousing = 'habitat_housing';
    case UrbanDevelopment = 'urban_development';

    case Transport = 'transport';

    // Social and solidarity economy
    case ZeroWaste = 'zero_waste';
    case AlternativeTradingSystems = 'alternative_trading_systems';
    case EthicalInvestment = 'ethical_investment';
    case ProducersSuppliers = 'producers_suppliers';
    case Shops = 'shops';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::AgricultureAlimentation => 'Agriculture et alimentation',
            self::ClimateEnergy => 'Climat et énergie',
            self::WaterNatureBiodiversity => 'Eau, nature et biodiversité',
            self::Culture => 'Culture',
            self::MeetingSpace => 'Espaces de rencontres et de lien social',
            self::MediaDigital => 'Médias et numérique',
            self::Healthcare => 'Santé',
            self::Seniors => 'Personnes âgées',
            self::Disability => 'Handicap',
            self::WellBeing => 'Développement personnel, bien-être',
            self::Solidarity => 'Solidarité',
            self::CitizenshipRightsInclusion => 'Citoyenneté, droits, inclusion',
            self::CollectiveIntelligence => 'Intelligence collective',
            self::Education => 'Éducation',
            self::Sensibilisation => 'Sensibilisation',
            self::Formation => 'Formation',
            self::HabitatHousing => 'Habitat, logement',
            self::UrbanDevelopment => 'Aménagements et projets urbains',
            self::Transport => 'Modes de déplacement',
            self::ZeroWaste => 'Fabriquer, réparer, zéro déchets',
            self::AlternativeTradingSystems => 'Systèmes d\'échange alternatifs',
            self::EthicalInvestment => 'Finance éthique',
            self::ProducersSuppliers => 'Producteurs, fournisseurs',
            self::Shops => 'Magasins, commerces',
        };
    }

    public function getTheme(): AlternativeCategoryTheme
    {
        return match ($this) {
            self::AgricultureAlimentation, self::ClimateEnergy, self::WaterNatureBiodiversity => AlternativeCategoryTheme::Alimentation,
            self::Culture, self::MeetingSpace, self::MediaDigital => AlternativeCategoryTheme::Culture,
            self::Healthcare, self::Seniors, self::Disability, self::WellBeing => AlternativeCategoryTheme::WellBeing,
            self::Solidarity, self::CitizenshipRightsInclusion, self::CollectiveIntelligence => AlternativeCategoryTheme::Citizenship,
            self::Education, self::Sensibilisation, self::Formation => AlternativeCategoryTheme::Education,
            self::HabitatHousing, self::UrbanDevelopment => AlternativeCategoryTheme::Habitat,
            self::Transport => AlternativeCategoryTheme::Transport,
            self::ZeroWaste, self::AlternativeTradingSystems, self::EthicalInvestment, self::ProducersSuppliers, self::Shops => AlternativeCategoryTheme::Social,
        };
    }
}
