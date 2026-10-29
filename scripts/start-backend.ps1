$root = Split-Path -Parent $PSScriptRoot
$env:Path = (Join-Path $root 'tools\php') + ';' + $env:Path
Set-Location (Join-Path $root 'backend')

Write-Host 'Laravel API：http://127.0.0.1:8000'
php artisan serve --host=127.0.0.1 --port=8000
