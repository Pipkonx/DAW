Write-Host "Iniciando configuración de calidad de código..."

# 1. Instalar dependencias
Write-Host "Instalando ESLint, Prettier y Vitest..."
npm install -D eslint eslint-plugin-vue prettier @vue/eslint-config-prettier vitest jsdom @rushstack/eslint-patch

# 2. Configuración de Prettier
Write-Host "Creando configuración de Prettier..."
Set-Content .prettierrc '{
  "semi": true,
  "singleQuote": true,
  "tabWidth": 4,
  "trailingComma": "none",
  "printWidth": 100
}'

# 3. Configuración de ESLint (Básica)
Write-Host "Creando configuración de ESLint..."
Set-Content .eslintrc.cjs '/* eslint-env node */
require("@rushstack/eslint-patch/modern-module-resolution");

module.exports = {
  root: true,
  extends: [
    "plugin:vue/vue3-essential",
    "eslint:recommended",
    "@vue/eslint-config-prettier"
  ],
  parserOptions: {
    ecmaVersion: "latest"
  },
  rules: {
    "vue/multi-word-component-names": "off"
  }
};'

Write-Host "¡Configuración completada! Ahora puedes ejecutar 'npm run lint' o 'npm run test'."
