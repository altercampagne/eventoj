<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240206230842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registration ALTER user_id SET NOT NULL');
        $this->addSql('ALTER TABLE registration ALTER event_id SET NOT NULL');
        $this->addSql('ALTER TABLE stage ALTER event_id SET NOT NULL');
        $this->addSql('ALTER TABLE stages_alternatives ALTER stage_id SET NOT NULL');
        $this->addSql('ALTER TABLE stages_alternatives ALTER alternative_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "stages_alternatives" ALTER stage_id DROP NOT NULL');
        $this->addSql('ALTER TABLE "stages_alternatives" ALTER alternative_id DROP NOT NULL');
        $this->addSql('ALTER TABLE "registration" ALTER user_id DROP NOT NULL');
        $this->addSql('ALTER TABLE "registration" ALTER event_id DROP NOT NULL');
        $this->addSql('ALTER TABLE "stage" ALTER event_id DROP NOT NULL');
    }
}
