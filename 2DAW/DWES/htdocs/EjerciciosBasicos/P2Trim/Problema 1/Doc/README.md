# Documentación de la Aplicación - Problema 1

Esta carpeta contiene la documentación técnica del proyecto. Según las normas del curso, se han seguido los estándares **PSR-5 PHPDoc** para comentar el código.

### Cómo visualizar la documentación

#### 1. Documentación del Código (PHPDoc)
Puedes leer los comentarios directamente en los archivos de la carpeta `app/`. Verás que todas las funciones tienen bloques como este:
```php
/**
 * Descripción de la función
 * @param [Tipo] $variable
 * @return [Tipo]
 */
```

#### 2. Documentación de la API (Swagger)
Para el **Problema 4.2**, se ha integrado **Swagger (OpenAPI)**. Para ver la documentación interactiva de la API:
1. Asegúrate de que el servidor está corriendo (`php artisan serve`).
2. Entra en tu navegador a: `http://localhost:8000/api/documentation`
3. Allí verás todos los "endpoints" (URLs de la API), los parámetros que necesitan y podrás probarlos directamente.

#### 3. Generación automática avanzada
Si deseas generar un sitio web completo con toda la documentación del código, puedes usar herramientas como **ApiGen** o **phpDocumentor** apuntando a la carpeta `app/`.
