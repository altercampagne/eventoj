<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240113164854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alternative ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN alternative.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE event ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE event ALTER published_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE event ALTER opening_date_for_bookings TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN event.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN event.published_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN event.opening_date_for_bookings IS \'At which date members will be able to register to this event?(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE registration ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE registration ALTER confirmed_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE registration ALTER canceled_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN registration.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN registration.confirmed_at IS \'Date on which the reservation was confirmed.(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN registration.canceled_at IS \'Date on which the reservation was cancelled.(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE stage ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN stage.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "event" ALTER published_at TYPE DATE');
        $this->addSql('ALTER TABLE "event" ALTER opening_date_for_bookings TYPE DATE');
        $this->addSql('ALTER TABLE "event" ALTER created_at TYPE DATE');
        $this->addSql('COMMENT ON COLUMN "event".published_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "event".opening_date_for_bookings IS \'At which date members will be able to register to this event?(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "event".created_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE "stage" ALTER created_at TYPE DATE');
        $this->addSql('COMMENT ON COLUMN "stage".created_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE "registration" ALTER created_at TYPE DATE');
        $this->addSql('ALTER TABLE "registration" ALTER confirmed_at TYPE DATE');
        $this->addSql('ALTER TABLE "registration" ALTER canceled_at TYPE DATE');
        $this->addSql('COMMENT ON COLUMN "registration".created_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "registration".confirmed_at IS \'Date on which the reservation was confirmed.(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "registration".canceled_at IS \'Date on which the reservation was cancelled.(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE "alternative" ALTER created_at TYPE DATE');
        $this->addSql('COMMENT ON COLUMN "alternative".created_at IS \'(DC2Type:date_immutable)\'');
    }
}
