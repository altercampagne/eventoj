<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231217175334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE event ADD slug VARCHAR(128) NOT NULL');
        $this->addSql('ALTER TABLE event ADD adults_capacity INT NOT NULL');
        $this->addSql('ALTER TABLE event ADD children_capacity INT NOT NULL');
        $this->addSql('ALTER TABLE event ADD published_at DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD opening_date_for_bookings DATE NOT NULL');
        $this->addSql('ALTER TABLE event ALTER created_at TYPE DATE');
        $this->addSql('COMMENT ON COLUMN event.published_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN event.opening_date_for_bookings IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN event.created_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA7989D9B62 ON event (slug)');

        $this->addSql('ALTER TABLE alternative ADD address_address_line1 VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE alternative ADD address_address_line2 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE alternative ADD address_city VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE alternative ADD address_zip_code VARCHAR(6) NOT NULL');
        $this->addSql('ALTER TABLE alternative ADD address_country_code VARCHAR(2) NOT NULL');
        $this->addSql('ALTER TABLE alternative ALTER description DROP NOT NULL');

        $this->addSql('CREATE TABLE "stage" (id UUID NOT NULL, event_id UUID DEFAULT NULL, departure_alternative_id UUID DEFAULT NULL, arrival_alternative_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(128) NOT NULL, description TEXT NOT NULL, created_at DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C27C9369989D9B62 ON "stage" (slug)');
        $this->addSql('CREATE INDEX IDX_C27C936971F7E88B ON "stage" (event_id)');
        $this->addSql('CREATE INDEX IDX_C27C9369241FC583 ON "stage" (departure_alternative_id)');
        $this->addSql('CREATE INDEX IDX_C27C9369DB817EC2 ON "stage" (arrival_alternative_id)');
        $this->addSql('COMMENT ON COLUMN "stage".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "stage".event_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "stage".departure_alternative_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "stage".arrival_alternative_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "stage".created_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE "stage" ADD CONSTRAINT FK_C27C936971F7E88B FOREIGN KEY (event_id) REFERENCES "event" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "stage" ADD CONSTRAINT FK_C27C9369241FC583 FOREIGN KEY (departure_alternative_id) REFERENCES "alternative" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "stage" ADD CONSTRAINT FK_C27C9369DB817EC2 FOREIGN KEY (arrival_alternative_id) REFERENCES "alternative" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE alternative ADD slug VARCHAR(128) NOT NULL');
        $this->addSql('ALTER TABLE alternative ALTER created_at TYPE DATE');
        $this->addSql('COMMENT ON COLUMN alternative.created_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EFF5DFA989D9B62 ON alternative (slug)');
        $this->addSql('ALTER TABLE stage ADD date DATE NOT NULL');
        $this->addSql('COMMENT ON COLUMN stage.date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE stage ADD difficulty VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE stage ADD type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_3BAE0AA7989D9B62');
        $this->addSql('ALTER TABLE "event" DROP type');
        $this->addSql('ALTER TABLE "event" DROP slug');
        $this->addSql('ALTER TABLE "event" DROP adults_capacity');
        $this->addSql('ALTER TABLE "event" DROP children_capacity');
        $this->addSql('ALTER TABLE "event" DROP published_at');
        $this->addSql('ALTER TABLE "event" DROP opening_date_for_bookings');
        $this->addSql('ALTER TABLE "event" ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN "event".created_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('ALTER TABLE "alternative" ALTER description SET NOT NULL');
        $this->addSql('ALTER TABLE "alternative" DROP address_address_line1');
        $this->addSql('ALTER TABLE "alternative" DROP address_address_line2');
        $this->addSql('ALTER TABLE "alternative" DROP address_city');
        $this->addSql('ALTER TABLE "alternative" DROP address_zip_code');
        $this->addSql('ALTER TABLE "alternative" DROP address_country_code');

        $this->addSql('ALTER TABLE "stage" DROP CONSTRAINT FK_C27C936971F7E88B');
        $this->addSql('ALTER TABLE "stage" DROP CONSTRAINT FK_C27C9369241FC583');
        $this->addSql('ALTER TABLE "stage" DROP CONSTRAINT FK_C27C9369DB817EC2');
        $this->addSql('DROP TABLE "stage"');
        $this->addSql('DROP INDEX UNIQ_EFF5DFA989D9B62');
        $this->addSql('ALTER TABLE "alternative" DROP slug');
        $this->addSql('ALTER TABLE "alternative" ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN "alternative".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "stage" DROP date');
        $this->addSql('ALTER TABLE "stage" DROP type');
        $this->addSql('ALTER TABLE "stage" DROP difficulty');
    }
}
