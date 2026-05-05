@echo off
REM Complete Messaging Test Script
REM Tests the full message flow from sending to real-time delivery

setlocal enabledelayedexpansion

echo.
echo ========================================
echo   LEARNWAY MESSAGING - COMPLETE TEST
echo ========================================
echo.

REM Check Docker status
echo [STEP 1/5] Checking Docker containers...
docker compose ps
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Docker not running
    echo Run: docker compose up -d
    goto EOF
)
echo ✓ Docker status checked
echo.

REM Check Socket.IO logs
echo [STEP 2/5] Checking Socket.IO Server...
echo.
echo Recent Socket.IO logs (last 15 lines):
docker compose logs socket-server --tail=15
echo.

REM Check Redis connection
echo [STEP 3/5] Testing Redis connection...
powershell -Command "
try {
    \$socket = New-Object System.Net.Sockets.TcpClient
    \$socket.Connect('localhost', 6379)
    if (\$socket.Connected) {
        Write-Host '✓ Redis is reachable on localhost:6379'
        \$socket.Close()
    }
} catch {
    Write-Host '✗ Cannot reach Redis on localhost:6379'
}
"
echo.

REM Check Socket.IO accessibility
echo [STEP 4/5] Checking Socket.IO Server accessibility...
powershell -Command "
try {
    \$response = Invoke-WebRequest -Uri 'http://localhost:3001/socket.io/' -TimeoutSec 3 -SkipHttpStatusCodeCheck
    if (\$response.StatusCode -eq 200 -or \$response.StatusCode -eq 400) {
        Write-Host '✓ Socket.IO server is accessible'
    } else {
        Write-Host '? Received status code: ' \$response.StatusCode
    }
} catch {
    Write-Host '✗ Cannot reach Socket.IO on localhost:3001'
}
"
echo.

REM Instructions for testing
echo [STEP 5/5] Manual Testing Instructions...
echo.
echo To test messaging:
echo.
echo 1. Open browser to: http://localhost:8000/messages
echo 2. Open conversation with another user
echo 3. Send a test message
echo 4. Watch for these:
echo.
echo    In Chat UI:
echo    - Message should appear IMMEDIATELY
echo    - Both sender and receiver should see it
echo.
echo    In Browser (F12 - Console tab):
echo    - Should NOT show errors
echo    - Should show connection logs
echo.
echo    In PHP Error Log:
echo    - Should show: "✓ Message published to Redis"
echo.
echo    In Docker Logs:
echo    docker compose logs socket-server -f
echo    - Should show: "📨 Broadcasting to room"
echo.

echo ========================================
echo              TEST COMPLETE
echo ========================================
echo.
echo NEXT STEPS:
echo 1. If message appears: Great! Messaging is working!
echo 2. If message doesn't appear: Check browser console (F12)
echo 3. For more diagnostics: Run debug_messaging.bat
echo 4. Check PHP error logs in XAMPP
echo.

:EOF
pause
