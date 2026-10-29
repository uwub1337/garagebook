$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot

& (Join-Path $root 'scripts\start-mysql.ps1')

Start-Process powershell -ArgumentList '-ExecutionPolicy', 'Bypass', '-File', (Join-Path $root 'scripts\start-phpmyadmin.ps1') -WindowStyle Hidden

Start-Process powershell -ArgumentList '-NoExit', '-ExecutionPolicy', 'Bypass', '-File', (Join-Path $root 'scripts\start-backend.ps1')
Start-Process powershell -ArgumentList '-NoExit', '-ExecutionPolicy', 'Bypass', '-File', (Join-Path $root 'scripts\start-frontend.ps1')

Start-Sleep -Seconds 3
Start-Process 'http://127.0.0.1:5173'

Write-Host ''
Write-Host 'GarageBook 已啟動！'
Write-Host '前端：http://127.0.0.1:5173'
Write-Host 'API ：http://127.0.0.1:8000'
Write-Host '資料庫管理：http://127.0.0.1:8081（phpMyAdmin）'
Write-Host '示範帳號：demo@garagebook.test / password123'
Write-Host '關閉服務請執行 stop.cmd'
