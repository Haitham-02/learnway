<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260505_CreateLivestream extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create livestream tables for video sessions';
    }

    public function up(Schema $schema): void
    {
        // Create livestreams table
        $this->addSql('CREATE TABLE livestreams (
            id BIGINT AUTO_INCREMENT NOT NULL,
            teacher_id BIGINT DEFAULT NULL,
            class_id INT DEFAULT NULL,
            title VARCHAR(255) NOT NULL,
            description LONGTEXT,
            meeting_room VARCHAR(255) NOT NULL,
            scheduled_at DATETIME DEFAULT NULL,
            started_at DATETIME DEFAULT NULL,
            ended_at DATETIME DEFAULT NULL,
            status VARCHAR(50) NOT NULL DEFAULT "SCHEDULED",
            recording_url VARCHAR(255) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            INDEX IDX_LIVESTREAMS_TEACHER (teacher_id),
            INDEX IDX_LIVESTREAMS_CLASS (class_id),
            CONSTRAINT FK_LIVESTREAMS_TEACHER FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
            CONSTRAINT FK_LIVESTREAMS_CLASS FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        // Create livestream_participants table
        $this->addSql('CREATE TABLE livestream_participants (
            id BIGINT AUTO_INCREMENT NOT NULL,
            livestream_id BIGINT DEFAULT NULL,
            user_id BIGINT DEFAULT NULL,
            role VARCHAR(50) NOT NULL DEFAULT "STUDENT",
            joined_at DATETIME NOT NULL,
            left_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            INDEX IDX_PARTICIPANTS_LIVESTREAM (livestream_id),
            INDEX IDX_PARTICIPANTS_USER (user_id),
            UNIQUE KEY UNIQ_PARTICIPANT_SESSION (livestream_id, user_id),
            CONSTRAINT FK_PARTICIPANTS_LIVESTREAM FOREIGN KEY (livestream_id) REFERENCES livestreams(id) ON DELETE CASCADE,
            CONSTRAINT FK_PARTICIPANTS_USER FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        // Create livestream_qa table
        $this->addSql('CREATE TABLE livestream_qa (
            id BIGINT AUTO_INCREMENT NOT NULL,
            livestream_id BIGINT DEFAULT NULL,
            student_id BIGINT DEFAULT NULL,
            question LONGTEXT NOT NULL,
            answer LONGTEXT DEFAULT NULL,
            answered_by BIGINT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            answered_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id),
            INDEX IDX_QA_LIVESTREAM (livestream_id),
            INDEX IDX_QA_STUDENT (student_id),
            INDEX IDX_QA_ANSWERED_BY (answered_by),
            CONSTRAINT FK_QA_LIVESTREAM FOREIGN KEY (livestream_id) REFERENCES livestreams(id) ON DELETE CASCADE,
            CONSTRAINT FK_QA_STUDENT FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
            CONSTRAINT FK_QA_ANSWERED_BY FOREIGN KEY (answered_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        // Create facial_analysis table
        $this->addSql('CREATE TABLE facial_analysis (
            id BIGINT AUTO_INCREMENT NOT NULL,
            livestream_id BIGINT DEFAULT NULL,
            student_id BIGINT DEFAULT NULL,
            emotion VARCHAR(50) NOT NULL,
            confidence DECIMAL(5,4) NOT NULL,
            additional_data JSON DEFAULT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            INDEX IDX_FACIAL_LIVESTREAM (livestream_id),
            INDEX IDX_FACIAL_STUDENT (student_id),
            INDEX IDX_FACIAL_EMOTION (emotion),
            CONSTRAINT FK_FACIAL_LIVESTREAM FOREIGN KEY (livestream_id) REFERENCES livestreams(id) ON DELETE CASCADE,
            CONSTRAINT FK_FACIAL_STUDENT FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        // Create livestream_chats table
        $this->addSql('CREATE TABLE livestream_chats (
            id BIGINT AUTO_INCREMENT NOT NULL,
            livestream_id BIGINT DEFAULT NULL,
            user_id BIGINT DEFAULT NULL,
            message LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            INDEX IDX_CHAT_LIVESTREAM (livestream_id),
            INDEX IDX_CHAT_USER (user_id),
            CONSTRAINT FK_CHAT_LIVESTREAM FOREIGN KEY (livestream_id) REFERENCES livestreams(id) ON DELETE CASCADE,
            CONSTRAINT FK_CHAT_USER FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE livestream_chats');
        $this->addSql('DROP TABLE facial_analysis');
        $this->addSql('DROP TABLE livestream_qa');
        $this->addSql('DROP TABLE livestream_participants');
        $this->addSql('DROP TABLE livestreams');
    }
}
