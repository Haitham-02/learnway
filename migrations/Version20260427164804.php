<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260427164804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announcements ADD target_value VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE chapter_items CHANGE url url LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE messages RENAME INDEX fk_db021e969ac0396 TO IDX_DB021E969AC0396');
        $this->addSql('ALTER TABLE submissions DROP FOREIGN KEY `FK_3F6169F7D19302F8`');
        $this->addSql('ALTER TABLE submissions ADD CONSTRAINT FK_3F6169F7D19302F8 FOREIGN KEY (assignment_id) REFERENCES assignments (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE submissions RENAME INDEX idx_submission_assignment TO IDX_3F6169F7D19302F8');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announcements DROP target_value');
        $this->addSql('ALTER TABLE chapter_items CHANGE url url TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE messages RENAME INDEX idx_db021e969ac0396 TO FK_DB021E969AC0396');
        $this->addSql('ALTER TABLE submissions DROP FOREIGN KEY FK_3F6169F7D19302F8');
        $this->addSql('ALTER TABLE submissions ADD CONSTRAINT `FK_3F6169F7D19302F8` FOREIGN KEY (assignment_id) REFERENCES assignments (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE submissions RENAME INDEX idx_3f6169f7d19302f8 TO IDX_SUBMISSION_ASSIGNMENT');
    }
}
