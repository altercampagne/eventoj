<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260201212454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registration ADD created_by UUID');
        $this->addSql('UPDATE registration SET created_by = user_id WHERE created_by IS NULL');
        $this->addSql('ALTER TABLE registration ALTER COLUMN created_by SET NOT NULL');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7DE12AB56 FOREIGN KEY (created_by) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_62A8A7A7DE12AB56 ON registration (created_by)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "registration" DROP CONSTRAINT FK_62A8A7A7DE12AB56');
        $this->addSql('DROP INDEX IDX_62A8A7A7DE12AB56');
        $this->addSql('ALTER TABLE "registration" DROP created_by');
    }
}
