# 🧩 Proyecto MVC en PHP - Gestión de Usuarios

Este proyecto es un ejemplo básico del patrón **MVC (Modelo - Vista - Controlador)** implementado en **PHP**, ideal para aprender cómo separar la lógica del negocio, la presentación y el control del flujo de la aplicación.  
Incluye un pequeño sistema CRUD (Crear, Leer, Actualizar y Borrar) de usuarios. 👤

---

## 🚀 Características

- Estructura MVC simple y limpia.  
- CRUD completo de usuarios.  
- Validación básica de formularios.  
- Conexión a base de datos con **PDO**.  
- Código fácil de entender y ampliar.

---

## 🗂️ Estructura del proyecto

```
proyecto_mvc/
│
├── index.php                     # Punto de entrada principal
│
├── config/
│   └── database.php              # Conexión a la base de datos
│
├── controllers/
│   └── UsuarioController.php     # Controlador principal
│
├── models/
│   └── Usuario.php               # Modelo de datos
│
└── views/
    ├── usuario_alta.php          # Formulario de alta
    ├── usuario_modificar.php     # Formulario de edición
    ├── usuario_borrar.php        # Confirmación de borrado
    └── usuario_lista.php         # Listado de usuarios
```

---

## 🧠 Requisitos

- **PHP 7.4+**
- **MySQL o MariaDB**
- Servidor local como **XAMPP**, **Laragon** o **WAMP**.

---

## 🗃️ Base de datos

Ejecuta este script SQL en tu gestor (phpMyAdmin, DBeaver, etc.):

```sql
CREATE DATABASE IF NOT EXISTS mvc_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE mvc_demo;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE
);
```


## 💡 Próximos pasos

- Añadir validaciones más completas.  
- Incluir sesiones e inicio de sesión.  
- Crear más entidades (productos, clientes, pedidos, etc).  
- Conectar el frontend con Bootstrap o TailwindCSS.

---

> Si este proyecto te sirvió, ¡guárdalo o modifícalo a tu gusto!  
> Aprender creando es la mejor forma de dominar PHP. 🚀
