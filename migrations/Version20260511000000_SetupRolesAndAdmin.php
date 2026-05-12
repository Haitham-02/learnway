<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Setup default roles and admin account
 */
final class Version20260511000000_SetupRolesAndAdmin extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create default roles (ADMIN, TEACHER, STUDENT) and setup admin user account';
    }

    public function up(Schema $schema): void
    {
        // Setup roles
        $this->addSql("INSERT OR IGNORE INTO roles (id, name, role_category, description) VALUES (1, 'ADMIN', 'Administration', 'System Administrator')");
        $this->addSql("INSERT OR IGNORE INTO roles (id, name, role_category, description) VALUES (2, 'TEACHER', 'Academic', 'Teacher')");
        $this->addSql("INSERT OR IGNORE INTO roles (id, name, role_category, description) VALUES (3, 'STUDENT', 'Academic', 'Student')");

        // Setup admin user with password: Admin@123
        // Hash: $2y$12$EixZaYVK1fsbw1ZfbX3OzeIUxvLiRFIDxtIZLC2NC/sSaP46d2XUi
        $this->addSql("INSERT OR IGNORE INTO users (role_id, email, password_hash, first_name, last_name, is_active, created_at) VALUES (1, 'admin@learnway.com', '\$2y\$12\$EixZaYVK1fsbw1ZfbX3OzeIUxvLiRFIDxtIZLC2NC/sSaP46d2XUi', 'Admin', 'User', 1, datetime('now'))");
    }

    public function down(Schema $schema): void
    {
        // Don't delete roles and admin on rollback - they're data, not schema
        // If you really want to remove them:
        // $this->addSql("DELETE FROM users WHERE email = 'admin@learnway.com'");
        // $this->addSql("DELETE FROM roles WHERE name IN ('ADMIN', 'TEACHER', 'STUDENT')");
    }
}


