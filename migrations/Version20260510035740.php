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
        $schemaManager = $this->connection->createSchemaManager();
        $tables = $schemaManager->listTableNames();

        if (in_array('forum_reviews', $tables, true)) {
            $indexes = array_change_key_case(array_fill_keys(array_keys($schemaManager->listTableIndexes('forum_reviews')), true), CASE_LOWER);
            if (isset($indexes['idx_98bca67ecb944f1a']) && !isset($indexes['uniq_98bca67ecb944f1a'])) {
                $this->addSql('ALTER TABLE forum_reviews DROP INDEX IDX_98BCA67ECB944F1A, ADD UNIQUE INDEX UNIQ_98BCA67ECB944F1A (student_id)');
            }
        }

        if (in_array('messages', $tables, true)) {
            $columns = array_map(
                static fn($column): string => $column->getName(),
                $schemaManager->listTableColumns('messages'),
            );

            if (!in_array('edited_at', $columns, true)) {
                $this->addSql('ALTER TABLE messages ADD edited_at DATETIME DEFAULT NULL');
            }

            if (!in_array('is_forwarded', $columns, true)) {
                $this->addSql('ALTER TABLE messages ADD is_forwarded TINYINT DEFAULT 0 NOT NULL');
            }
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $tables = $schemaManager->listTableNames();

        if (in_array('forum_reviews', $tables, true)) {
            $indexes = array_change_key_case(array_fill_keys(array_keys($schemaManager->listTableIndexes('forum_reviews')), true), CASE_LOWER);
            if (isset($indexes['uniq_98bca67ecb944f1a']) && !isset($indexes['idx_98bca67ecb944f1a'])) {
                $this->addSql('ALTER TABLE forum_reviews DROP INDEX UNIQ_98BCA67ECB944F1A, ADD INDEX IDX_98BCA67ECB944F1A (student_id)');
            }
        }

        if (in_array('messages', $tables, true)) {
            $columns = array_map(
                static fn($column): string => $column->getName(),
                $schemaManager->listTableColumns('messages'),
            );

            if (in_array('edited_at', $columns, true)) {
                $this->addSql('ALTER TABLE messages DROP edited_at');
            }

            if (in_array('is_forwarded', $columns, true)) {
                $this->addSql('ALTER TABLE messages DROP is_forwarded');
            }
        }
    }
}
