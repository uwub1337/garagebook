$root = Split-Path -Parent $PSScriptRoot
$mysql = Join-Path $root 'tools\mysql'
$data = Join-Path $root 'tools\mysql-data'

$listening = Get-NetTCPConnection -LocalPort 3306 -State Listen -ErrorAction SilentlyContinue
if ($listening) {
    Write-Host 'MySQL 已在執行 (127.0.0.1:3306)'
    return
}

if (-not (Test-Path $data)) {
    New-Item -ItemType Directory -Force $data | Out-Null
    & (Join-Path $mysql 'bin\mysqld.exe') --initialize-insecure --basedir=$mysql --datadir=$data
}

Start-Process -FilePath (Join-Path $mysql 'bin\mysqld.exe') `
    -ArgumentList "--basedir=$mysql", "--datadir=$data", "--port=3306", "--bind-address=127.0.0.1" `
    -WindowStyle Hidden

for ($i = 0; $i -lt 30; $i++) {
    Start-Sleep -Milliseconds 500
    & (Join-Path $mysql 'bin\mysqladmin.exe') -u root -h 127.0.0.1 --connect-timeout=2 ping 2>$null | Out-Null

    if ($LASTEXITCODE -eq 0) {
        & (Join-Path $mysql 'bin\mysql.exe') -u root -h 127.0.0.1 -e "CREATE DATABASE IF NOT EXISTS garagebook CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
        Write-Host 'MySQL 已啟動'
        return
    }
}

throw 'MySQL 啟動失敗，請確認 tools\mysql 存在。'
