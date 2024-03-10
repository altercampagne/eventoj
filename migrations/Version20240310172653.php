<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240310172653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD minimum_price_per_day INT NOT NULL DEFAULT 2000');
        $this->addSql('ALTER TABLE event ADD support_price_per_day INT NOT NULL DEFAULT 4700');
        $this->addSql('ALTER TABLE event ADD days_at_solidarity_price INT NOT NULL DEFAULT 8');
        $this->addSql('ALTER TABLE event ALTER COLUMN minimum_price_per_day DROP DEFAULT');
        $this->addSql('ALTER TABLE event ALTER COLUMN support_price_per_day DROP DEFAULT');
        $this->addSql('ALTER TABLE event ALTER COLUMN days_at_solidarity_price DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN event.minimum_price_per_day IS \'The minimum price per day\'');
        $this->addSql('COMMENT ON COLUMN event.support_price_per_day IS \'The suggested support price per day for this event\'');
        $this->addSql('COMMENT ON COLUMN event.days_at_solidarity_price IS \'The maximum number of days at the solidarity price.\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "event" DROP minimum_price_per_day');
        $this->addSql('ALTER TABLE "event" DROP support_price_per_day');
        $this->addSql('ALTER TABLE "event" DROP days_at_solidarity_price');
    }
}
