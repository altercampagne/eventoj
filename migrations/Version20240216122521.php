<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216122521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD break_even_price_per_day INT NOT NULL DEFAULT 3300');
        $this->addSql('COMMENT ON COLUMN event.break_even_price_per_day IS \'The average price per day at which we need to sell tickets in order to break even on this event\'');
        $this->addSql('ALTER TABLE event ALTER COLUMN break_even_price_per_day DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "event" DROP break_even_price_per_day');
    }
}
