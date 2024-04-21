<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240421083539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alternative ADD address_latitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE alternative ADD address_longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE alternative ALTER address_address_line1 DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD address_latitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD address_longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ALTER address_address_line1 DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP address_latitude');
        $this->addSql('ALTER TABLE "user" DROP address_longitude');
        $this->addSql('ALTER TABLE "user" ALTER address_address_line1 SET NOT NULL');
        $this->addSql('ALTER TABLE "alternative" DROP address_latitude');
        $this->addSql('ALTER TABLE "alternative" DROP address_longitude');
        $this->addSql('ALTER TABLE "alternative" ALTER address_address_line1 SET NOT NULL');
    }
}
