Write-Host "--- Verificando Calidad del Código ---"

Write-Host "[1/2] Ejecutando Linter..."
# Corregir automáticamente errores de estilo
npm run lint
if ($LASTEXITCODE -ne 0) {
    Write-Error "❌ Linting falló. Por favor corrige los errores."
    exit 1
} else {
    Write-Host "✅ Linting correcto."
}

Write-Host "[2/2] Ejecutando Tests..."
# Ejecutar tests una vez
npx vitest run
if ($LASTEXITCODE -ne 0) {
    Write-Error "❌ Tests fallaron."
    exit 1
} else {
    Write-Host "✅ Tests correctos."
}

Write-Host "🎉 ¡Todo listo para producción!"
