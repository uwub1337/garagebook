$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$php = Join-Path $root 'tools\php\php.exe'
$phpMyAdmin = Join-Path $root 'tools\phpMyAdmin-5.2.3-all-languages'
$port = 8081

if (-not (Test-Path -LiteralPath $phpMyAdmin)) {
    throw '找不到 phpMyAdmin。請確認 tools 資料夾完整。'
}

& (Join-Path $root 'scripts\start-mysql.ps1')

$listening = Get-NetTCPConnection -LocalPort $port -State Listen -ErrorAction SilentlyContinue

if (-not $listening) {
    Start-Process -FilePath $php `
        -ArgumentList @('-S', "127.0.0.1:$port", '-t', $phpMyAdmin) `
        -WindowStyle Hidden

    Start-Sleep -Milliseconds 500
}

Start-Process "http://127.0.0.1:$port"
Write-Host "phpMyAdmin 已開啟：http://127.0.0.1:$port"
