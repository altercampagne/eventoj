<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240424163401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO public.question (id, slug, question, answer, locked, created_at, updated_at, category) VALUES ('fa87888f-9f0c-4a70-81e0-633bdd8df0ce', 'comment-est-determinee-la-difficulte-dune-etape', 'Comment est déterminée la difficulté d''une étape ?', '<div>La difficulté de l''étape est donnée à titre indicatif : il s''agit d''une information qui est forcément très subjective et dépendante de la forme des participant·es.<br><br><strong>Étape facile :<br></strong>Jusqu''à 30 kilomètres, pas de pente continue de plus de 7%, jusqu''à 300 mètres de dénivelé positif.<br><br><strong>Étape moyenne :<br></strong>Jusqu''à 60 kilomètres, pas de pente continue supérieure à 11%, jusqu''à 600 mètres de dénivelé positif.<br><br><strong>Étape difficile :<br></strong>Plus de 60 kilomètres, des pentes continues de plus de 11%, plus de 600 de dénivelé positif.</div>', true, '2024-04-24 18:28:45', '2024-04-24 18:28:45', '5stages') ON CONFLICT DO NOTHING");
    }

    public function down(Schema $schema): void
    {
    }
}
