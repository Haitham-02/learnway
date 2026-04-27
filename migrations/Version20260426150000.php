<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260426150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove subject_sections: migrate chapters to link Class + Subject directly, drop subject_sections, and add grade_level to subjects.';
    }

    public function isTransactional(): bool
    {
        // DDL on MySQL is not transactional; we also want partial progress to survive if possible.
        return false;
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $tables = $schemaManager->listTableNames();

        // --- 1. Chapters: add class_id / subject_id, backfill from subject_sections, drop section_id ---
        if (in_array('chapters', $tables, true)) {
            $columns = array_map(
                static fn($column): string => $column->getName(),
                $schemaManager->listTableColumns('chapters'),
            );
            $fkNames = array_map(
                static fn($fk): string => $fk->getName(),
                $schemaManager->listTableForeignKeys('chapters'),
            );
            $indexes = array_keys($schemaManager->listTableIndexes('chapters'));

            if (!in_array('class_id', $columns, true)) {
                $this->addSql('ALTER TABLE chapters ADD class_id INT DEFAULT NULL');
            }
            if (!in_array('subject_id', $columns, true)) {
                $this->addSql('ALTER TABLE chapters ADD subject_id INT DEFAULT NULL');
            }

            // Backfill from subject_sections if possible.
            if (in_array('section_id', $columns, true) && in_array('subject_sections', $tables, true)) {
                $this->addSql(
                    'UPDATE chapters c
                     INNER JOIN subject_sections s ON s.id = c.section_id
                     SET c.class_id = s.class_id, c.subject_id = s.subject_id
                     WHERE c.class_id IS NULL OR c.subject_id IS NULL'
                );
            }

            // Drop FK and index on section_id before dropping the column.
            foreach ($fkNames as $fkName) {
                if ($fkName === 'chapters_ibfk_1') {
                    $this->addSql('ALTER TABLE chapters DROP FOREIGN KEY chapters_ibfk_1');
                }
            }
            if (in_array('idx_chapters_section', $indexes, true)) {
                $this->addSql('DROP INDEX idx_chapters_section ON chapters');
            }
            if (in_array('section_id', $columns, true)) {
                $this->addSql('ALTER TABLE chapters DROP COLUMN section_id');
            }

            // Add FKs for class_id / subject_id if missing.
            $fkNames = array_map(
                static fn($fk): string => $fk->getName(),
                $schemaManager->listTableForeignKeys('chapters'),
            );
            $fkColumns = [];
            foreach ($schemaManager->listTableForeignKeys('chapters') as $fk) {
                $fkColumns[$fk->getName()] = $fk->getLocalColumns();
            }

            $hasClassFk = false;
            $hasSubjectFk = false;
            foreach ($fkColumns as $cols) {
                if ($cols === ['class_id']) {
                    $hasClassFk = true;
                }
                if ($cols === ['subject_id']) {
                    $hasSubjectFk = true;
                }
            }
            if (!$hasClassFk && in_array('classes', $tables, true)) {
                $this->addSql('ALTER TABLE chapters ADD CONSTRAINT fk_chapters_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL');
            }
            if (!$hasSubjectFk && in_array('subjects', $tables, true)) {
                $this->addSql('ALTER TABLE chapters ADD CONSTRAINT fk_chapters_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL');
            }

            // Helpful index for listing chapters per class/subject.
            $indexes = array_keys($schemaManager->listTableIndexes('chapters'));
            if (!in_array('idx_chapters_class_subject', $indexes, true)) {
                $this->addSql('CREATE INDEX idx_chapters_class_subject ON chapters (class_id, subject_id, sort_order)');
            }
        }

        // --- 2. Drop subject_sections ---
        $tables = $schemaManager->listTableNames();
        if (in_array('subject_sections', $tables, true)) {
            $this->addSql('DROP TABLE subject_sections');
        }

        // --- 3. Subjects: add grade_level + swap selector index ---
        if (in_array('subjects', $tables, true)) {
            $columns = array_map(
                static fn($column): string => $column->getName(),
                $schemaManager->listTableColumns('subjects'),
            );
            if (!in_array('grade_level', $columns, true)) {
                $this->addSql('ALTER TABLE subjects ADD grade_level VARCHAR(255) DEFAULT NULL');
            }

            $indexes = array_keys($schemaManager->listTableIndexes('subjects'));
            if (in_array('idx_subjects_selector', $indexes, true)) {
                $this->addSql('DROP INDEX idx_subjects_selector ON subjects');
            }
            if (!in_array('idx_subjects_selector_v2', $indexes, true)) {
                $this->addSql('CREATE INDEX idx_subjects_selector_v2 ON subjects (is_active, grade_level, name)');
            }
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $tables = $schemaManager->listTableNames();

        // Restore the subjects index and drop grade_level.
        if (in_array('subjects', $tables, true)) {
            $indexes = array_keys($schemaManager->listTableIndexes('subjects'));
            if (in_array('idx_subjects_selector_v2', $indexes, true)) {
                $this->addSql('DROP INDEX idx_subjects_selector_v2 ON subjects');
            }
            if (!in_array('idx_subjects_selector', $indexes, true)) {
                $this->addSql('CREATE INDEX idx_subjects_selector ON subjects (is_active, name)');
            }

            $columns = array_map(
                static fn($column): string => $column->getName(),
                $schemaManager->listTableColumns('subjects'),
            );
            if (in_array('grade_level', $columns, true)) {
                $this->addSql('ALTER TABLE subjects DROP COLUMN grade_level');
            }
        }

        // NOTE: subject_sections is NOT recreated here (restore from backup if you need it).
        // Chapter class_id/subject_id columns are left in place on down(); reverting the
        // schema change would require recreating subject_sections and the section_id FK,
        // which is out of scope for this migration.
    }
}
