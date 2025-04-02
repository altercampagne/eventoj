<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250328103602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment ADD instalments INT NOT NULL DEFAULT 1');
        $this->addSql('ALTER TABLE payment ALTER instalments DROP DEFAULT');
        $this->addSql(<<<'SQL'
                ALTER TABLE payment DROP refunded_amount
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "payment" DROP instalments');
        $this->addSql(<<<'SQL'
                ALTER TABLE "payment" ADD refunded_amount INT DEFAULT NULL
            SQL);
        $this->addSql(<<<'SQL'
                COMMENT ON COLUMN "payment".refunded_amount IS 'The refunded amount for this payment'
            SQL);
    }
}
