# Wrapper de PowerShell para la Suite de Pruebas de Docker
Write-Host "Ejecutando Suite de Pruebas de Integracion para Docker y MySQL..." -ForegroundColor Cyan
php (Join-Path $PSScriptRoot "verify-docker.php")
exit $LASTEXITCODE
