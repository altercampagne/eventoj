<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208132630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "payment" (id UUID NOT NULL, payer_id UUID NOT NULL, registration_id UUID NOT NULL, status VARCHAR(20) NOT NULL, amount INT NOT NULL, helloasso_checkout_intent_id VARCHAR(255) DEFAULT NULL, approved_at TIMESTAMP(0) WITHOUT TIME ZONE, failed_at TIMESTAMP(0) WITHOUT TIME ZONE, refunded_at TIMESTAMP(0) WITHOUT TIME ZONE, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6D28840DC17AD9A9 ON "payment" (payer_id)');
        $this->addSql('CREATE INDEX IDX_6D28840D833D8F43 ON "payment" (registration_id)');
        $this->addSql('CREATE INDEX idx_payment_status ON "payment" (status)');
        $this->addSql('COMMENT ON COLUMN "payment".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "payment".payer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "payment".registration_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "payment".status IS \'Status of this payment (pending, approved, failed)\'');
        $this->addSql('COMMENT ON COLUMN "payment".amount IS \'The amount of thie payment\'');
        $this->addSql('COMMENT ON COLUMN "payment".helloasso_checkout_intent_id IS \'The checkout intent ID provided by Helloasso\'');
        $this->addSql('COMMENT ON COLUMN "payment".approved_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "payment".failed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "payment".refunded_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "payment".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "payment" ADD CONSTRAINT FK_6D28840DC17AD9A9 FOREIGN KEY (payer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "payment" ADD CONSTRAINT FK_6D28840D833D8F43 FOREIGN KEY (registration_id) REFERENCES "registration" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE registration DROP helloasso_checkout_intent_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "payment" DROP CONSTRAINT FK_6D28840DC17AD9A9');
        $this->addSql('ALTER TABLE "payment" DROP CONSTRAINT FK_6D28840D833D8F43');
        $this->addSql('DROP TABLE "payment"');
        $this->addSql('ALTER TABLE "registration" ADD helloasso_checkout_intent_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "registration".helloasso_checkout_intent_id IS \'The checkout intent ID provided by Helloasso\'');
    }
}
