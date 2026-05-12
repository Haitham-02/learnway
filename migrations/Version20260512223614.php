<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260512223614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subjects DROP FOREIGN KEY `FK_SUBJECTS_TERM`');
        $this->addSql('ALTER TABLE subjects ADD CONSTRAINT FK_AB259917E2C35FC FOREIGN KEY (term_id) REFERENCES terms (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subjects DROP FOREIGN KEY FK_AB259917E2C35FC');
        $this->addSql('ALTER TABLE subjects ADD CONSTRAINT `FK_SUBJECTS_TERM` FOREIGN KEY (term_id) REFERENCES terms (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
