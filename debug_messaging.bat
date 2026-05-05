@echo off
REM Messaging Debug Script
REM This script helps diagnose messaging system issues

echo.
echo ========================================
echo     MESSAGING SYSTEM DEBUG SCRIPT
echo ========================================
echo.

REM Check Docker status
echo [1/4] Checking Docker containers...
docker compose ps
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Docker not running or compose command failed
    echo Make sure you run: docker compose up -d
    goto EOF
)
echo ✓ Docker status OK
echo.

REM Check Redis connection
echo [2/4] Checking Redis connection...
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
    Write-Host 'Error: ' \$_.Exception.Message
}
"
echo.

REM Check Socket.IO server logs
echo [3/4] Checking Socket.IO Server logs...
echo.
echo Recent Socket.IO logs:
docker compose logs socket-server --tail=10
echo.

REM Check if browser can connect
echo [4/4] Checking Socket.IO accessibility...
powershell -Command "
try {
    \$response = Invoke-WebRequest -Uri 'http://localhost:3001/socket.io/' -TimeoutSec 5 -SkipHttpStatusCodeCheck
    Write-Host ('✓ Socket.IO server is accessible (Status: ' + \$response.StatusCode + ')')
} catch {
    Write-Host '✗ Cannot reach Socket.IO on localhost:3001'
    Write-Host 'Error: ' \$_.Exception.Message
}
"
echo.

echo ========================================
echo     DEBUG INFORMATION COLLECTED
echo ========================================
echo.
echo Next steps:
echo 1. Check if all containers show "Running" status
echo 2. Open browser console (F12) on the messaging page
echo 3. Look for WebSocket connection messages
echo 4. Send a test message and check the logs
echo.
echo To view real-time logs:
echo   docker compose logs -f socket-server
echo   docker compose logs -f redis
echo.

:EOF
pause
