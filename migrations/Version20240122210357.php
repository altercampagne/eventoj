<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240122210357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registration DROP first_meal');
        $this->addSql('ALTER TABLE registration DROP last_meal');
        $this->addSql('ALTER TABLE stages_registrations DROP CONSTRAINT FK_DCFAFC15833D8F43');
        $this->addSql('ALTER TABLE stages_registrations DROP CONSTRAINT FK_DCFAFC152298D193');
        $this->addSql('ALTER TABLE stages_registrations DROP CONSTRAINT stages_registrations_pkey');
        $this->addSql('ALTER TABLE stages_registrations ADD id UUID NOT NULL');
        $this->addSql('ALTER TABLE stages_registrations ADD present_for_breakfast BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE stages_registrations ADD present_for_lunch BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE stages_registrations ADD present_for_dinner BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE stages_registrations ALTER registration_id DROP NOT NULL');
        $this->addSql('ALTER TABLE stages_registrations ALTER stage_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN stages_registrations.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN stages_registrations.present_for_breakfast IS \'Does the participant will be present for the breakfast?\'');
        $this->addSql('COMMENT ON COLUMN stages_registrations.present_for_lunch IS \'Does the participant will be present for the lunch?\'');
        $this->addSql('COMMENT ON COLUMN stages_registrations.present_for_dinner IS \'Does the participant will be present for the dinner?\'');
        $this->addSql('ALTER TABLE stages_registrations ADD CONSTRAINT FK_DCFAFC15833D8F43 FOREIGN KEY (registration_id) REFERENCES "registration" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stages_registrations ADD CONSTRAINT FK_DCFAFC152298D193 FOREIGN KEY (stage_id) REFERENCES "stage" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stages_registrations ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE stages_registrations ALTER registration_id SET NOT NULL');
        $this->addSql('ALTER TABLE stages_registrations ALTER stage_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "registration" ADD first_meal VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE "registration" ADD last_meal VARCHAR(10) NOT NULL');
        $this->addSql('COMMENT ON COLUMN "registration".first_meal IS \'First meal participant will share with us (breakfast, lunch, dinner)\'');
        $this->addSql('COMMENT ON COLUMN "registration".last_meal IS \'Last meal participant will share with us (breakfast, lunch, dinner)\'');
        $this->addSql('ALTER TABLE "stages_registrations" DROP CONSTRAINT fk_dcfafc152298d193');
        $this->addSql('ALTER TABLE "stages_registrations" DROP CONSTRAINT fk_dcfafc15833d8f43');
        $this->addSql('DROP INDEX stages_registrations_pkey');
        $this->addSql('ALTER TABLE "stages_registrations" DROP id');
        $this->addSql('ALTER TABLE "stages_registrations" DROP present_for_breakfast');
        $this->addSql('ALTER TABLE "stages_registrations" DROP present_for_lunch');
        $this->addSql('ALTER TABLE "stages_registrations" DROP present_for_dinner');
        $this->addSql('ALTER TABLE "stages_registrations" ALTER stage_id SET NOT NULL');
        $this->addSql('ALTER TABLE "stages_registrations" ALTER registration_id SET NOT NULL');
        $this->addSql('ALTER TABLE "stages_registrations" ADD CONSTRAINT fk_dcfafc152298d193 FOREIGN KEY (stage_id) REFERENCES stage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "stages_registrations" ADD CONSTRAINT fk_dcfafc15833d8f43 FOREIGN KEY (registration_id) REFERENCES registration (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "stages_registrations" ADD PRIMARY KEY (registration_id, stage_id)');
        $this->addSql('ALTER TABLE "stages_registrations" ALTER stage_id DROP NOT NULL');
        $this->addSql('ALTER TABLE "stages_registrations" ALTER registration_id DROP NOT NULL');
    }
}
