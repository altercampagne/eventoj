<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208203728 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE companion (id UUID NOT NULL, user_id UUID NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, birth_date DATE NOT NULL, email VARCHAR(180) DEFAULT NULL, phone_number VARCHAR(35) DEFAULT NULL, diet VARCHAR(255) NOT NULL, gluten_intolerant BOOLEAN DEFAULT false NOT NULL, lactose_intolerant BOOLEAN DEFAULT false NOT NULL, diet_details VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1BAD2E69A76ED395 ON companion (user_id)');
        $this->addSql('COMMENT ON COLUMN companion.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN companion.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN companion.birth_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN companion.phone_number IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN companion.diet IS \'Diet of the user (omnivore, vegetarien, vegan)\'');
        $this->addSql('COMMENT ON COLUMN companion.diet_details IS \'Free field to provide more information about user diet.\'');
        $this->addSql('COMMENT ON COLUMN companion.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE companion ADD CONSTRAINT FK_1BAD2E69A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE companion DROP CONSTRAINT FK_1BAD2E69A76ED395');
        $this->addSql('DROP TABLE companion');
    }
}
