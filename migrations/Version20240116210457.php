<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240116210457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registration DROP canceled_at');
        $this->addSql('COMMENT ON COLUMN registration.status IS \'Status of this registration (waiting_payment, confirmed)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "registration" ADD canceled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "registration".canceled_at IS \'Date on which the reservation was cancelled.(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "registration".status IS \'Status of this registration (waiting_payment, confirmed, canceled)\'');
    }
}
