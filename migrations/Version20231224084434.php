<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231224084434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "registration" (id UUID NOT NULL, user_id UUID DEFAULT NULL, event_id UUID DEFAULT NULL, status VARCHAR(20) NOT NULL, price_per_day INT NOT NULL, created_at DATE NOT NULL, confirmed_at DATE DEFAULT NULL, canceled_at DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_62A8A7A7A76ED395 ON "registration" (user_id)');
        $this->addSql('CREATE INDEX IDX_62A8A7A771F7E88B ON "registration" (event_id)');
        $this->addSql('COMMENT ON COLUMN "registration".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "registration".user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "registration".event_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "registration".status IS \'Status of this registration (waiting_payment, confirmed, canceled)\'');
        $this->addSql('COMMENT ON COLUMN "registration".price_per_day IS \'The price per day choose by the user.\'');
        $this->addSql('COMMENT ON COLUMN "registration".created_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "registration".confirmed_at IS \'Date on which the reservation was confirmed.(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "registration".canceled_at IS \'Date on which the reservation was cancelled.(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE stages_registrations (registration_id UUID NOT NULL, stage_id UUID NOT NULL, PRIMARY KEY(registration_id, stage_id))');
        $this->addSql('CREATE INDEX IDX_DCFAFC15833D8F43 ON stages_registrations (registration_id)');
        $this->addSql('CREATE INDEX IDX_DCFAFC152298D193 ON stages_registrations (stage_id)');
        $this->addSql('COMMENT ON COLUMN stages_registrations.registration_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN stages_registrations.stage_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "registration" ADD CONSTRAINT FK_62A8A7A7A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "registration" ADD CONSTRAINT FK_62A8A7A771F7E88B FOREIGN KEY (event_id) REFERENCES "event" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stages_registrations ADD CONSTRAINT FK_DCFAFC15833D8F43 FOREIGN KEY (registration_id) REFERENCES "registration" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stages_registrations ADD CONSTRAINT FK_DCFAFC152298D193 FOREIGN KEY (stage_id) REFERENCES "stage" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('COMMENT ON COLUMN stages_alternatives.relation IS \'Relation between stage & alternative (departure, arrival, visit, full_day)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "registration" DROP CONSTRAINT FK_62A8A7A7A76ED395');
        $this->addSql('ALTER TABLE "registration" DROP CONSTRAINT FK_62A8A7A771F7E88B');
        $this->addSql('ALTER TABLE stages_registrations DROP CONSTRAINT FK_DCFAFC15833D8F43');
        $this->addSql('ALTER TABLE stages_registrations DROP CONSTRAINT FK_DCFAFC152298D193');
        $this->addSql('DROP TABLE "registration"');
        $this->addSql('DROP TABLE stages_registrations');
        $this->addSql('COMMENT ON COLUMN "stages_alternatives".relation IS \'Relation between stage & alternative (departure, arrival, visit, stay)\'');
    }
}
