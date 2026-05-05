-- ============================================
-- Learnway Database Full Setup
-- Complete SQL Schema for learnway_web
-- ============================================

-- Create Roles table (must come first as it's referenced by Users)
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    role_category VARCHAR(255) NOT NULL,
    description LONGTEXT,
    INDEX idx_roles_category (role_category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Academic Years table
CREATE TABLE IF NOT EXISTS academic_years (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    start_date DATE,
    end_date DATE,
    is_current TINYINT DEFAULT 0,
    current_flag TINYINT GENERATED ALWAYS AS (CASE WHEN is_current = 1 THEN 1 ELSE NULL END) STORED,
    UNIQUE KEY uniq_academic_years_current_flag (current_flag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Terms table
CREATE TABLE IF NOT EXISTS terms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    academic_year_id INT,
    name VARCHAR(255) NOT NULL,
    is_current TINYINT DEFAULT 0,
    current_in_year_flag INT GENERATED ALWAYS AS (CASE WHEN is_current = 1 THEN academic_year_id ELSE NULL END) STORED,
    UNIQUE KEY uniq_terms_current_in_year (current_in_year_flag),
    INDEX idx_terms_selector (academic_year_id, is_current),
    FOREIGN KEY (academic_year_id) REFERENCES academic_years (id),
    CONSTRAINT FK_88A23F71C54F3401 FOREIGN KEY (academic_year_id) REFERENCES academic_years (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Classes table
CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    grade_level VARCHAR(255) NOT NULL,
    section VARCHAR(255),
    is_active TINYINT DEFAULT 1,
    created_at DATETIME,
    INDEX idx_classes_active (is_active),
    INDEX idx_classes_grade_level (grade_level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Users table
CREATE TABLE IF NOT EXISTS users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    role_id INT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255),
    date_of_birth DATE,
    gender VARCHAR(255),
    phone VARCHAR(255),
    employee_id VARCHAR(255),
    student_id VARCHAR(255),
    is_active TINYINT DEFAULT 1,
    last_login_at DATETIME,
    created_at DATETIME,
    INDEX IDX_1483A5E9D60322AC (role_id),
    FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Subjects table
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    term_id INT,
    subject_code VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description LONGTEXT,
    is_active TINYINT DEFAULT 1,
    INDEX IDX_AB259917E2C35FC (term_id),
    FOREIGN KEY (term_id) REFERENCES terms (id),
    CONSTRAINT FK_AB259917E2C35FC FOREIGN KEY (term_id) REFERENCES terms (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Chapters table
CREATE TABLE IF NOT EXISTS chapters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT,
    sort_order INT,
    is_published TINYINT DEFAULT 0,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX IDX_C721437123EDC87 (subject_id),
    FOREIGN KEY (subject_id) REFERENCES subjects (id) ON DELETE CASCADE,
    CONSTRAINT FK_C721437123EDC87 FOREIGN KEY (subject_id) REFERENCES subjects (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Chapter Items table
CREATE TABLE IF NOT EXISTS chapter_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chapter_id INT,
    type VARCHAR(255) NOT NULL,
    sort_order INT,
    INDEX idx_items_chapter (chapter_id),
    FOREIGN KEY (chapter_id) REFERENCES chapters (id) ON DELETE CASCADE,
    CONSTRAINT FK_61577FF2579F4768 FOREIGN KEY (chapter_id) REFERENCES chapters (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Chapter Contents table
CREATE TABLE IF NOT EXISTS chapter_contents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chapter_id INT,
    item_id INT UNIQUE,
    title VARCHAR(255),
    body LONGTEXT NOT NULL,
    created_by BIGINT,
    created_at DATETIME,
    updated_at DATETIME,
    UNIQUE KEY UNIQ_52E37C72126F525E (item_id),
    INDEX IDX_52E37C72DE12AB56 (created_by),
    INDEX IDX_52E37C72579F4768 (chapter_id),
    FOREIGN KEY (item_id) REFERENCES chapter_items (id),
    FOREIGN KEY (created_by) REFERENCES users (id),
    FOREIGN KEY (chapter_id) REFERENCES chapters (id) ON DELETE CASCADE,
    CONSTRAINT FK_52E37C72126F525E FOREIGN KEY (item_id) REFERENCES chapter_items (id),
    CONSTRAINT FK_52E37C72DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id),
    CONSTRAINT FK_52E37C72579F4768 FOREIGN KEY (chapter_id) REFERENCES chapters (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Chapter Files table
CREATE TABLE IF NOT EXISTS chapter_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT UNIQUE,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(255),
    uploaded_by BIGINT,
    uploaded_at DATETIME,
    UNIQUE KEY UNIQ_867CD6E6126F525E (item_id),
    INDEX IDX_867CD6E6E3E73126 (uploaded_by),
    FOREIGN KEY (item_id) REFERENCES chapter_items (id),
    FOREIGN KEY (uploaded_by) REFERENCES users (id),
    CONSTRAINT FK_867CD6E6126F525E FOREIGN KEY (item_id) REFERENCES chapter_items (id),
    CONSTRAINT FK_867CD6E6E3E73126 FOREIGN KEY (uploaded_by) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Assignments table
CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chapter_id INT,
    type VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    due_date DATETIME,
    description LONGTEXT,
    submission_type VARCHAR(255),
    allow_late_submission TINYINT DEFAULT 0,
    status VARCHAR(255),
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_assignments_chapter (chapter_id),
    FOREIGN KEY (chapter_id) REFERENCES chapters (id) ON DELETE CASCADE,
    CONSTRAINT FK_308A50DD579F4768 FOREIGN KEY (chapter_id) REFERENCES chapters (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Submissions table
CREATE TABLE IF NOT EXISTS submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT UNIQUE,
    student_id BIGINT,
    submission_text LONGTEXT,
    submitted_at DATETIME,
    is_late TINYINT DEFAULT 0,
    feedback LONGTEXT,
    reviewed_by BIGINT,
    reviewed_at DATETIME,
    status VARCHAR(255),
    created_at DATETIME,
    updated_at DATETIME,
    UNIQUE KEY UNIQ_3F6169F7D19302F8 (assignment_id),
    INDEX IDX_3F6169F7CB944F1A (student_id),
    INDEX IDX_3F6169F785D7FB47 (reviewed_by),
    FOREIGN KEY (assignment_id) REFERENCES assignments (id),
    FOREIGN KEY (student_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users (id),
    CONSTRAINT FK_3F6169F7D19302F8 FOREIGN KEY (assignment_id) REFERENCES assignments (id),
    CONSTRAINT FK_3F6169F7CB944F1A FOREIGN KEY (student_id) REFERENCES users (id),
    CONSTRAINT FK_3F6169F785D7FB47 FOREIGN KEY (reviewed_by) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Submission Files table
CREATE TABLE IF NOT EXISTS submission_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(255),
    uploaded_by BIGINT,
    uploaded_at DATETIME,
    INDEX IDX_DBAA3AFBE1FD4933 (submission_id),
    INDEX IDX_DBAA3AFBE3E73126 (uploaded_by),
    FOREIGN KEY (submission_id) REFERENCES submissions (id),
    FOREIGN KEY (uploaded_by) REFERENCES users (id),
    CONSTRAINT FK_DBAA3AFBE1FD4933 FOREIGN KEY (submission_id) REFERENCES submissions (id),
    CONSTRAINT FK_DBAA3AFBE3E73126 FOREIGN KEY (uploaded_by) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Student Enrollments table
CREATE TABLE IF NOT EXISTS student_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    user_id INT,
    academic_year_id INT,
    is_active TINYINT DEFAULT 1,
    enrolled_at DATETIME,
    INDEX IDX_1B38CC31EA000B10 (class_id),
    INDEX IDX_1B38CC31A76ED395 (user_id),
    INDEX IDX_1B38CC31C54F3401 (academic_year_id),
    UNIQUE KEY uniq_user_academic_year (user_id, academic_year_id),
    FOREIGN KEY (class_id) REFERENCES classes (id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years (id) ON DELETE CASCADE,
    CONSTRAINT FK_1B38CC31A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Chapter Progress table
CREATE TABLE IF NOT EXISTS chapter_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chapter_id INT UNIQUE,
    student_id BIGINT UNIQUE,
    status VARCHAR(255),
    last_accessed_at DATETIME,
    completed_at DATETIME,
    UNIQUE KEY UNIQ_C4189F43579F4768 (chapter_id),
    UNIQUE KEY UNIQ_C4189F43CB944F1A (student_id),
    FOREIGN KEY (chapter_id) REFERENCES chapters (id),
    FOREIGN KEY (student_id) REFERENCES users (id),
    CONSTRAINT FK_C4189F43579F4768 FOREIGN KEY (chapter_id) REFERENCES chapters (id),
    CONSTRAINT FK_C4189F43CB944F1A FOREIGN KEY (student_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Teacher Assignments table
CREATE TABLE IF NOT EXISTS teacher_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id BIGINT,
    subject_id INT,
    class_id INT,
    assigned_at DATETIME,
    ended_at DATETIME,
    FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects (id),
    FOREIGN KEY (class_id) REFERENCES classes (id),
    CONSTRAINT FK_E6D6EC9741807E1D FOREIGN KEY (teacher_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Forum Posts table
CREATE TABLE IF NOT EXISTS forum_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255),
    description LONGTEXT,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255),
    created_by BIGINT,
    created_at DATETIME,
    INDEX IDX_90291C2DEA000B10 (class_id),
    INDEX IDX_90291C2DDE12AB56 (created_by),
    FOREIGN KEY (created_by) REFERENCES users (id),
    CONSTRAINT FK_90291C2DDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Forum Comments table
CREATE TABLE IF NOT EXISTS forum_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT,
    parent_id INT,
    student_id BIGINT,
    content LONGTEXT NOT NULL,
    created_at DATETIME,
    sync_uuid VARCHAR(255),
    INDEX IDX_786D1BCD4B89032C (post_id),
    INDEX IDX_786D1BCD727ACA70 (parent_id),
    INDEX IDX_786D1BCDCB944F1A (student_id),
    FOREIGN KEY (post_id) REFERENCES forum_posts (id),
    FOREIGN KEY (parent_id) REFERENCES forum_comments (id),
    FOREIGN KEY (student_id) REFERENCES users (id),
    CONSTRAINT FK_786D1BCD4B89032C FOREIGN KEY (post_id) REFERENCES forum_posts (id),
    CONSTRAINT FK_786D1BCD727ACA70 FOREIGN KEY (parent_id) REFERENCES forum_comments (id),
    CONSTRAINT FK_786D1BCDCB944F1A FOREIGN KEY (student_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Forum Reviews table
CREATE TABLE IF NOT EXISTS forum_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT UNIQUE,
    student_id BIGINT UNIQUE,
    review_text LONGTEXT,
    created_at DATETIME,
    sync_uuid VARCHAR(255),
    UNIQUE KEY UNIQ_98BCA67E4B89032C (post_id),
    UNIQUE KEY UNIQ_98BCA67ECB944F1A (student_id),
    FOREIGN KEY (post_id) REFERENCES forum_posts (id),
    FOREIGN KEY (student_id) REFERENCES users (id),
    CONSTRAINT FK_98BCA67E4B89032C FOREIGN KEY (post_id) REFERENCES forum_posts (id),
    CONSTRAINT FK_98BCA67ECB944F1A FOREIGN KEY (student_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Forum Post Attachments table
CREATE TABLE IF NOT EXISTS forum_post_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(255),
    uploaded_at DATETIME,
    INDEX IDX_2BB74F104B89032C (post_id),
    FOREIGN KEY (post_id) REFERENCES forum_posts (id),
    CONSTRAINT FK_2BB74F104B89032C FOREIGN KEY (post_id) REFERENCES forum_posts (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Conversations table
CREATE TABLE IF NOT EXISTS conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    pair_hash VARCHAR(255),
    created_by BIGINT,
    created_at DATETIME,
    INDEX IDX_C2521BF1DE12AB56 (created_by),
    FOREIGN KEY (created_by) REFERENCES users (id),
    CONSTRAINT FK_C2521BF1DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Conversation Members table
CREATE TABLE IF NOT EXISTS conversation_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT,
    user_id BIGINT UNIQUE,
    role VARCHAR(255),
    joined_at DATETIME,
    UNIQUE KEY UNIQ_DEF6DCF59AC0396 (conversation_id),
    UNIQUE KEY UNIQ_DEF6DCF5A76ED395 (user_id),
    FOREIGN KEY (conversation_id) REFERENCES conversations (id),
    FOREIGN KEY (user_id) REFERENCES users (id),
    CONSTRAINT FK_DEF6DCF59AC0396 FOREIGN KEY (conversation_id) REFERENCES conversations (id),
    CONSTRAINT FK_DEF6DCF5A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT,
    sender_id BIGINT,
    content LONGTEXT NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    sent_at DATETIME,
    status VARCHAR(255),
    INDEX IDX_DB021E969AC0396 (conversation_id),
    INDEX IDX_DB021E96F624B39D (sender_id),
    FOREIGN KEY (conversation_id) REFERENCES conversations (id),
    FOREIGN KEY (sender_id) REFERENCES users (id),
    CONSTRAINT FK_DB021E969AC0396 FOREIGN KEY (conversation_id) REFERENCES conversations (id),
    CONSTRAINT FK_DB021E96F624B39D FOREIGN KEY (sender_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Message Reads table
CREATE TABLE IF NOT EXISTS message_reads (
    user_id BIGINT NOT NULL,
    message_id INT NOT NULL,
    PRIMARY KEY (user_id, message_id),
    INDEX IDX_37E6935AA76ED395 (user_id),
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (message_id) REFERENCES messages (id),
    CONSTRAINT FK_37E6935AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id),
    CONSTRAINT FK_37E6935A537A1329 FOREIGN KEY (message_id) REFERENCES messages (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Announcements table
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    posted_by BIGINT,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    target_type VARCHAR(255),
    target_id INT,
    posted_at DATETIME,
    INDEX IDX_F422A9DAE36D154 (posted_by),
    FOREIGN KEY (posted_by) REFERENCES users (id),
    CONSTRAINT FK_F422A9DAE36D154 FOREIGN KEY (posted_by) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Doctrine Migrations table (for tracking migrations)
CREATE TABLE IF NOT EXISTS doctrine_migration_versions (
    version VARCHAR(191) NOT NULL,
    executed_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    execution_time INT,
    PRIMARY KEY (version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insert default roles
-- ============================================
INSERT IGNORE INTO roles (id, name, role_category, description) VALUES
(1, 'ADMIN', 'admin', 'Administrator with full system access'),
(2, 'TEACHER', 'educator', 'Teacher responsible for classes and assignments'),
(3, 'STUDENT', 'learner', 'Student enrolled in classes'),
(4, 'PARENT', 'guardian', 'Parent or guardian of students');

-- ============================================
-- End of Database Setup
-- ============================================
