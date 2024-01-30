<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240130213244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD diet VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD gluten_intolerant BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD lactose_intolerant BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD diet_details VARCHAR(255) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "user".diet IS \'Diet of the user (omnivore, vegetarien, vegan)\'');
        $this->addSql('COMMENT ON COLUMN "user".diet_details IS \'Free field to provide more information about user diet.\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP diet');
        $this->addSql('ALTER TABLE "user" DROP gluten_intolerant');
        $this->addSql('ALTER TABLE "user" DROP lactose_intolerant');
        $this->addSql('ALTER TABLE "user" DROP diet_details');
    }
}
