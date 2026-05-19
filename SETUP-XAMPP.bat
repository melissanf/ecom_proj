@echo off
title Beauty Store - XAMPP Setup
cd /d "%~dp0"

set "XAMPP=C:\xampp"
set "PHP=%XAMPP%\php\php.exe"
set "DEST=%XAMPP%\htdocs\beauty-store"

echo ============================================
echo   Beauty Store - Automatic XAMPP Setup
echo ============================================
echo.

if not exist "%PHP%" (
    echo ERROR: XAMPP PHP not found at:
    echo   %PHP%
    echo.
    echo If XAMPP is on another drive, edit SETUP-XAMPP.bat
    echo and change the XAMPP= line to your path.
    echo.
    pause
    exit /b 1
)

echo [1/4] Copying files to htdocs with UTF-8 fix...
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0deploy.ps1" -Source "%~dp0" -Dest "%DEST%"
if errorlevel 1 (
    echo ERROR during copy. Try: Right-click SETUP-XAMPP.bat - Run as administrator
    pause
    exit /b 1
)

echo [2/4] Removing bad .htaccess...
if exist "%DEST%\.htaccess" del /f /q "%DEST%\.htaccess"

echo [3/4] Testing PHP...
"%PHP%" -r "echo 'PHP CLI OK';"
if errorlevel 1 (
    echo ERROR: XAMPP PHP failed to run.
    pause
    exit /b 1
)
echo.

echo [4/4] Installing database (start MySQL in XAMPP first!)...
"%PHP%" "%DEST%\install-cli.php"
if errorlevel 1 (
    echo.
    echo Database install failed. Open XAMPP and START MySQL, then run this bat again.
    pause
    exit /b 1
)

echo.
echo ============================================
echo   DONE!
echo ============================================
echo.
echo 1. In XAMPP: Apache and MySQL must be RUNNING
echo 2. Open: http://localhost/beauty-store/test-php.php
echo    (must say: PHP works!)
echo 3. Open: http://localhost/beauty-store/
echo.
echo Admin: http://localhost/beauty-store/admin/
echo   admin@beauty.com / password
echo.
pause
