<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240219204231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "uploaded_file" (id UUID NOT NULL, type VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, original_file_name VARCHAR(255) NOT NULL, size INT DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_uploaded_file_path ON "uploaded_file" (path)');
        $this->addSql('COMMENT ON COLUMN "uploaded_file".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "uploaded_file".type IS \'Type of this file which correspond to the entity its linked to (event, alternative, ...).\'');
        $this->addSql('COMMENT ON COLUMN "uploaded_file".path IS \'This is the path on ObjectStorage.\'');
        $this->addSql('COMMENT ON COLUMN "uploaded_file".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE event ADD uploaded_file_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE event DROP image_path');
        $this->addSql('COMMENT ON COLUMN event.uploaded_file_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7276973A0 FOREIGN KEY (uploaded_file_id) REFERENCES "uploaded_file" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA7276973A0 ON event (uploaded_file_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "event" DROP CONSTRAINT FK_3BAE0AA7276973A0');
        $this->addSql('DROP TABLE "uploaded_file"');
        $this->addSql('DROP INDEX UNIQ_3BAE0AA7276973A0');
        $this->addSql('ALTER TABLE "event" ADD image_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "event" DROP uploaded_file_id');
    }
}
