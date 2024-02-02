<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240202125049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX idx_alternative_slug ON alternative (slug)');
        $this->addSql('CREATE INDEX idx_event_slug ON event (slug)');
        $this->addSql('CREATE INDEX idx_registration_status ON registration (status)');
        $this->addSql('CREATE INDEX idx_stage_slug ON stage (slug)');
        $this->addSql('CREATE INDEX idx_user_email ON "user" (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX idx_user_email');
        $this->addSql('DROP INDEX idx_alternative_slug');
        $this->addSql('DROP INDEX idx_event_slug');
        $this->addSql('DROP INDEX idx_stage_slug');
        $this->addSql('DROP INDEX idx_registration_status');
    }
}
