<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216005359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "event_picture" (id UUID NOT NULL, path VARCHAR(255) NOT NULL, original_file_name VARCHAR(255) NOT NULL, size INT DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_id UUID NOT NULL, event_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_938CE626A76ED395 ON "event_picture" (user_id)');
        $this->addSql('CREATE INDEX IDX_938CE62671F7E88B ON "event_picture" (event_id)');
        $this->addSql('CREATE INDEX idx_event_picture_path ON "event_picture" (path)');
        $this->addSql('COMMENT ON COLUMN "event_picture".path IS \'This is the path on ObjectStorage.\'');
        $this->addSql('ALTER TABLE "event_picture" ADD CONSTRAINT FK_938CE626A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "event_picture" ADD CONSTRAINT FK_938CE62671F7E88B FOREIGN KEY (event_id) REFERENCES "event" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE "uploaded_file" RENAME TO "uploaded_image"');

        $this->addSql('ALTER TABLE alternative_pictures DROP CONSTRAINT fk_c47beef393cb796c');
        $this->addSql('ALTER TABLE alternative_pictures ADD CONSTRAINT FK_C47BEEF393CB796C FOREIGN KEY (file_id) REFERENCES "uploaded_image" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA7276973A0');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7276973A0 FOREIGN KEY (uploaded_file_id) REFERENCES "uploaded_image" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
    }
}
