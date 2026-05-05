@echo off
REM Direct MySQL Fix - Execute the fix immediately

mysql -u root -p2532001 learnway_web -e "ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) DEFAULT NULL AFTER last_name;"
mysql -u root -p2532001 learnway_web -e "ALTER TABLE users ADD COLUMN IF NOT EXISTS date_of_birth DATE DEFAULT NULL AFTER profile_picture;"
mysql -u root -p2532001 learnway_web -e "ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login_at DATETIME DEFAULT NULL AFTER is_active;"

echo.
echo Columns added successfully!
echo Verifying table structure...
echo.

mysql -u root -p2532001 learnway_web -e "DESC users;"

pause
