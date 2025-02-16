<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216112608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_picture ADD width INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event_picture ADD height INT DEFAULT NULL');
        $this->addSql('ALTER TABLE uploaded_image ADD width INT DEFAULT NULL');
        $this->addSql('ALTER TABLE uploaded_image ADD height INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "event_picture" DROP width');
        $this->addSql('ALTER TABLE "event_picture" DROP height');
        $this->addSql('ALTER TABLE "uploaded_image" DROP width');
        $this->addSql('ALTER TABLE "uploaded_image" DROP height');
    }
}
