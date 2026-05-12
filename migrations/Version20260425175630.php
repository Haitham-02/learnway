<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260425175630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename class_enrollments to student_enrollments, enforce student-only enrollment, and add teacher assignment history fields to subject_sections.';
    }

    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $tables = $schemaManager->listTableNames();
        $platform = $this->connection->getDatabasePlatform();
        $platformName = $platform->getName();

        // Handle table rename (SQLite vs MySQL)
        if (in_array('class_enrollments', $tables, true) && !in_array('student_enrollments', $tables, true)) {
            if ($platformName === 'sqlite') {
                // SQLite doesn't support RENAME TABLE directly, use ALTER TABLE
                $this->addSql('ALTER TABLE class_enrollments RENAME TO student_enrollments');
            } else {
                $this->addSql('RENAME TABLE class_enrollments TO student_enrollments');
            }
        }

        $tables = $schemaManager->listTableNames();
        if (in_array('subject_sections', $tables, true)) {
            $columns = array_map(
                static fn($column): string => $column->getName(),
                $schemaManager->listTableColumns('subject_sections'),
            );

            if (!in_array('assigned_at', $columns, true)) {
                $this->addSql('ALTER TABLE subject_sections ADD assigned_at DATETIME DEFAULT NULL');
                $this->addSql('UPDATE subject_sections SET assigned_at = created_at WHERE assigned_at IS NULL');
            }

            if (!in_array('ended_at', $columns, true)) {
                $this->addSql('ALTER TABLE subject_sections ADD ended_at DATETIME DEFAULT NULL');
            }
        }

        // Only create MySQL triggers for MySQL databases
        if ($platformName !== 'sqlite' && in_array('student_enrollments', $tables, true)) {
            $this->addSql('DROP TRIGGER IF EXISTS trg_student_enrollments_student_only_insert');
            $this->addSql('DROP TRIGGER IF EXISTS trg_student_enrollments_student_only_update');

            $this->addSql(
                "CREATE TRIGGER trg_student_enrollments_student_only_insert
                BEFORE INSERT ON student_enrollments
                FOR EACH ROW
                BEGIN
                    DECLARE v_role_name VARCHAR(255);
                    SELECT UPPER(r.name) INTO v_role_name
                    FROM users u
                    LEFT JOIN roles r ON r.id = u.role_id
                    WHERE u.id = NEW.user_id
                    LIMIT 1;

                    IF v_role_name IS NULL OR v_role_name <> 'STUDENT' THEN
                        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Only users with STUDENT role can be enrolled';
                    END IF;
                END",
            );

            $this->addSql(
                "CREATE TRIGGER trg_student_enrollments_student_only_update
                BEFORE UPDATE ON student_enrollments
                FOR EACH ROW
                BEGIN
                    DECLARE v_role_name VARCHAR(255);
                    SELECT UPPER(r.name) INTO v_role_name
                    FROM users u
                    LEFT JOIN roles r ON r.id = u.role_id
                    WHERE u.id = NEW.user_id
                    LIMIT 1;

                    IF v_role_name IS NULL OR v_role_name <> 'STUDENT' THEN
                        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Only users with STUDENT role can be enrolled';
                    END IF;
                END",
            );
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $tables = $schemaManager->listTableNames();

        if (in_array('student_enrollments', $tables, true)) {
            $this->addSql('DROP TRIGGER IF EXISTS trg_student_enrollments_student_only_insert');
            $this->addSql('DROP TRIGGER IF EXISTS trg_student_enrollments_student_only_update');
        }

        if (in_array('subject_sections', $tables, true)) {
            $columns = array_map(
                static fn($column): string => $column->getName(),
                $schemaManager->listTableColumns('subject_sections'),
            );

            if (in_array('assigned_at', $columns, true)) {
                $this->addSql('ALTER TABLE subject_sections DROP COLUMN assigned_at');
            }

            if (in_array('ended_at', $columns, true)) {
                $this->addSql('ALTER TABLE subject_sections DROP COLUMN ended_at');
            }
        }

        $tables = $schemaManager->listTableNames();
        if (in_array('student_enrollments', $tables, true) && !in_array('class_enrollments', $tables, true)) {
            $this->addSql('RENAME TABLE student_enrollments TO class_enrollments');
        }
    }
}
