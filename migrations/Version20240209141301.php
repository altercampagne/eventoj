<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240209141301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE registrations_companions (registration_id UUID NOT NULL, companion_id UUID NOT NULL, PRIMARY KEY(registration_id, companion_id))');
        $this->addSql('CREATE INDEX IDX_13847E7D833D8F43 ON registrations_companions (registration_id)');
        $this->addSql('CREATE INDEX IDX_13847E7D8227E3FD ON registrations_companions (companion_id)');
        $this->addSql('COMMENT ON COLUMN registrations_companions.registration_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN registrations_companions.companion_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE registrations_companions ADD CONSTRAINT FK_13847E7D833D8F43 FOREIGN KEY (registration_id) REFERENCES "registration" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE registrations_companions ADD CONSTRAINT FK_13847E7D8227E3FD FOREIGN KEY (companion_id) REFERENCES companion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE registration ADD needed_bike INT NOT NULL');
        $this->addSql('ALTER TABLE registration DROP need_bike');
        $this->addSql('COMMENT ON COLUMN registration.needed_bike IS \'How many bikes are needed by participants?\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE registrations_companions DROP CONSTRAINT FK_13847E7D833D8F43');
        $this->addSql('ALTER TABLE registrations_companions DROP CONSTRAINT FK_13847E7D8227E3FD');
        $this->addSql('DROP TABLE registrations_companions');
        $this->addSql('ALTER TABLE "registration" ADD need_bike BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE "registration" DROP needed_bike');
        $this->addSql('COMMENT ON COLUMN "registration".need_bike IS \'Does the participant need a loan bike?\'');
    }
}
