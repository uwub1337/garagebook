@echo off
chcp 65001 >nul
echo 正在推送專案至 GitHub (https://github.com/uwub1337/garagebook)...
echo.

set "GIT_EXE=C:\Program Files\Cherry Studio\resources\app.asar.unpacked\resources\binaries\win32-x64\git\cmd\git.exe"

if not exist "%GIT_EXE%" (
    echo 找不到 Git，請確認系統是否有安裝 Git。
    pause
    exit /b 1
)

cd /d "%~dp0"

echo 正在執行 git push -u origin main ...
"%GIT_EXE%" push -u origin main

if %ERRORLEVEL% equ 0 (
    echo.
    echo ===================================================
    echo  推送成功！已成功掛上 GitHub！
    echo  倉儲網址：https://github.com/uwub1337/garagebook
    echo ===================================================
) else (
    echo.
    echo 推送失敗，若需要登入，請依照跳出的 GitHub 視窗進行授權。
)

echo.
pause
