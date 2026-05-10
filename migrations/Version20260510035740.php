<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260510035740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forum_reviews DROP INDEX IDX_98BCA67ECB944F1A, ADD UNIQUE INDEX UNIQ_98BCA67ECB944F1A (student_id)');
        $this->addSql('ALTER TABLE messages ADD edited_at DATETIME DEFAULT NULL, ADD is_forwarded TINYINT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forum_reviews DROP INDEX UNIQ_98BCA67ECB944F1A, ADD INDEX IDX_98BCA67ECB944F1A (student_id)');
        $this->addSql('ALTER TABLE messages DROP edited_at, DROP is_forwarded');
    }
}
