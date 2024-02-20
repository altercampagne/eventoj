<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220115957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alternative ADD uploaded_file_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN alternative.uploaded_file_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE alternative ADD CONSTRAINT FK_EFF5DFA276973A0 FOREIGN KEY (uploaded_file_id) REFERENCES "uploaded_file" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EFF5DFA276973A0 ON alternative (uploaded_file_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "alternative" DROP CONSTRAINT FK_EFF5DFA276973A0');
        $this->addSql('DROP INDEX UNIQ_EFF5DFA276973A0');
        $this->addSql('ALTER TABLE "alternative" DROP uploaded_file_id');
    }
}
