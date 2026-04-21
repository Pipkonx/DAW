#!/bin/bash

# Script de Despliegue Automático para FintechPro
# Uso: ./deploy.sh

echo "🚀 Iniciando despliegue..."

# 1. Traer los últimos cambios
git pull origin main

# 2. Reconstruir contenedores si es necesario y levantar
docker-compose up -d --build

# 3. Ejecutar migraciones con fuerza (para producción)
echo "📂 Ejecutando migraciones..."
docker exec fintechpro-app php artisan migrate --force

# 4. Limpiar y recrear caches de Laravel
echo "🧹 Limpiando caches..."
docker exec fintechpro-app php artisan config:cache
docker exec fintechpro-app php artisan route:cache
docker exec fintechpro-app php artisan view:cache

# 5. Reiniciar colas si las usas
# docker exec fintechpro-app php artisan queue:restart

echo "✅ Despliegue completado con éxito."
