<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241125172307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alternative_pictures (alternative_id UUID NOT NULL, file_id UUID NOT NULL, PRIMARY KEY(alternative_id, file_id))');
        $this->addSql('CREATE INDEX IDX_C47BEEF3FC05FFAC ON alternative_pictures (alternative_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C47BEEF393CB796C ON alternative_pictures (file_id)');
        $this->addSql('COMMENT ON COLUMN alternative_pictures.alternative_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN alternative_pictures.file_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE alternative_pictures ADD CONSTRAINT FK_C47BEEF3FC05FFAC FOREIGN KEY (alternative_id) REFERENCES "alternative" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE alternative_pictures ADD CONSTRAINT FK_C47BEEF393CB796C FOREIGN KEY (file_id) REFERENCES "uploaded_file" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('INSERT INTO alternative_pictures SELECT id, uploaded_file_id from alternative where uploaded_file_id is not null');

        $this->addSql('ALTER TABLE alternative DROP CONSTRAINT fk_eff5dfa276973a0');
        $this->addSql('DROP INDEX uniq_eff5dfa276973a0');
        $this->addSql('ALTER TABLE alternative DROP uploaded_file_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE alternative_pictures DROP CONSTRAINT FK_C47BEEF3FC05FFAC');
        $this->addSql('ALTER TABLE alternative_pictures DROP CONSTRAINT FK_C47BEEF393CB796C');
        $this->addSql('DROP TABLE alternative_pictures');
        $this->addSql('ALTER TABLE "alternative" ADD uploaded_file_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "alternative".uploaded_file_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "alternative" ADD CONSTRAINT fk_eff5dfa276973a0 FOREIGN KEY (uploaded_file_id) REFERENCES uploaded_file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_eff5dfa276973a0 ON "alternative" (uploaded_file_id)');
    }
}
