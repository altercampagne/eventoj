<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220202159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stages_preparations (stage_id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY(stage_id, user_id))');
        $this->addSql('CREATE INDEX IDX_E01404782298D193 ON stages_preparations (stage_id)');
        $this->addSql('CREATE INDEX IDX_E0140478A76ED395 ON stages_preparations (user_id)');
        $this->addSql('COMMENT ON COLUMN stages_preparations.stage_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN stages_preparations.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE stages_preparations ADD CONSTRAINT FK_E01404782298D193 FOREIGN KEY (stage_id) REFERENCES "stage" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stages_preparations ADD CONSTRAINT FK_E0140478A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE stages_preparations DROP CONSTRAINT FK_E01404782298D193');
        $this->addSql('ALTER TABLE stages_preparations DROP CONSTRAINT FK_E0140478A76ED395');
        $this->addSql('DROP TABLE stages_preparations');
    }
}
