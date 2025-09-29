# SubsTracker - Gestor de Suscripciones basado en Gmail

## Descripción General
SubsTracker es una aplicación web que ayuda a los usuarios a identificar, organizar y gestionar sus suscripciones activas mediante el análisis de sus correos electrónicos de Gmail. La aplicación detecta automáticamente servicios de suscripción, fechas de renovación y costos, presentando esta información de manera clara y accesible.

## Problema y Objetivos

### Problema
Actualmente, los usuarios deben revisar manualmente sus correos electrónicos para identificar suscripciones activas, lo que implica una inversión significativa de tiempo y atención. En muchos casos, este proceso no se realiza, lo que lleva a que las personas mantengan suscripciones innecesarias o duplicadas durante largos periodos de tiempo.

### Objetivos
- Automatizar la detección de servicios de suscripción a través del análisis de correos electrónicos
- Proporcionar una interfaz visual clara para gestionar todas las suscripciones en un solo lugar
- Garantizar la privacidad y seguridad de los datos del usuario
- Ofrecer una solución gratuita o accesible sin necesidad de acceso a datos bancarios

## Análisis de Soluciones Existentes
Existen algunas aplicaciones comerciales que ofrecen funcionalidades similares (como Truebill), pero muchas son limitadas en cuanto a localización, requieren acceso bancario o están restringidas a ciertos países. SubsTracker busca ofrecer una solución gratuita o accesible, sin necesidad de acceso a datos bancarios, utilizando únicamente el correo electrónico como fuente de información.

## Arquitectura del Sistema

### Componentes Principales
1. **Interfaz Web del Usuario**: Portal donde el usuario podrá iniciar sesión con su cuenta de Google, visualizar sus suscripciones detectadas y gestionar su información personal.

2. **Integración con Gmail API**: Componente central que permite acceder a correos electrónicos del usuario (con su consentimiento), buscando patrones comunes relacionados con suscripciones.

3. **Módulo de Procesamiento**: Capa de análisis de texto que procesa los correos encontrados y extrae información relevante (nombre del servicio, frecuencia de pago, fechas de renovación, etc.).

4. **Base de Datos**: Almacenamiento de resultados procesados para facilitar la consulta por parte del usuario.

5. **Módulo de Seguridad y Autenticación**: Implementado mediante OAuth 2.0 de Google para garantizar acceso seguro a los datos del usuario.

## Requisitos Técnicos

### Hardware
- Servidor en la nube o VPS para el despliegue de la aplicación
- No se requiere hardware especializado adicional

### Software y Tecnologías
- **Frontend**: 
  - HTML5, CSS3, JavaScript 
  - Framework: Vue.js (por el momento no)
  - Bibliotecas UI: Material-UI o PicoCSS

- **Backend**: 
  - Node.js con MySQL
  - API RESTful para comunicación cliente-servidor

- **Base de Datos**: 
  - MySQL
  - Esquemas para usuarios, suscripciones y configuraciones

- **APIs Externas**: 
  - Gmail API (https://developers.google.com/gmail/api)
  - Google OAuth 2.0 para autenticación

- **Herramientas de Desarrollo**:
  - Git para control de versiones
  - Jest/Mocha para pruebas unitarias

## Guía de Instalación y Configuración

### Requisitos Previos
- Node.js (v14 o superior)
- NPM o Yarn
- MongoDB/PostgreSQL instalado localmente o acceso a una instancia remota
- Cuenta de desarrollador de Google para acceder a Gmail API

## Plan de Desarrollo

### Fase 1: Configuración y Autenticación
- Configurar estructura del proyecto
- Implementar autenticación con Google OAuth
- Crear modelos de base de datos iniciales

### Fase 2: Integración con Gmail API
- Implementar conexión con Gmail API
- Desarrollar funciones para buscar y recuperar correos
- Crear sistema de permisos y alcance

### Fase 3: Análisis de Correos
- Desarrollar algoritmos para detectar patrones de suscripción
- Implementar extracción de información relevante
- Crear sistema de categorización

### Fase 4: Interfaz de Usuario
- Diseñar e implementar dashboard principal
- Crear vistas para gestión de suscripciones
- Implementar funcionalidades de filtrado y búsqueda

### Fase 5: Pruebas y Optimización
- Realizar pruebas unitarias y de integración
- Optimizar rendimiento y experiencia de usuario
- Implementar feedback de usuarios iniciales

## Contribución
Las contribuciones son bienvenidas. Para contribuir:

1. Haz fork del repositorio
2. Crea una rama para tu funcionalidad (`git checkout -b feature/nueva-funcionalidad`)
3. Realiza tus cambios y haz commit (`git commit -m 'Añadir nueva funcionalidad'`)
4. Sube tus cambios (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

## Licencia
Este proyecto está licenciado bajo la Licencia MIT - ver el archivo LICENSE para más detalles.