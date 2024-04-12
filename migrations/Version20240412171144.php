<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240412171144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event ADD first_meal_of_first_day VARCHAR(10) NOT NULL DEFAULT \'lunch\'');
        $this->addSql('ALTER TABLE event ADD last_meal_of_last_day VARCHAR(10) NOT NULL DEFAULT \'lunch\'');
        $this->addSql('ALTER TABLE event ALTER COLUMN first_meal_of_first_day DROP DEFAULT');
        $this->addSql('ALTER TABLE event ALTER COLUMN last_meal_of_last_day DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "event" DROP first_meal_of_first_day');
        $this->addSql('ALTER TABLE "event" DROP last_meal_of_last_day');
    }
}
