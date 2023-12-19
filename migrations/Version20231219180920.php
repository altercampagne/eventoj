<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231219180920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ALTER type TYPE VARCHAR(3)');
        $this->addSql('COMMENT ON COLUMN event.type IS \'Type of event (AT, BT, ADT, EB)\'');
        $this->addSql('COMMENT ON COLUMN event.opening_date_for_bookings IS \'At which date members will be able to register to this event?(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE stage ALTER difficulty TYPE VARCHAR(6)');
        $this->addSql('ALTER TABLE stage ALTER type TYPE VARCHAR(7)');
        $this->addSql('COMMENT ON COLUMN stage.difficulty IS \'Difficulty of this stage (easy, medium, hard).\'');
        $this->addSql('COMMENT ON COLUMN stage.type IS \'Type of this stage (before, after, classic).\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "stage" ALTER type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "stage" ALTER difficulty TYPE VARCHAR(255)');
        $this->addSql('COMMENT ON COLUMN "stage".type IS NULL');
        $this->addSql('COMMENT ON COLUMN "stage".difficulty IS NULL');
        $this->addSql('ALTER TABLE "event" ALTER type TYPE VARCHAR(255)');
        $this->addSql('COMMENT ON COLUMN "event".type IS NULL');
        $this->addSql('COMMENT ON COLUMN "event".opening_date_for_bookings IS \'(DC2Type:date_immutable)\'');
    }
}
