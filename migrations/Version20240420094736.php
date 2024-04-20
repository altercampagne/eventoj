<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240420094736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE question set category = \'2price\' where category = \'cat1\'');
        $this->addSql('UPDATE question set category = \'6daily_life\' where category = \'cat2\'');
        $this->addSql('UPDATE question set category = \'4children\' where category = \'cat3\'');
        $this->addSql('UPDATE question set category = \'1registration\' where category = \'cat4\'');
        $this->addSql('UPDATE question set category = \'7join_the_tour\' where category = \'cat5\'');
        $this->addSql('UPDATE question set category = \'5stages\' where category = \'cat6\'');
        $this->addSql('UPDATE question set category = \'3cancelation\' where category = \'cat7\'');
        $this->addSql('UPDATE question set category = \'8bikes\' where category = \'cat8\'');
        $this->addSql('UPDATE question set category = \'9glossary\' where category = \'general\'');
    }

    public function down(Schema $schema): void
    {
    }
}
