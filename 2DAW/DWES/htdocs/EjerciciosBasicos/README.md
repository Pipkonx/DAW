# 🚀 Notas de Aprendizaje: Laravel 12.x

---

## 📖 Documentación Oficial y Recursos
- **Controladores:** [Laravel Docs - Controllers](https://laravel.com/docs/12.x/controllers)
- **Eloquent Resources:** [Documentación Eloquent Resources](https://laravel.com/docs/12.x/eloquent-resources#main-content)
- **Vite:** [Vite.dev](https://vite.dev/)
- **Laravel Sail (Docker):** [Documentación Sail](https://laravel.com/docs/12.x/sail)

## 🛠️ Comandos Útiles
Crear un controlador de tipo recurso para una entidad (ej. ProvControl):
```bash
php artisan make:controller ProvControl --resource
```

## 🔐 Autenticación y Seguridad
- **Auth General:** [Documentación Authentication](https://laravel.com/docs/12.x/authentication)
- **Recomendación:** Iniciar proyectos nuevos con Vue, Laravel Auth y NPM.
- **Socialite:** Implementar inicio de sesión con Google.
- **Middleware:** Pendiente revisar a fondo la configuración y el uso de middlewares.

## 🏗️ Herramientas de UI y Administración
- **Laravel UI:** Integrar el paquete para la gestión de interfaces.
- **Filament PHP:** [Filament Docs](https://filamentphp.com/docs) - Panel administrativo moderno.
- **Laravel Nova:** [Nova Docs](https://nova.laravel.com/) - Herramienta avanzada para la creación de CRUDs.

## ✍️ Notas para el Examen (Eloquent)
El examen se centrará en el proceso completo utilizando Eloquent:
1.  **Relaciones:** Definir y utilizar relaciones entre modelos.
2.  **Formularios:** Creación y manejo de datos.
3.  **Validación:** Implementar reglas de validación en las peticiones.
4.  **Flujo completo:** Realizar todo el proceso desde la base de datos hasta la vista.


<!-- // REVISAR DOC
// https://laravel.com/docs/12.x/controllers\

// php artisan make:controller ProvControl --resource

// https://laravel.com/docs/12.x/eloquent-resources#main-content

// revisar el tema de docker con sail en laravel con Sail
// https://laravel.com/docs/12.x/sail

// https://laravel.com/docs/12.x/authentication
// aconsejable crear un proyecto nuevo de laravel con vue y laravel de auth y con npm

// revisar el middleware

//El examen eloquent relaciones formulario  validando etc haciendo to el proceso

// tenemos que agregar el laravel ui

// leer sobre 
//https://vite.dev/

// leer m'as sobre 
// https://filamentphp.com/docs
// y para el crud
// https://nova.laravel.com/

// inicio sesion con google enlaravel google socialite -->