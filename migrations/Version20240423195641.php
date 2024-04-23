<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240423195641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO public.question (id, slug, question, answer, locked, created_at, updated_at, category) VALUES ('d3f5ad0e-84de-4e09-9909-0c6a35119b86', 'pourquoi-adherer-a-lassociation', 'Pourquoi adhérer à l''association ?', '<div>Adhérer c''est bien !!</div>', true, '2024-04-12 18:28:45', '2024-04-12 18:28:45', '2price') ON CONFLICT DO NOTHING");
        $this->addSql("UPDATE public.question SET locked = true WHERE slug = 'pourquoi-adherer-a-lassociation'");

        $this->addSql('ALTER TABLE payment ALTER registration_id DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "payment" ALTER registration_id SET NOT NULL');
    }
}
