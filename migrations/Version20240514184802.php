<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240514184802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stage ADD booked_adults_seats INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE stage ALTER COLUMN booked_adults_seats DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN stage.booked_adults_seats IS \'Number of booked seats, adults only (computed)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "stage" DROP booked_adults_seats');
    }
}
