<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260426162553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
<<<<<<< HEAD
        // Skip for SQLite - contains MySQL-specific ALTER TABLE CHANGE syntax
        $platform = $this->connection->getDatabasePlatform();
        if ($platform->getName() === 'sqlite') {
            return;
        }

=======
        $schemaManager = $this->connection->createSchemaManager();
        if (!$schemaManager->tablesExist(['announcements'])) {
            return;
        }

        foreach ($schemaManager->listTableForeignKeys('announcements') as $foreignKey) {
            if (strtoupper($foreignKey->getName()) === 'FK_F422A9DAE36D154') {
                return;
            }
        }

>>>>>>> a68a05d89020c09f6487d63477940c12fd0e8657
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announcements CHANGE posted_by posted_by BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE announcements ADD CONSTRAINT FK_F422A9DAE36D154 FOREIGN KEY (posted_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE announcements RENAME INDEX posted_by TO IDX_F422A9DAE36D154');
        $this->addSql('ALTER TABLE assignments DROP FOREIGN KEY `assignments_ibfk_1`');
        $this->addSql('DROP INDEX idx_assignments_chapter ON assignments');
        $this->addSql('ALTER TABLE assignments CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE chapter_id chapter_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE submission_type submission_type VARCHAR(255) DEFAULT NULL, CHANGE allow_late_submission allow_late_submission TINYINT DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE assignments ADD CONSTRAINT FK_308A50DD579F4768 FOREIGN KEY (chapter_id) REFERENCES chapters (id)');
        $this->addSql('ALTER TABLE chapter_contents DROP FOREIGN KEY `chapter_contents_ibfk_1`');
        $this->addSql('ALTER TABLE chapter_contents DROP FOREIGN KEY `chapter_contents_ibfk_2`');
        $this->addSql('DROP INDEX item_id ON chapter_contents');
        $this->addSql('ALTER TABLE chapter_contents ADD chapter_id INT DEFAULT NULL, DROP item_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE chapter_contents ADD CONSTRAINT FK_52E37C72579F4768 FOREIGN KEY (chapter_id) REFERENCES chapters (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chapter_contents ADD CONSTRAINT FK_52E37C72DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_52E37C72579F4768 ON chapter_contents (chapter_id)');
        $this->addSql('ALTER TABLE chapter_contents RENAME INDEX created_by TO IDX_52E37C72DE12AB56');
        $this->addSql('ALTER TABLE chapter_files DROP FOREIGN KEY `chapter_files_ibfk_1`');
        $this->addSql('ALTER TABLE chapter_files DROP FOREIGN KEY `chapter_files_ibfk_2`');
        $this->addSql('DROP INDEX item_id ON chapter_files');
        $this->addSql('ALTER TABLE chapter_files ADD chapter_id INT DEFAULT NULL, DROP item_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE file_path file_path VARCHAR(255) NOT NULL, CHANGE file_type file_type VARCHAR(255) DEFAULT NULL, CHANGE uploaded_at uploaded_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE chapter_files ADD CONSTRAINT FK_867CD6E6579F4768 FOREIGN KEY (chapter_id) REFERENCES chapters (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chapter_files ADD CONSTRAINT FK_867CD6E6E3E73126 FOREIGN KEY (uploaded_by) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_867CD6E6579F4768 ON chapter_files (chapter_id)');
        $this->addSql('ALTER TABLE chapter_files RENAME INDEX uploaded_by TO IDX_867CD6E6E3E73126');
        $this->addSql('ALTER TABLE chapter_items DROP FOREIGN KEY `chapter_items_ibfk_1`');
        $this->addSql('DROP INDEX idx_items_chapter ON chapter_items');
        $this->addSql('ALTER TABLE chapter_items CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE chapter_id chapter_id INT DEFAULT NULL, CHANGE type type VARCHAR(255) NOT NULL, CHANGE sort_order sort_order INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chapter_items ADD CONSTRAINT FK_61577FF2579F4768 FOREIGN KEY (chapter_id) REFERENCES chapters (id)');
        $this->addSql('ALTER TABLE chapter_progress DROP INDEX IDX_C4189F43579F4768, ADD UNIQUE INDEX UNIQ_C4189F43579F4768 (chapter_id)');
        $this->addSql('ALTER TABLE chapter_progress DROP FOREIGN KEY `chapter_progress_ibfk_1`');
        $this->addSql('ALTER TABLE chapter_progress DROP FOREIGN KEY `chapter_progress_ibfk_2`');
        $this->addSql('DROP INDEX chapter_id ON chapter_progress');
        $this->addSql('ALTER TABLE chapter_progress CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE chapter_id chapter_id INT DEFAULT NULL, CHANGE student_id student_id BIGINT DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE last_accessed_at last_accessed_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE chapter_progress ADD CONSTRAINT FK_C4189F43579F4768 FOREIGN KEY (chapter_id) REFERENCES chapters (id)');
        $this->addSql('ALTER TABLE chapter_progress ADD CONSTRAINT FK_C4189F43CB944F1A FOREIGN KEY (student_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE chapters DROP FOREIGN KEY `fk_chapters_subject`');
        $this->addSql('DROP INDEX idx_chapters_class_subject ON chapters');
        $this->addSql('ALTER TABLE chapters CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE title title VARCHAR(255) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE sort_order sort_order INT DEFAULT NULL, CHANGE is_published is_published TINYINT DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE chapters ADD CONSTRAINT FK_C721437123EDC87 FOREIGN KEY (subject_id) REFERENCES subjects (id)');
        $this->addSql('ALTER TABLE chapters RENAME INDEX fk_chapters_subject TO IDX_C721437123EDC87');
        $this->addSql('DROP INDEX idx_classes_selector ON classes');
        $this->addSql('ALTER TABLE classes CHANGE name name VARCHAR(255) NOT NULL, CHANGE grade_level grade_level VARCHAR(255) NOT NULL, CHANGE section section VARCHAR(255) DEFAULT NULL, CHANGE is_active is_active TINYINT DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE conversation_members DROP INDEX idx_members_user, ADD UNIQUE INDEX UNIQ_DEF6DCF5A76ED395 (user_id)');
        $this->addSql('ALTER TABLE conversation_members DROP INDEX IDX_DEF6DCF59AC0396, ADD UNIQUE INDEX UNIQ_DEF6DCF59AC0396 (conversation_id)');
        $this->addSql('ALTER TABLE conversation_members DROP FOREIGN KEY `conversation_members_ibfk_1`');
        $this->addSql('ALTER TABLE conversation_members DROP FOREIGN KEY `conversation_members_ibfk_2`');
        $this->addSql('DROP INDEX conversation_id ON conversation_members');
        $this->addSql('ALTER TABLE conversation_members CHANGE conversation_id conversation_id BIGINT DEFAULT NULL, CHANGE user_id user_id BIGINT DEFAULT NULL, CHANGE role role VARCHAR(255) DEFAULT NULL, CHANGE joined_at joined_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE conversation_members ADD CONSTRAINT FK_DEF6DCF59AC0396 FOREIGN KEY (conversation_id) REFERENCES conversations (id)');
        $this->addSql('ALTER TABLE conversation_members ADD CONSTRAINT FK_DEF6DCF5A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY `conversations_ibfk_1`');
        $this->addSql('DROP INDEX pair_hash ON conversations');
        $this->addSql('ALTER TABLE conversations CHANGE type type VARCHAR(255) NOT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE pair_hash pair_hash VARCHAR(255) DEFAULT NULL, CHANGE created_by created_by BIGINT DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF1DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE conversations RENAME INDEX created_by TO IDX_C2521BF1DE12AB56');
        $this->addSql('ALTER TABLE forum_comments DROP FOREIGN KEY `fk_fw_comments_parent`');
        $this->addSql('ALTER TABLE forum_comments DROP FOREIGN KEY `fk_fw_comments_post`');
        $this->addSql('ALTER TABLE forum_comments DROP FOREIGN KEY `fk_fw_comments_user`');
        $this->addSql('DROP INDEX sync_uuid ON forum_comments');
        $this->addSql('ALTER TABLE forum_comments CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE post_id post_id INT DEFAULT NULL, CHANGE parent_id parent_id INT DEFAULT NULL, CHANGE student_id student_id BIGINT DEFAULT NULL, CHANGE content content LONGTEXT NOT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE sync_uuid sync_uuid VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE forum_comments ADD CONSTRAINT FK_786D1BCD4B89032C FOREIGN KEY (post_id) REFERENCES forum_posts (id)');
        $this->addSql('ALTER TABLE forum_comments ADD CONSTRAINT FK_786D1BCD727ACA70 FOREIGN KEY (parent_id) REFERENCES forum_comments (id)');
        $this->addSql('ALTER TABLE forum_comments ADD CONSTRAINT FK_786D1BCDCB944F1A FOREIGN KEY (student_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE forum_comments RENAME INDEX idx_fw_comments_post TO IDX_786D1BCD4B89032C');
        $this->addSql('ALTER TABLE forum_comments RENAME INDEX fk_fw_comments_parent TO IDX_786D1BCD727ACA70');
        $this->addSql('ALTER TABLE forum_comments RENAME INDEX idx_fw_comments_user TO IDX_786D1BCDCB944F1A');
        $this->addSql('ALTER TABLE forum_post_attachments DROP FOREIGN KEY `forum_post_attachments_ibfk_1`');
        $this->addSql('ALTER TABLE forum_post_attachments CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE post_id post_id INT DEFAULT NULL, CHANGE file_type file_type VARCHAR(255) DEFAULT NULL, CHANGE uploaded_at uploaded_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE forum_post_attachments ADD CONSTRAINT FK_2BB74F104B89032C FOREIGN KEY (post_id) REFERENCES forum_posts (id)');
        $this->addSql('ALTER TABLE forum_post_attachments RENAME INDEX post_id TO IDX_2BB74F104B89032C');
        $this->addSql('ALTER TABLE forum_posts DROP FOREIGN KEY `fk_fw_posts_user`');
        $this->addSql('ALTER TABLE forum_posts CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE class_id class_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE content content LONGTEXT NOT NULL, CHANGE created_by created_by BIGINT DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE forum_posts ADD CONSTRAINT FK_90291C2DDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE forum_posts RENAME INDEX idx_fw_posts_class TO IDX_90291C2DEA000B10');
        $this->addSql('ALTER TABLE forum_posts RENAME INDEX idx_fw_posts_user TO IDX_90291C2DDE12AB56');
        $this->addSql('ALTER TABLE forum_reviews DROP INDEX idx_fw_reviews_post, ADD UNIQUE INDEX UNIQ_98BCA67E4B89032C (post_id)');
        $this->addSql('ALTER TABLE forum_reviews DROP INDEX idx_fw_reviews_user, ADD UNIQUE INDEX UNIQ_98BCA67ECB944F1A (student_id)');
        $this->addSql('ALTER TABLE forum_reviews DROP FOREIGN KEY `fk_fw_reviews_post`');
        $this->addSql('ALTER TABLE forum_reviews DROP FOREIGN KEY `fk_fw_reviews_user`');
        $this->addSql('DROP INDEX uq_fw_review_unique ON forum_reviews');
        $this->addSql('DROP INDEX sync_uuid ON forum_reviews');
        $this->addSql('ALTER TABLE forum_reviews CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE post_id post_id INT DEFAULT NULL, CHANGE student_id student_id BIGINT DEFAULT NULL, CHANGE review_text review_text LONGTEXT DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE sync_uuid sync_uuid VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE forum_reviews ADD CONSTRAINT FK_98BCA67E4B89032C FOREIGN KEY (post_id) REFERENCES forum_posts (id)');
        $this->addSql('ALTER TABLE forum_reviews ADD CONSTRAINT FK_98BCA67ECB944F1A FOREIGN KEY (student_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY `messages_ibfk_1`');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY `messages_ibfk_2`');
        $this->addSql('DROP INDEX idx_messages_conversation ON messages');
        $this->addSql('ALTER TABLE messages CHANGE conversation_id conversation_id BIGINT DEFAULT NULL, CHANGE sender_id sender_id BIGINT DEFAULT NULL, CHANGE content content LONGTEXT NOT NULL, CHANGE is_deleted is_deleted TINYINT DEFAULT NULL, CHANGE sent_at sent_at DATETIME DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E969AC0396 FOREIGN KEY (conversation_id) REFERENCES conversations (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96F624B39D FOREIGN KEY (sender_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE messages RENAME INDEX sender_id TO IDX_DB021E96F624B39D');
        $this->addSql('DROP INDEX name ON roles');
        $this->addSql('ALTER TABLE roles CHANGE name name VARCHAR(255) NOT NULL, CHANGE role_category role_category VARCHAR(255) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('DROP INDEX subject_code ON subjects');
        $this->addSql('DROP INDEX idx_subjects_selector_v2 ON subjects');
        $this->addSql('ALTER TABLE subjects CHANGE subject_code subject_code VARCHAR(255) NOT NULL, CHANGE name name VARCHAR(255) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE is_active is_active TINYINT DEFAULT NULL');
        $this->addSql('ALTER TABLE subjects RENAME INDEX fk_subjects_term TO IDX_AB259917E2C35FC');
        $this->addSql('ALTER TABLE submission_files DROP FOREIGN KEY `submission_files_ibfk_1`');
        $this->addSql('ALTER TABLE submission_files DROP FOREIGN KEY `submission_files_ibfk_2`');
        $this->addSql('ALTER TABLE submission_files CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE submission_id submission_id INT DEFAULT NULL, CHANGE file_path file_path VARCHAR(255) NOT NULL, CHANGE file_type file_type VARCHAR(255) DEFAULT NULL, CHANGE uploaded_at uploaded_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE submission_files ADD CONSTRAINT FK_DBAA3AFBE1FD4933 FOREIGN KEY (submission_id) REFERENCES submissions (id)');
        $this->addSql('ALTER TABLE submission_files ADD CONSTRAINT FK_DBAA3AFBE3E73126 FOREIGN KEY (uploaded_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE submission_files RENAME INDEX submission_id TO IDX_DBAA3AFBE1FD4933');
        $this->addSql('ALTER TABLE submission_files RENAME INDEX uploaded_by TO IDX_DBAA3AFBE3E73126');
        $this->addSql('ALTER TABLE submissions DROP INDEX IDX_3F6169F7D19302F8, ADD UNIQUE INDEX UNIQ_3F6169F7D19302F8 (assignment_id)');
        $this->addSql('ALTER TABLE submissions DROP FOREIGN KEY `submissions_ibfk_1`');
        $this->addSql('ALTER TABLE submissions DROP FOREIGN KEY `submissions_ibfk_2`');
        $this->addSql('ALTER TABLE submissions DROP FOREIGN KEY `submissions_ibfk_3`');
        $this->addSql('DROP INDEX assignment_id ON submissions');
        $this->addSql('ALTER TABLE submissions CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE assignment_id assignment_id INT DEFAULT NULL, CHANGE student_id student_id BIGINT DEFAULT NULL, CHANGE submission_text submission_text LONGTEXT DEFAULT NULL, CHANGE is_late is_late TINYINT DEFAULT NULL, CHANGE feedback feedback LONGTEXT DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE submissions ADD CONSTRAINT FK_3F6169F7D19302F8 FOREIGN KEY (assignment_id) REFERENCES assignments (id)');
        $this->addSql('ALTER TABLE submissions ADD CONSTRAINT FK_3F6169F7CB944F1A FOREIGN KEY (student_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE submissions ADD CONSTRAINT FK_3F6169F785D7FB47 FOREIGN KEY (reviewed_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE submissions RENAME INDEX idx_submissions_student TO IDX_3F6169F7CB944F1A');
        $this->addSql('ALTER TABLE submissions RENAME INDEX reviewed_by TO IDX_3F6169F785D7FB47');
        $this->addSql('DROP INDEX uniq_terms_current_in_year ON terms');
        $this->addSql('DROP INDEX idx_terms_selector ON terms');
        $this->addSql('ALTER TABLE terms DROP current_in_year_flag, CHANGE academic_year_id academic_year_id INT DEFAULT NULL, CHANGE name name VARCHAR(255) NOT NULL, CHANGE is_current is_current TINYINT DEFAULT NULL');
        $this->addSql('ALTER TABLE terms RENAME INDEX academic_year_id TO IDX_88A23F71C54F3401');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announcements DROP FOREIGN KEY FK_F422A9DAE36D154');
        $this->addSql('ALTER TABLE announcements CHANGE posted_by posted_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE announcements RENAME INDEX idx_f422a9dae36d154 TO posted_by');
        $this->addSql('ALTER TABLE assignments DROP FOREIGN KEY FK_308A50DD579F4768');
        $this->addSql('ALTER TABLE assignments CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE title title VARCHAR(200) NOT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE submission_type submission_type ENUM(\'TEXT\', \'FILE\', \'BOTH\') DEFAULT \'TEXT\', CHANGE allow_late_submission allow_late_submission TINYINT DEFAULT 0, CHANGE status status ENUM(\'DRAFT\', \'PUBLISHED\', \'CLOSED\') DEFAULT \'DRAFT\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE type type ENUM(\'homework\', \'quiz\', \'exam\') DEFAULT \'homework\' NOT NULL, CHANGE chapter_id chapter_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE assignments ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (chapter_id) REFERENCES chapters (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX idx_assignments_chapter ON assignments (chapter_id, due_date)');
        $this->addSql('ALTER TABLE chapter_contents DROP FOREIGN KEY FK_52E37C72579F4768');
        $this->addSql('ALTER TABLE chapter_contents DROP FOREIGN KEY FK_52E37C72DE12AB56');
        $this->addSql('DROP INDEX IDX_52E37C72579F4768 ON chapter_contents');
        $this->addSql('ALTER TABLE chapter_contents ADD item_id BIGINT NOT NULL, DROP chapter_id, CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE title title VARCHAR(200) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE chapter_contents ADD CONSTRAINT `chapter_contents_ibfk_1` FOREIGN KEY (item_id) REFERENCES chapter_items (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chapter_contents ADD CONSTRAINT `chapter_contents_ibfk_2` FOREIGN KEY (created_by) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX item_id ON chapter_contents (item_id)');
        $this->addSql('ALTER TABLE chapter_contents RENAME INDEX idx_52e37c72de12ab56 TO created_by');
        $this->addSql('ALTER TABLE chapter_files DROP FOREIGN KEY FK_867CD6E6579F4768');
        $this->addSql('ALTER TABLE chapter_files DROP FOREIGN KEY FK_867CD6E6E3E73126');
        $this->addSql('DROP INDEX IDX_867CD6E6579F4768 ON chapter_files');
        $this->addSql('ALTER TABLE chapter_files ADD item_id BIGINT NOT NULL, DROP chapter_id, CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE file_path file_path VARCHAR(500) NOT NULL, CHANGE file_type file_type VARCHAR(100) DEFAULT NULL, CHANGE uploaded_at uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE chapter_files ADD CONSTRAINT `chapter_files_ibfk_1` FOREIGN KEY (item_id) REFERENCES chapter_items (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chapter_files ADD CONSTRAINT `chapter_files_ibfk_2` FOREIGN KEY (uploaded_by) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX item_id ON chapter_files (item_id)');
        $this->addSql('ALTER TABLE chapter_files RENAME INDEX idx_867cd6e6e3e73126 TO uploaded_by');
        $this->addSql('ALTER TABLE chapter_items DROP FOREIGN KEY FK_61577FF2579F4768');
        $this->addSql('ALTER TABLE chapter_items CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE type type ENUM(\'CONTENT\', \'FILE\') NOT NULL, CHANGE sort_order sort_order INT DEFAULT 0, CHANGE chapter_id chapter_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE chapter_items ADD CONSTRAINT `chapter_items_ibfk_1` FOREIGN KEY (chapter_id) REFERENCES chapters (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX idx_items_chapter ON chapter_items (chapter_id, sort_order)');
        $this->addSql('ALTER TABLE chapter_progress DROP INDEX UNIQ_C4189F43579F4768, ADD INDEX IDX_C4189F43579F4768 (chapter_id)');
        $this->addSql('ALTER TABLE chapter_progress DROP FOREIGN KEY FK_C4189F43579F4768');
        $this->addSql('ALTER TABLE chapter_progress DROP FOREIGN KEY FK_C4189F43CB944F1A');
        $this->addSql('ALTER TABLE chapter_progress CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE status status ENUM(\'NOT_STARTED\', \'IN_PROGRESS\', \'COMPLETED\') DEFAULT \'NOT_STARTED\', CHANGE last_accessed_at last_accessed_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE chapter_id chapter_id BIGINT NOT NULL, CHANGE student_id student_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE chapter_progress ADD CONSTRAINT `chapter_progress_ibfk_1` FOREIGN KEY (chapter_id) REFERENCES chapters (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chapter_progress ADD CONSTRAINT `chapter_progress_ibfk_2` FOREIGN KEY (student_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX chapter_id ON chapter_progress (chapter_id, student_id)');
        $this->addSql('ALTER TABLE chapters DROP FOREIGN KEY FK_C721437123EDC87');
        $this->addSql('ALTER TABLE chapters CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE title title VARCHAR(200) NOT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE sort_order sort_order INT DEFAULT 0, CHANGE is_published is_published TINYINT DEFAULT 0, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE chapters ADD CONSTRAINT `fk_chapters_subject` FOREIGN KEY (subject_id) REFERENCES subjects (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE INDEX idx_chapters_class_subject ON chapters (class_id, subject_id, sort_order)');
        $this->addSql('ALTER TABLE chapters RENAME INDEX idx_c721437123edc87 TO fk_chapters_subject');
        $this->addSql('ALTER TABLE classes CHANGE name name VARCHAR(100) NOT NULL, CHANGE grade_level grade_level VARCHAR(20) NOT NULL, CHANGE section section VARCHAR(10) DEFAULT NULL, CHANGE is_active is_active TINYINT DEFAULT 1, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('CREATE INDEX idx_classes_selector ON classes (is_active, name, grade_level)');
        $this->addSql('ALTER TABLE conversation_members DROP INDEX UNIQ_DEF6DCF59AC0396, ADD INDEX IDX_DEF6DCF59AC0396 (conversation_id)');
        $this->addSql('ALTER TABLE conversation_members DROP INDEX UNIQ_DEF6DCF5A76ED395, ADD INDEX idx_members_user (user_id)');
        $this->addSql('ALTER TABLE conversation_members DROP FOREIGN KEY FK_DEF6DCF59AC0396');
        $this->addSql('ALTER TABLE conversation_members DROP FOREIGN KEY FK_DEF6DCF5A76ED395');
        $this->addSql('ALTER TABLE conversation_members CHANGE role role ENUM(\'MEMBER\', \'ADMIN\') DEFAULT \'MEMBER\', CHANGE joined_at joined_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE conversation_id conversation_id BIGINT NOT NULL, CHANGE user_id user_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE conversation_members ADD CONSTRAINT `conversation_members_ibfk_1` FOREIGN KEY (conversation_id) REFERENCES conversations (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversation_members ADD CONSTRAINT `conversation_members_ibfk_2` FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX conversation_id ON conversation_members (conversation_id, user_id)');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF1DE12AB56');
        $this->addSql('ALTER TABLE conversations CHANGE type type ENUM(\'DIRECT\', \'GROUP\') NOT NULL, CHANGE name name VARCHAR(200) DEFAULT NULL, CHANGE pair_hash pair_hash VARCHAR(64) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE created_by created_by BIGINT NOT NULL');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (created_by) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX pair_hash ON conversations (pair_hash)');
        $this->addSql('ALTER TABLE conversations RENAME INDEX idx_c2521bf1de12ab56 TO created_by');
        $this->addSql('ALTER TABLE forum_comments DROP FOREIGN KEY FK_786D1BCD4B89032C');
        $this->addSql('ALTER TABLE forum_comments DROP FOREIGN KEY FK_786D1BCD727ACA70');
        $this->addSql('ALTER TABLE forum_comments DROP FOREIGN KEY FK_786D1BCDCB944F1A');
        $this->addSql('ALTER TABLE forum_comments CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE content content TEXT NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE sync_uuid sync_uuid VARCHAR(100) DEFAULT NULL, CHANGE post_id post_id BIGINT NOT NULL, CHANGE parent_id parent_id BIGINT DEFAULT NULL, CHANGE student_id student_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE forum_comments ADD CONSTRAINT `fk_fw_comments_parent` FOREIGN KEY (parent_id) REFERENCES forum_comments (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forum_comments ADD CONSTRAINT `fk_fw_comments_post` FOREIGN KEY (post_id) REFERENCES forum_posts (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forum_comments ADD CONSTRAINT `fk_fw_comments_user` FOREIGN KEY (student_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX sync_uuid ON forum_comments (sync_uuid)');
        $this->addSql('ALTER TABLE forum_comments RENAME INDEX idx_786d1bcd727aca70 TO fk_fw_comments_parent');
        $this->addSql('ALTER TABLE forum_comments RENAME INDEX idx_786d1bcd4b89032c TO idx_fw_comments_post');
        $this->addSql('ALTER TABLE forum_comments RENAME INDEX idx_786d1bcdcb944f1a TO idx_fw_comments_user');
        $this->addSql('ALTER TABLE forum_post_attachments DROP FOREIGN KEY FK_2BB74F104B89032C');
        $this->addSql('ALTER TABLE forum_post_attachments CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE file_type file_type VARCHAR(50) DEFAULT NULL, CHANGE uploaded_at uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE post_id post_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE forum_post_attachments ADD CONSTRAINT `forum_post_attachments_ibfk_1` FOREIGN KEY (post_id) REFERENCES forum_posts (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forum_post_attachments RENAME INDEX idx_2bb74f104b89032c TO post_id');
        $this->addSql('ALTER TABLE forum_posts DROP FOREIGN KEY FK_90291C2DDE12AB56');
        $this->addSql('ALTER TABLE forum_posts CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE title title VARCHAR(200) NOT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE content content TEXT NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE class_id class_id INT NOT NULL, CHANGE created_by created_by BIGINT NOT NULL');
        $this->addSql('ALTER TABLE forum_posts ADD CONSTRAINT `fk_fw_posts_user` FOREIGN KEY (created_by) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forum_posts RENAME INDEX idx_90291c2dea000b10 TO idx_fw_posts_class');
        $this->addSql('ALTER TABLE forum_posts RENAME INDEX idx_90291c2dde12ab56 TO idx_fw_posts_user');
        $this->addSql('ALTER TABLE forum_reviews DROP INDEX UNIQ_98BCA67E4B89032C, ADD INDEX idx_fw_reviews_post (post_id)');
        $this->addSql('ALTER TABLE forum_reviews DROP INDEX UNIQ_98BCA67ECB944F1A, ADD INDEX idx_fw_reviews_user (student_id)');
        $this->addSql('ALTER TABLE forum_reviews DROP FOREIGN KEY FK_98BCA67E4B89032C');
        $this->addSql('ALTER TABLE forum_reviews DROP FOREIGN KEY FK_98BCA67ECB944F1A');
        $this->addSql('ALTER TABLE forum_reviews CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE review_text review_text TEXT DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE sync_uuid sync_uuid VARCHAR(100) DEFAULT NULL, CHANGE post_id post_id BIGINT NOT NULL, CHANGE student_id student_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE forum_reviews ADD CONSTRAINT `fk_fw_reviews_post` FOREIGN KEY (post_id) REFERENCES forum_posts (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forum_reviews ADD CONSTRAINT `fk_fw_reviews_user` FOREIGN KEY (student_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX uq_fw_review_unique ON forum_reviews (post_id, student_id)');
        $this->addSql('CREATE UNIQUE INDEX sync_uuid ON forum_reviews (sync_uuid)');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E969AC0396');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96F624B39D');
        $this->addSql('ALTER TABLE messages CHANGE content content TEXT NOT NULL, CHANGE is_deleted is_deleted TINYINT DEFAULT 0, CHANGE sent_at sent_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE status status ENUM(\'SENT\', \'DELIVERED\', \'SEEN\') DEFAULT \'SENT\', CHANGE conversation_id conversation_id BIGINT NOT NULL, CHANGE sender_id sender_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (conversation_id) REFERENCES conversations (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (sender_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX idx_messages_conversation ON messages (conversation_id, sent_at)');
        $this->addSql('ALTER TABLE messages RENAME INDEX idx_db021e96f624b39d TO sender_id');
        $this->addSql('ALTER TABLE roles CHANGE name name VARCHAR(50) NOT NULL, CHANGE role_category role_category ENUM(\'ADMIN\', \'STAFF\', \'TEACHER\', \'STUDENT\') NOT NULL, CHANGE description description TEXT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX name ON roles (name)');
        $this->addSql('ALTER TABLE subjects CHANGE subject_code subject_code VARCHAR(20) NOT NULL, CHANGE name name VARCHAR(200) NOT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE is_active is_active TINYINT DEFAULT 1');
        $this->addSql('CREATE UNIQUE INDEX subject_code ON subjects (subject_code)');
        $this->addSql('CREATE INDEX idx_subjects_selector_v2 ON subjects (is_active, grade_level, name)');
        $this->addSql('ALTER TABLE subjects RENAME INDEX idx_ab259917e2c35fc TO FK_SUBJECTS_TERM');
        $this->addSql('ALTER TABLE submission_files DROP FOREIGN KEY FK_DBAA3AFBE1FD4933');
        $this->addSql('ALTER TABLE submission_files DROP FOREIGN KEY FK_DBAA3AFBE3E73126');
        $this->addSql('ALTER TABLE submission_files CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE file_path file_path VARCHAR(500) NOT NULL, CHANGE file_type file_type VARCHAR(100) DEFAULT NULL, CHANGE uploaded_at uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE submission_id submission_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE submission_files ADD CONSTRAINT `submission_files_ibfk_1` FOREIGN KEY (submission_id) REFERENCES submissions (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE submission_files ADD CONSTRAINT `submission_files_ibfk_2` FOREIGN KEY (uploaded_by) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE submission_files RENAME INDEX idx_dbaa3afbe1fd4933 TO submission_id');
        $this->addSql('ALTER TABLE submission_files RENAME INDEX idx_dbaa3afbe3e73126 TO uploaded_by');
        $this->addSql('ALTER TABLE submissions DROP INDEX UNIQ_3F6169F7D19302F8, ADD INDEX IDX_3F6169F7D19302F8 (assignment_id)');
        $this->addSql('ALTER TABLE submissions DROP FOREIGN KEY FK_3F6169F7D19302F8');
        $this->addSql('ALTER TABLE submissions DROP FOREIGN KEY FK_3F6169F7CB944F1A');
        $this->addSql('ALTER TABLE submissions DROP FOREIGN KEY FK_3F6169F785D7FB47');
        $this->addSql('ALTER TABLE submissions CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE submission_text submission_text TEXT DEFAULT NULL, CHANGE is_late is_late TINYINT DEFAULT 0, CHANGE feedback feedback TEXT DEFAULT NULL, CHANGE status status ENUM(\'SUBMITTED\', \'REVIEWED\', \'RETURNED\') DEFAULT \'SUBMITTED\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE assignment_id assignment_id BIGINT NOT NULL, CHANGE student_id student_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE submissions ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (assignment_id) REFERENCES assignments (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE submissions ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (student_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE submissions ADD CONSTRAINT `submissions_ibfk_3` FOREIGN KEY (reviewed_by) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX assignment_id ON submissions (assignment_id, student_id)');
        $this->addSql('ALTER TABLE submissions RENAME INDEX idx_3f6169f7cb944f1a TO idx_submissions_student');
        $this->addSql('ALTER TABLE submissions RENAME INDEX idx_3f6169f785d7fb47 TO reviewed_by');
        $this->addSql('ALTER TABLE terms ADD current_in_year_flag INT DEFAULT NULL, CHANGE name name VARCHAR(50) NOT NULL, CHANGE is_current is_current TINYINT DEFAULT 0, CHANGE academic_year_id academic_year_id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_terms_current_in_year ON terms (current_in_year_flag)');
        $this->addSql('CREATE INDEX idx_terms_selector ON terms (academic_year_id, is_current, start_date, name)');
        $this->addSql('ALTER TABLE terms RENAME INDEX idx_88a23f71c54f3401 TO academic_year_id');
    }
}
