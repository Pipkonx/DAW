# 📄 README - Sistema de Gestión de Tareas con Roles

## Descripción
Este proyecto es un **sistema web de gestión de tareas** desarrollado con **PHP (PDO), HTML, CSS, Bootstrap y MySQL**, siguiendo el patrón **MVC**.  
Permite administrar usuarios con **roles de administrador y operador**, y gestionar tareas con datos completos, adjuntos y fotos.  

- **Administrador:** Puede crear, editar, eliminar y ver todos los usuarios y tareas.  
- **Operador:** Solo puede editar sus propias tareas y crear nuevas tareas.  

Los archivos y fotos se almacenan localmente en carpetas del servidor, y no se utilizan sesiones.  

---

## 📂 Estructura del proyecto

```
mi_app/
│
├─ index.php # Punto de entrada
├─ config/
│ └─ database.php # Conexión PDO (Singleton)
├─ controllers/
│ ├─ UserController.php
│ └─ TaskController.php
├─ models/
│ ├─ User.php
│ └─ Task.php
├─ views/
│ ├─ templates/
│ │ ├─ header.php
│ │ └─ footer.php
│ ├─ users/
│ │ ├─ list.php
│ │ ├─ create.php
│ │ └─ edit.php
│ └─ tasks/
│ ├─ list.php
│ ├─ create.php
│ └─ edit.php
├─ uploads/
│ ├─ resumen/ # Archivos adjuntos de tareas
│ └─ fotos/ # Fotos de tareas
└─ database.sql # Base de datos de ejemplo
```