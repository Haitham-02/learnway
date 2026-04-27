<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260426110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'UX safety defaults: enforce single current year/term-per-year and add selector/filter indexes.';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $tables = $schemaManager->listTableNames();

        if (in_array('academic_years', $tables, true)) {
            $columns = array_map(
                static fn($column): string => $column->getName(),
                $schemaManager->listTableColumns('academic_years'),
            );
            $indexes = array_keys($schemaManager->listTableIndexes('academic_years'));

            if (!in_array('current_flag', $columns, true)) {
                $this->addSql('ALTER TABLE academic_years ADD current_flag TINYINT GENERATED ALWAYS AS (CASE WHEN is_current = 1 THEN 1 ELSE NULL END) STORED');
            }
            if (!in_array('uniq_academic_years_current_flag', $indexes, true)) {
                $this->addSql('CREATE UNIQUE INDEX uniq_academic_years_current_flag ON academic_years (current_flag)');
            }
        }

        if (in_array('terms', $tables, true)) {
            $columns = array_map(
                static fn($column): string => $column->getName(),
                $schemaManager->listTableColumns('terms'),
            );
            $indexes = array_keys($schemaManager->listTableIndexes('terms'));

            if (!in_array('current_in_year_flag', $columns, true)) {
                $this->addSql('ALTER TABLE terms ADD current_in_year_flag INT GENERATED ALWAYS AS (CASE WHEN is_current = 1 THEN academic_year_id ELSE NULL END) STORED');
            }
            if (!in_array('uniq_terms_current_in_year', $indexes, true)) {
                $this->addSql('CREATE UNIQUE INDEX uniq_terms_current_in_year ON terms (current_in_year_flag)');
            }
            if (!in_array('idx_terms_selector', $indexes, true)) {
                $this->addSql('CREATE INDEX idx_terms_selector ON terms (academic_year_id, is_current, start_date, name)');
            }
        }

        if (in_array('classes', $tables, true)) {
            $indexes = array_keys($schemaManager->listTableIndexes('classes'));
            if (!in_array('idx_classes_selector', $indexes, true)) {
                $this->addSql('CREATE INDEX idx_classes_selector ON classes (is_active, name, grade_level, academic_year_id)');
            }
        }

        if (in_array('subjects', $tables, true)) {
            $indexes = array_keys($schemaManager->listTableIndexes('subjects'));
            if (!in_array('idx_subjects_selector', $indexes, true)) {
                $this->addSql('CREATE INDEX idx_subjects_selector ON subjects (is_active, name)');
            }
        }

        if (in_array('users', $tables, true)) {
            $indexes = array_keys($schemaManager->listTableIndexes('users'));
            if (!in_array('idx_users_selector', $indexes, true)) {
                $this->addSql('CREATE INDEX idx_users_selector ON users (role_id, is_active, last_name, first_name)');
            }
        }

        if (in_array('subject_sections', $tables, true)) {
            $indexes = array_keys($schemaManager->listTableIndexes('subject_sections'));
            if (!in_array('idx_sections_filters', $indexes, true)) {
                $this->addSql('CREATE INDEX idx_sections_filters ON subject_sections (term_id, status, class_id, teacher_id)');
            }
        }

        if (in_array('student_enrollments', $tables, true)) {
            $indexes = array_keys($schemaManager->listTableIndexes('student_enrollments'));
            if (!in_array('idx_enrollments_filters', $indexes, true)) {
                $this->addSql('CREATE INDEX idx_enrollments_filters ON student_enrollments (class_id, left_at, enrolled_at)');
            }
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $tables = $schemaManager->listTableNames();

        if (in_array('student_enrollments', $tables, true)) {
            $indexes = array_keys($schemaManager->listTableIndexes('student_enrollments'));
            if (in_array('idx_enrollments_filters', $indexes, true)) {
                $this->addSql('DROP INDEX idx_enrollments_filters ON student_enrollments');
            }
        }

        if (in_array('subject_sections', $tables, true)) {
            $indexes = array_keys($schemaManager->listTableIndexes('subject_sections'));
            if (in_array('idx_sections_filters', $indexes, true)) {
                $this->addSql('DROP INDEX idx_sections_filters ON subject_sections');
            }
        }

        if (in_array('users', $tables, true)) {
            $indexes = array_keys($schemaManager->listTableIndexes('users'));
            if (in_array('idx_users_selector', $indexes, true)) {
                $this->addSql('DROP INDEX idx_users_selector ON users');
            }
        }

        if (in_array('subjects', $tables, true)) {
            $indexes = array_keys($schemaManager->listTableIndexes('subjects'));
            if (in_array('idx_subjects_selector', $indexes, true)) {
                $this->addSql('DROP INDEX idx_subjects_selector ON subjects');
            }
        }

        if (in_array('classes', $tables, true)) {
            $indexes = array_keys($schemaManager->listTableIndexes('classes'));
            if (in_array('idx_classes_selector', $indexes, true)) {
                $this->addSql('DROP INDEX idx_classes_selector ON classes');
            }
        }

        if (in_array('terms', $tables, true)) {
            $indexes = array_keys($schemaManager->listTableIndexes('terms'));
            $columns = array_map(
                static fn($column): string => $column->getName(),
                $schemaManager->listTableColumns('terms'),
            );

            if (in_array('idx_terms_selector', $indexes, true)) {
                $this->addSql('DROP INDEX idx_terms_selector ON terms');
            }
            if (in_array('uniq_terms_current_in_year', $indexes, true)) {
                $this->addSql('DROP INDEX uniq_terms_current_in_year ON terms');
            }
            if (in_array('current_in_year_flag', $columns, true)) {
                $this->addSql('ALTER TABLE terms DROP COLUMN current_in_year_flag');
            }
        }

        if (in_array('academic_years', $tables, true)) {
            $indexes = array_keys($schemaManager->listTableIndexes('academic_years'));
            $columns = array_map(
                static fn($column): string => $column->getName(),
                $schemaManager->listTableColumns('academic_years'),
            );

            if (in_array('uniq_academic_years_current_flag', $indexes, true)) {
                $this->addSql('DROP INDEX uniq_academic_years_current_flag ON academic_years');
            }
            if (in_array('current_flag', $columns, true)) {
                $this->addSql('ALTER TABLE academic_years DROP COLUMN current_flag');
            }
        }
    }
}
