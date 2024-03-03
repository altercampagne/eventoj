<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240303145804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "membership" (id UUID NOT NULL, user_id UUID DEFAULT NULL, companion_id UUID DEFAULT NULL, payment_id UUID NOT NULL, price INT NOT NULL, start_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_86FFD285A76ED395 ON "membership" (user_id)');
        $this->addSql('CREATE INDEX IDX_86FFD2858227E3FD ON "membership" (companion_id)');
        $this->addSql('CREATE INDEX IDX_86FFD2854C3A3BB ON "membership" (payment_id)');
        $this->addSql('COMMENT ON COLUMN "membership".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "membership".user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "membership".companion_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "membership".payment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "membership".price IS \'The price of this membership\'');
        $this->addSql('COMMENT ON COLUMN "membership".start_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "membership".end_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "membership".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "membership".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "membership" ADD CONSTRAINT FK_86FFD285A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "membership" ADD CONSTRAINT FK_86FFD2858227E3FD FOREIGN KEY (companion_id) REFERENCES companion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "membership" ADD CONSTRAINT FK_86FFD2854C3A3BB FOREIGN KEY (payment_id) REFERENCES "payment" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "membership" DROP CONSTRAINT FK_86FFD285A76ED395');
        $this->addSql('ALTER TABLE "membership" DROP CONSTRAINT FK_86FFD2858227E3FD');
        $this->addSql('ALTER TABLE "membership" DROP CONSTRAINT FK_86FFD2854C3A3BB');
        $this->addSql('DROP TABLE "membership"');
    }
}
