@echo off
REM Delete old livestream folders - they've been moved to templates/livestream/

echo Deleting old livestream folders...

rmdir /s /q "templates\teacher\livestreams"
rmdir /s /q "templates\student\livestreams"

echo ✅ Old folders deleted!
echo ✅ All livestream templates are now in: templates/livestream/
pause
