<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231219205629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "stages_alternatives" (id UUID NOT NULL, stage_id UUID DEFAULT NULL, alternative_id UUID DEFAULT NULL, relation VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75B26882298D193 ON "stages_alternatives" (stage_id)');
        $this->addSql('CREATE INDEX IDX_75B2688FC05FFAC ON "stages_alternatives" (alternative_id)');
        $this->addSql('COMMENT ON COLUMN "stages_alternatives".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "stages_alternatives".stage_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "stages_alternatives".alternative_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "stages_alternatives".relation IS \'Relation between stage & alternative (departure, arrival, visit, stay)\'');
        $this->addSql('ALTER TABLE "stages_alternatives" ADD CONSTRAINT FK_75B26882298D193 FOREIGN KEY (stage_id) REFERENCES "stage" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "stages_alternatives" ADD CONSTRAINT FK_75B2688FC05FFAC FOREIGN KEY (alternative_id) REFERENCES "alternative" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stage DROP CONSTRAINT fk_c27c9369241fc583');
        $this->addSql('ALTER TABLE stage DROP CONSTRAINT fk_c27c9369db817ec2');
        $this->addSql('DROP INDEX idx_c27c9369db817ec2');
        $this->addSql('DROP INDEX idx_c27c9369241fc583');
        $this->addSql('ALTER TABLE stage DROP departure_alternative_id');
        $this->addSql('ALTER TABLE stage DROP arrival_alternative_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "stages_alternatives" DROP CONSTRAINT FK_75B26882298D193');
        $this->addSql('ALTER TABLE "stages_alternatives" DROP CONSTRAINT FK_75B2688FC05FFAC');
        $this->addSql('DROP TABLE "stages_alternatives"');
        $this->addSql('ALTER TABLE "stage" ADD departure_alternative_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE "stage" ADD arrival_alternative_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "stage".departure_alternative_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "stage".arrival_alternative_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "stage" ADD CONSTRAINT fk_c27c9369241fc583 FOREIGN KEY (departure_alternative_id) REFERENCES alternative (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "stage" ADD CONSTRAINT fk_c27c9369db817ec2 FOREIGN KEY (arrival_alternative_id) REFERENCES alternative (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_c27c9369db817ec2 ON "stage" (arrival_alternative_id)');
        $this->addSql('CREATE INDEX idx_c27c9369241fc583 ON "stage" (departure_alternative_id)');
    }
}
