@echo off
REM Direct MySQL fix - Execute all migrations immediately

echo Fixing all missing columns...
echo.

mysql -u root -p2532001 learnway_web -e "ALTER TABLE chapter_progress ADD COLUMN IF NOT EXISTS completed_at DATETIME DEFAULT NULL AFTER last_accessed_at;"

mysql -u root -p2532001 learnway_web -e "ALTER TABLE assignments ADD COLUMN IF NOT EXISTS due_date DATETIME DEFAULT NULL AFTER title;"

mysql -u root -p2532001 learnway_web -e "ALTER TABLE submissions ADD COLUMN IF NOT EXISTS submitted_at DATETIME DEFAULT NULL AFTER submission_text;"

mysql -u root -p2532001 learnway_web -e "ALTER TABLE submissions ADD COLUMN IF NOT EXISTS reviewed_at DATETIME DEFAULT NULL AFTER feedback;"

mysql -u root -p2532001 learnway_web -e "ALTER TABLE student_enrollments ADD COLUMN IF NOT EXISTS academic_year_id INT DEFAULT NULL AFTER class_id;"

mysql -u root -p2532001 learnway_web -e "ALTER TABLE student_enrollments ADD CONSTRAINT FK_1B38CC31C54F3401 FOREIGN KEY (academic_year_id) REFERENCES academic_years (id) ON DELETE CASCADE;"

mysql -u root -p2532001 learnway_web -e "ALTER TABLE student_enrollments ADD UNIQUE KEY IF NOT EXISTS uniq_user_academic_year (user_id, academic_year_id);"

mysql -u root -p2532001 learnway_web -e "ALTER TABLE chapter_contents ADD COLUMN IF NOT EXISTS body LONGTEXT NOT NULL DEFAULT '' AFTER title;"

mysql -u root -p2532001 learnway_web -e "ALTER TABLE chapter_contents ADD COLUMN IF NOT EXISTS chapter_id INT DEFAULT NULL AFTER id;"

mysql -u root -p2532001 learnway_web -e "ALTER TABLE chapter_contents ADD CONSTRAINT FK_52E37C72579F4768 FOREIGN KEY (chapter_id) REFERENCES chapters (id) ON DELETE CASCADE;"

mysql -u root -p2532001 learnway_web -e "ALTER TABLE forum_posts ADD COLUMN IF NOT EXISTS subtitle VARCHAR(255) DEFAULT NULL AFTER title;"

mysql -u root -p2532001 learnway_web -e "ALTER TABLE forum_posts ADD COLUMN IF NOT EXISTS featured_image VARCHAR(255) DEFAULT NULL AFTER content;"

mysql -u root -p2532001 learnway_web -e "ALTER TABLE announcements ADD COLUMN IF NOT EXISTS target_type VARCHAR(255) DEFAULT NULL;"

mysql -u root -p2532001 learnway_web -e "ALTER TABLE announcements ADD COLUMN IF NOT EXISTS target_id INT DEFAULT NULL;"

mysql -u root -p2532001 learnway_web -e "ALTER TABLE announcements ADD COLUMN IF NOT EXISTS posted_at DATETIME DEFAULT NULL;"

echo.
echo ========================================
echo All columns fixed successfully!
echo ========================================
echo.
pause
