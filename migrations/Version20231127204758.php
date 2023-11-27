<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231127204758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD last_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD birth_date DATE NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD phone_number VARCHAR(35) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD biography TEXT');
        $this->addSql('ALTER TABLE "user" ADD address_address_line1 VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD address_address_line2 VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ADD address_city VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD address_zip_code VARCHAR(6) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD address_country_code VARCHAR(2) NOT NULL');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN name TO first_name');
        $this->addSql('ALTER TABLE "user" ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN "user".birth_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".phone_number IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" DROP first_name');
        $this->addSql('ALTER TABLE "user" DROP last_name');
        $this->addSql('ALTER TABLE "user" DROP birth_date');
        $this->addSql('ALTER TABLE "user" DROP phone_number');
        $this->addSql('ALTER TABLE "user" DROP biography');
        $this->addSql('ALTER TABLE "user" DROP address_address_line1');
        $this->addSql('ALTER TABLE "user" DROP address_address_line2');
        $this->addSql('ALTER TABLE "user" DROP address_city');
        $this->addSql('ALTER TABLE "user" DROP address_zip_code');
        $this->addSql('ALTER TABLE "user" DROP address_country_code');
        $this->addSql('ALTER TABLE "user" DROP created_at');
    }
}
