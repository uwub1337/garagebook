$root = Split-Path -Parent $PSScriptRoot
$env:Path = (Join-Path $root 'tools\node') + ';' + $env:Path
Set-Location (Join-Path $root 'frontend')

Write-Host 'Vue 前端：http://127.0.0.1:5173'
npm run dev -- --host 127.0.0.1
