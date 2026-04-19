# Gestión de Contactos 📱 (CRUD con MySQL)

Este proyecto es una aplicación de escritorio desarrollada en Python para la gestión eficiente de una lista de contactos. Permite realizar las operaciones básicas de **Crear, Leer, Actualizar y Eliminar (CRUD)** sobre una base de datos MySQL.

## Características
- **Interfaz Gráfica**: Desarrollada con Tkinter y estilos modernos (`ttk.Style`).
- **Base de Datos**: Persistencia real de datos utilizando MySQL.
- **Búsqueda en tiempo real**: Filtrado dinámico mientras el usuario escribe.
- **Validación de Datos**: Comprobación de formato de nombre, teléfono y email mediante expresiones regulares (Regex).

## Librerías utilizadas
- `mysql-connector-python`: Para la comunicación con el servidor de base de datos.
- `tkinter`: Para la interfaz de usuario.
- `re`: Para las validaciones de datos.

## Instalación
1. Asegúrate de tener un servidor MySQL corriendo (puedes usar XAMPP o MySQL directo).
2. Instala el conector de Python:
   ```bash
   pip install mysql-connector-python
   ```
3. Ejecuta el script SQL incluido (`setup_db.sql`) en tu gestor de base de datos para crear la tabla y los datos iniciales.

## Ejecución
Lanza el programa principal con:
```bash
python main.py
```

## Estructura del Proyecto
- `main.py`: Punto de entrada de la aplicación.
- `gui.py`: Contiene toda la lógica de la interfaz gráfica y eventos.
- `database.py`: Centraliza las consultas SQL y la conexión.
- `validations.py`: Funciones de validación de entradas de usuario.
- `setup_db.sql`: Script de creación de la base de datos.
