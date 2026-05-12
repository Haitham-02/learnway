<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260425180120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add DB triggers to enforce student-only rows in student_enrollments.';
    }

    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        $tables = $this->connection->createSchemaManager()->listTableNames();
        if (!in_array('student_enrollments', $tables, true)) {
            return;
        }

        $platform = $this->connection->getDatabasePlatform();
        $platformName = $platform->getName();

        // Only create MySQL triggers for MySQL databases (skip for SQLite)
        if ($platformName === 'sqlite') {
            return;
        }

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

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TRIGGER IF EXISTS trg_student_enrollments_student_only_insert');
        $this->addSql('DROP TRIGGER IF EXISTS trg_student_enrollments_student_only_update');
    }
}
