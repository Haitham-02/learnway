@echo off
REM Messaging System - Quick Verification Script
REM Tests if the messaging fix is working

echo.
echo ╔════════════════════════════════════════════╗
echo ║   MESSAGING SYSTEM - QUICK VERIFICATION    ║
echo ╚════════════════════════════════════════════╝
echo.

echo [STEP 1] Checking Docker containers...
docker compose ps | findstr "redis\|socket-server\|mailer"
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ✗ Docker containers not running!
    echo Run: docker compose up -d
    goto END
)
echo ✓ All containers running
echo.

echo [STEP 2] Checking if PHP files were updated...
findstr "✓ Initializing message form" templates\message\show.html.twig
if %ERRORLEVEL% NEQ 0 (
    echo ✗ Template not updated
    goto END
)
echo ✓ Template updated with new form code
echo.

echo [STEP 3] Checking if PHP controller was updated...
findstr "=== MESSAGE REQUEST ===" src\Controller\MessageController.php
if %ERRORLEVEL% NEQ 0 (
    echo ✗ Controller not updated
    goto END
)
echo ✓ Controller updated with detailed logging
echo.

echo ╔════════════════════════════════════════════╗
echo ║           READY TO TEST!                   ║
echo ╚════════════════════════════════════════════╝
echo.
echo Next steps:
echo.
echo 1. Open browser: http://localhost:8000/messages
echo 2. Press F12 to open Developer Console
echo 3. Send a test message
echo 4. Check console for:
echo    ✓ "HTMX sending request"
echo    ✓ "HTMX before request, method: POST"
echo    ✓ "HTMX after request, status: 200"
echo.
echo 5. Check PHP logs:
echo    Look for: "✓ Message saved to DB"
echo.
echo If you see these messages, the fix is working!
echo If not, the form submission is still broken.
echo.

:END
pause
