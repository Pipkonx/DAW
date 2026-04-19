# MEMORIA TÉCNICA: APLICACIÓN CRUD DE GESTIÓN DE CONTACTOS

**Asignatura:** Programación en Python  
**Actividad:** Gestión de Bases de Datos Relacionales con MySQL y Tkinter  
**Autor:** [Tu Nombre y Apellidos]

---

## 1. Introducción
Este proyecto consiste en el desarrollo de una aplicación de escritorio diseñada para la gestión eficiente de una agenda de contactos. La aplicación integra el lenguaje **Python** para la lógica de negocio, **MySQL** para la persistencia de datos y la librería **Tkinter** para la interfaz gráfica de usuario (GUI).

## 2. Objetivos del Proyecto
- Implementar un sistema funcional de mantenimiento de registros (Create, Read, Update, Delete).
- Garantizar la integridad de los datos mediante validaciones estrictas.
- Proporcionar una experiencia de usuario fluida con búsqueda en tiempo real.
- Modularizar el código siguiendo buenas prácticas de desarrollo.

## 3. Requisitos del Entorno
- **Lenguaje:** Python 3.9 o superior.
- **Base de Datos:** MySQL Server.
- **Librerías:** `mysql-connector-python` (conector oficial) y `tkinter` (estándar).

## 4. Diseño de la Base de Datos
Se ha diseñado una base de datos denominada `contact_db` con una tabla principal `contactos`.
- `id`: Clave primaria, autoincremental.
- `nombre`: Cadena de texto (obligatorio).
- `telefono`: Cadena de texto (formato numérico).
- `email`: Cadena de texto (opcional, con formato validado).

El script de creación (`setup_db.sql`) asegura que la estructura se despliegue correctamente en cualquier entorno local.

## 5. Implementación Técnica

### 5.1. Organización Modular
El código se ha dividido en cuatro módulos principales:
1.  **`main.py`**: Punto de entrada que lanza la aplicación.
2.  **`database.py`**: Centraliza todas las operaciones SQL (cláusulas SELECT, INSERT, UPDATE, DELETE).
3.  **`validations.py`**: Contiene la lógica de expresiones regulares (Regex).
4.  **`gui.py`**: Implementa la interfaz visual y la gestión de eventos de los botones.

### 5.2. Validaciones con Regex
Se han implementado tres niveles de validación:
- **Nombre:** No puede estar vacío y solo permite letras y espacios.
- **Teléfono:** Solo permite valores numéricos y debe tener entre 7 y 15 dígitos.
- **Email:** Si se proporciona, debe cumplir con el patrón estándar `usuario@dominio.com`.

### 5.3. Búsqueda en Tiempo Real
La aplicación utiliza un campo de entrada vinculado al evento `<KeyRelease>`. Cada vez que el usuario suelta una tecla, se lanza una consulta SQL con la cláusula `LIKE`, filtrando los resultados de la tabla instantáneamente sin necesidad de pulsar un botón de "buscar".

## 6. Pruebas y Evaluación
Se han realizado pruebas de estrés introduciendo datos inválidos (letras en el teléfono, emails sin @) y se ha verificado que los mensajes de error emergen correctamente antes de intentar la inserción en la base de datos. Se ha confirmado la persistencia de los datos mediante reinicios de la aplicación.

## 7. Conclusión
El desarrollo de esta aplicación ha permitido integrar conocimientos avanzados de Python, desde la manipulación de interfaces gráficas hasta la interacción con sistemas de bases de datos profesionales. La división del código en módulos garantiza que la aplicación sea mantenible y escalable para futuras versiones.

---
*Documento generado como soporte para la entrega final de DAW - Python.*
