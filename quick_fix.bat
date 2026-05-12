@echo off
REM SQLite Database Setup Script for Learnway
REM This script rebuilds the SQLite database with proper roles and admin account

echo.
echo ╔════════════════════════════════════════════╗
echo ║    LEARNWAY DATABASE SETUP (SQLite)        ║
echo ╚════════════════════════════════════════════╝
echo.

REM Delete old database if it exists
if exist var\data.db (
    echo [1/2] Removing old database...
    del var\data.db
    echo ✓ Old database removed
) else (
    echo [!] No existing database found (first setup)
)

echo.
echo [2/2] Creating new database with roles and admin...
php import_sqlite.php

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ╔════════════════════════════════════════════╗
    echo ║  ✗ DATABASE SETUP FAILED                   ║
    echo ╚════════════════════════════════════════════╝
    pause
    exit /b 1
)

echo.
echo ╔════════════════════════════════════════════╗
echo ║  ✓ DATABASE SETUP COMPLETE                 ║
echo ╚════════════════════════════════════════════╝
echo.
echo You can now access the application with:
echo   Email:    admin@learnway.com
echo   Password: Admin@123
echo.
echo URL: http://127.0.0.1:8000
echo.

pause
