<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260427153724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Superseded by Version20260427164804; intentionally no-op for legacy compatibility.';
    }

    public function up(Schema $schema): void
    {
<<<<<<< HEAD
        // Skip for SQLite - contains MySQL-specific ALTER TABLE CHANGE syntax
        $platform = $this->connection->getDatabasePlatform();
        if ($platform->getName() === 'sqlite') {
            return;
        }

        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapter_items CHANGE url url LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE messages RENAME INDEX fk_db021e969ac0396 TO IDX_DB021E969AC0396');
        $this->addSql('ALTER TABLE submissions DROP FOREIGN KEY `FK_3F6169F7D19302F8`');
        $this->addSql('ALTER TABLE submissions ADD CONSTRAINT FK_3F6169F7D19302F8 FOREIGN KEY (assignment_id) REFERENCES assignments (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE submissions RENAME INDEX idx_submission_assignment TO IDX_3F6169F7D19302F8');
=======
        // Intentionally left blank.
>>>>>>> a68a05d89020c09f6487d63477940c12fd0e8657
    }

    public function down(Schema $schema): void
    {
        // Intentionally left blank.
    }
}
