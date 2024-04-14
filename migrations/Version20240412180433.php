<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240412180433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "question" (id UUID NOT NULL, slug VARCHAR(255) NOT NULL, question VARCHAR(255) NOT NULL, answer TEXT NOT NULL, locked BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B6F7494E989D9B62 ON "question" (slug)');
        $this->addSql('CREATE INDEX idx_question_slug ON "question" (slug)');
        $this->addSql('COMMENT ON COLUMN "question".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "question".locked IS \'Questions used outside of the FAQ are locked and cannot be removed.\'');
        $this->addSql('COMMENT ON COLUMN "question".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "question".updated_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql("INSERT INTO public.question (id, slug, question, answer, locked, created_at, updated_at) VALUES ('d9667d7e-9e1c-41b0-9651-54bcc5dede7c', 'quest-ce-quun-altertour', 'Qu''est-ce qu''un AlterTour ?', '<div>L''AlterTour (ou AT) est <strong>un tour participatif à vélo à la rencontre d''alternatives</strong>.&nbsp;</div><div><br></div><div>Il est accessible sans condition d''âge ou de niveau sportif, <strong>deux véhicules motorisés nous secondent</strong> pour transporter le matériel logistique (nourriture, cuisine, hygiène ...), vélos et altercyclistes fatigués.&nbsp;</div><div><br>C''est <strong>un évènement participatif</strong> car chacun·e prend part aux tâches quotidiennes, chacun·e est invité à partager ses talents et chacun·e peut donner son avis et prendre part aux décisions.</div>', true, '2024-04-12 18:28:45', '2024-04-12 18:28:45')");
        $this->addSql("INSERT INTO public.question (id, slug, question, answer, locked, created_at, updated_at) VALUES ('e73d96c6-ef15-488b-886b-c9d835b04fec', 'quest-ce-quune-echappe-belle', 'Qu''est-ce qu''une Échappé Belle ?', '<div>Une échappée belle est un évènement un peu différent de l''AlterTour.</div>', true, '2024-04-14 11:53:45', '2024-04-14 11:53:46')");
        $this->addSql("INSERT INTO public.question (id, slug, question, answer, locked, created_at, updated_at) VALUES ('fdf62e2a-408d-4b34-a827-0f58a100ee0f', 'quest-ce-quun-alter-d-tour', 'Qu''est-ce qu''un Alter-D-Tour ?', '<div>Un Alter-D-Tour...</div>', true, '2024-04-14 11:54:15', '2024-04-14 11:54:15')");
        $this->addSql("INSERT INTO public.question (id, slug, question, answer, locked, created_at, updated_at) VALUES ('4fde9a33-8b04-4fbc-9f0a-034902e51d8b', 'quest-ce-quun-bieretour', 'Qu''est-ce qu''un BièreTour ?', '<div>Un BièreTour, ...</div>', true, '2024-04-14 11:54:35', '2024-04-14 11:54:35')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE "question"');
    }
}
