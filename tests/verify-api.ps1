# Wrapper de PowerShell para la Suite de Pruebas de API y Contención
Write-Host "Ejecutando Suite Automatizada de Pruebas de API REST y Contención..." -ForegroundColor Cyan
php (Join-Path $PSScriptRoot "verify-api.php")
exit $LASTEXITCODE
