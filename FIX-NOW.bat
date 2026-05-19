@echo off
echo Converting PHP files from UTF-16 to UTF-8...
echo Folder: %~dp0
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0convert-utf8.ps1"
echo.
echo DONE. Now:
echo 1. Restart Apache in XAMPP
echo 2. Open http://localhost/beauty-store/test-php.php
echo.
pause
