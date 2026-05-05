@echo off
REM Complete Learnway Database Setup Script
REM This script will:
REM 1. Create all database tables
REM 2. Create the dummy admin user

setlocal enabledelayedexpansion

echo.
echo ========================================
echo Learnway Database Setup
echo ========================================
echo.

REM Set your MySQL credentials
set MYSQL_USER=root
set MYSQL_PASS=2532001
set MYSQL_HOST=127.0.0.1
set DB_NAME=learnway_web

echo Creating database tables...
mysql -h %MYSQL_HOST% -u %MYSQL_USER% -p%MYSQL_PASS% %DB_NAME% < database_setup.sql

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to create database tables!
    pause
    exit /b 1
)

echo Tables created successfully!
echo.
echo Creating admin user...
mysql -h %MYSQL_HOST% -u %MYSQL_USER% -p%MYSQL_PASS% %DB_NAME% < create_admin_user.sql

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to create admin user!
    pause
    exit /b 1
)

echo.
echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Admin User Credentials:
echo Email:    admin@learnway.local
echo Password: Admin@123
echo.
echo You can now log in to the application.
echo.
pause
