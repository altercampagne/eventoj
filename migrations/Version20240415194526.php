<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240415194526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment ADD refunded_amount INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN payment.refunded_amount IS \'The refunded amount for this payment\'');
        $this->addSql('COMMENT ON COLUMN payment.status IS \'Status of this payment (pending, approved, failed, refunded)\'');
        $this->addSql('COMMENT ON COLUMN payment.amount IS \'The amount of this payment\'');
        $this->addSql('COMMENT ON COLUMN registration.status IS \'Status of this registration (waiting_payment, confirmed, canceled)\'');

        $this->addSql('DROP INDEX uniq_6d28840dfe2c561e');
        $this->addSql('ALTER TABLE payment ADD paheko_refund_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE payment RENAME COLUMN paheko_id TO paheko_payment_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D483F53 ON payment (paheko_payment_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D4822FC53 ON payment (paheko_refund_id)');

        $this->addSql('ALTER TABLE registration ADD canceled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN registration.canceled_at IS \'Date on which the reservation was canceled.(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "payment" DROP refunded_amount');
        $this->addSql('COMMENT ON COLUMN "payment".status IS \'Status of this payment (pending, approved, failed)\'');
        $this->addSql('COMMENT ON COLUMN "payment".amount IS \'The amount of thie payment\'');
        $this->addSql('COMMENT ON COLUMN "registration".status IS \'Status of this registration (waiting_payment, confirmed)\'');

        $this->addSql('DROP INDEX UNIQ_6D28840D483F53');
        $this->addSql('DROP INDEX UNIQ_6D28840D4822FC53');
        $this->addSql('ALTER TABLE "payment" ADD paheko_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "payment" DROP paheko_payment_id');
        $this->addSql('ALTER TABLE "payment" DROP paheko_refund_id');
        $this->addSql('CREATE UNIQUE INDEX uniq_6d28840dfe2c561e ON "payment" (paheko_id)');

        $this->addSql('ALTER TABLE "registration" DROP canceled_at');
    }
}
