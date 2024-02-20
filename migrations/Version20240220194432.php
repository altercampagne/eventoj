<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220194432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stages_alternatives DROP CONSTRAINT FK_75B26882298D193');
        $this->addSql('ALTER TABLE stages_alternatives DROP CONSTRAINT FK_75B2688FC05FFAC');
        $this->addSql('ALTER TABLE stages_alternatives DROP CONSTRAINT stages_alternatives_pkey');
        $this->addSql('ALTER TABLE stages_alternatives DROP id');
        $this->addSql('ALTER TABLE stages_alternatives DROP relation');
        $this->addSql('ALTER TABLE stages_alternatives ADD CONSTRAINT FK_75B26882298D193 FOREIGN KEY (stage_id) REFERENCES "stage" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stages_alternatives ADD CONSTRAINT FK_75B2688FC05FFAC FOREIGN KEY (alternative_id) REFERENCES "alternative" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stages_alternatives ADD PRIMARY KEY (stage_id, alternative_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE stages_alternatives DROP CONSTRAINT fk_75b26882298d193');
        $this->addSql('ALTER TABLE stages_alternatives DROP CONSTRAINT fk_75b2688fc05ffac');
        $this->addSql('DROP INDEX stages_alternatives_pkey');
        $this->addSql('ALTER TABLE stages_alternatives ADD id UUID NOT NULL');
        $this->addSql('ALTER TABLE stages_alternatives ADD relation VARCHAR(10) NOT NULL');
        $this->addSql('COMMENT ON COLUMN stages_alternatives.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN stages_alternatives.relation IS \'Relation between stage & alternative (departure, arrival, visit, full_day)\'');
        $this->addSql('ALTER TABLE stages_alternatives ADD CONSTRAINT fk_75b26882298d193 FOREIGN KEY (stage_id) REFERENCES stage (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stages_alternatives ADD CONSTRAINT fk_75b2688fc05ffac FOREIGN KEY (alternative_id) REFERENCES alternative (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stages_alternatives ADD PRIMARY KEY (id)');
    }
}
