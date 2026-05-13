<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260511110924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $tables = $schemaManager->listTableNames();

        if (!in_array('schedule_change_requests', $tables, true)) {
            $this->addSql('CREATE TABLE schedule_change_requests (id INT AUTO_INCREMENT NOT NULL, proposed_day_of_week VARCHAR(20) NOT NULL, reason LONGTEXT DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, teacher_id BIGINT NOT NULL, class_schedule_id INT NOT NULL, proposed_time_slot_id INT NOT NULL, INDEX IDX_2627177141807E1D (teacher_id), INDEX IDX_262717719C650DE3 (class_schedule_id), INDEX IDX_2627177172F206FB (proposed_time_slot_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
            $this->addSql('ALTER TABLE schedule_change_requests ADD CONSTRAINT FK_2627177141807E1D FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE schedule_change_requests ADD CONSTRAINT FK_262717719C650DE3 FOREIGN KEY (class_schedule_id) REFERENCES class_schedules (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE schedule_change_requests ADD CONSTRAINT FK_2627177172F206FB FOREIGN KEY (proposed_time_slot_id) REFERENCES time_slots (id) ON DELETE CASCADE');
        }

        if (in_array('forum_reviews', $tables, true)) {
            $indexes = array_change_key_case(array_fill_keys(array_keys($schemaManager->listTableIndexes('forum_reviews')), true), CASE_LOWER);
            if (isset($indexes['uniq_98bca67ecb944f1a']) && !isset($indexes['idx_98bca67ecb944f1a'])) {
                $this->addSql('ALTER TABLE forum_reviews DROP INDEX UNIQ_98BCA67ECB944F1A, ADD INDEX IDX_98BCA67ECB944F1A (student_id)');
            }
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $tables = $schemaManager->listTableNames();

        if (in_array('schedule_change_requests', $tables, true)) {
            $this->addSql('DROP TABLE schedule_change_requests');
        }

        if (in_array('forum_reviews', $tables, true)) {
            $indexes = array_change_key_case(array_fill_keys(array_keys($schemaManager->listTableIndexes('forum_reviews')), true), CASE_LOWER);
            if (isset($indexes['idx_98bca67ecb944f1a']) && !isset($indexes['uniq_98bca67ecb944f1a'])) {
                $this->addSql('ALTER TABLE forum_reviews DROP INDEX IDX_98BCA67ECB944F1A, ADD UNIQUE INDEX UNIQ_98BCA67ECB944F1A (student_id)');
            }
        }
    }
}
