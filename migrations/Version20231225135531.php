<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231225135531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registration ADD first_meal VARCHAR(10) NOT NULL DEFAULT \'lunch\'');
        $this->addSql('ALTER TABLE registration ADD last_meal VARCHAR(10) NOT NULL DEFAULT \'lunch\'');
        $this->addSql('ALTER TABLE registration ADD need_bike BOOLEAN NOT NULL DEFAULT false');
        $this->addSql('ALTER TABLE registration ADD helloasso_checkout_intent_id VARCHAR(255)');
        $this->addSql('COMMENT ON COLUMN registration.first_meal IS \'First meal participant will share with us (breakfast, lunch, dinner)\'');
        $this->addSql('COMMENT ON COLUMN registration.last_meal IS \'Last meal participant will share with us (breakfast, lunch, dinner)\'');
        $this->addSql('COMMENT ON COLUMN registration.need_bike IS \'Does the participant need a loan bike?\'');
        $this->addSql('COMMENT ON COLUMN registration.helloasso_checkout_intent_id IS \'The checkout intent ID provided by Helloasso\'');
        $this->addSql('ALTER TABLE registration ALTER COLUMN first_meal DROP DEFAULT');
        $this->addSql('ALTER TABLE registration ALTER COLUMN last_meal DROP DEFAULT');
        $this->addSql('ALTER TABLE registration ALTER COLUMN need_bike DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "registration" DROP first_meal');
        $this->addSql('ALTER TABLE "registration" DROP last_meal');
        $this->addSql('ALTER TABLE "registration" DROP need_bike');
        $this->addSql('ALTER TABLE "registration" DROP helloasso_checkout_intent_id');
    }
}
