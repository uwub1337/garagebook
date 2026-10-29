$root = Split-Path -Parent $PSScriptRoot

foreach ($port in 8000, 5173, 8081) {
    Get-NetTCPConnection -LocalPort $port -State Listen -ErrorAction SilentlyContinue |
        Select-Object -ExpandProperty OwningProcess -Unique |
        ForEach-Object { Stop-Process -Id $_ -Force -ErrorAction SilentlyContinue }
}

$mysqladmin = Join-Path $root 'tools\mysql\bin\mysqladmin.exe'
if (Test-Path $mysqladmin) {
    & $mysqladmin -u root -h 127.0.0.1 shutdown 2>$null | Out-Null
}

Write-Host 'GarageBook 服務已停止（MySQL、Laravel、Vite、phpMyAdmin）。'
