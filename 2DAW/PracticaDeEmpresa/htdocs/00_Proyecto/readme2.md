# Ejercicio: Sistema de Gestión de prácticas Académicas

# Descripcion General

Desarrollar una aplicacion web para la gestion integral de prácticas académicas usando Java, Mysql y Eclipse/Spriing Tools Suite. El sistema implementará el patron MVC modelo vista controlador y gestionará cuatro tipos de suarois con diferentes niveles de acceso y funcionalidades

## Objetivo de Aprendizaje

- Implementar el patron MVC en Java
- Gesionar bases de datos relacionales con MySAL
- Implementar sistemas de autentificacion y autorizacion
- Crear interfaces de usuarios intuitivas
- Desarrollar funcionalidades CRUD completas
- Generar reportes y estadísticas
- Aplicar buenas prácticas de programacion

## Tecnologías requeridas

- Lenguaje: Java
- Framework: Spring Boot + Spring MVC + Spring Security
- Base de Datos: MySql
- ORM: JPA/Hivernate
- IDE: Eclipse o Spring Tools Suite
- Frontend: Thymeleaf + Bootstrap 5
- Build Tool: Maven

## Especificacion de perfiles de usuario

 1.  Administrador
- Permisos: Acceso completo al sistema
- Funcionalidades:
 - CRUD completo de todos los usuarios
 - Gestion de cursos y empresas
 - Configuracion del sistema
 - Acceso a todas las estadisticas
 - Gestion de criterios de evaluación globales

2. Tutor del Curso
- Permisos: Gestion de sus cursos asignados
- Funcionalidades: 
  - Ver alumnos de sus cursos
  - Consultar empresas asociadas a sus alumnos
  - Ver tutores de practicas de sus alumnos
  - Evaluar alumnos y tutores
  - Generar reportes por curso/empresa/alumno
  - Acceder a estadísticas de sus cursos

3. Tutor de Prácticas (Empresa)
- Permisos: Gestion de sus alumnos asignados
- Funcionalidades:
  - Ver solo sus alumnos asignados
  - Registrar observaciones diarias
  - Evaluar prácticas de sus alumnos
  - Gestionar criterios de evaluación
  - Registrar incidencias
  - Actualizar horarios y acticidades

4. Alumno
- Permisos: Gestion de su informacion personal
- Funcionalidades:
  - Ver y actualizar datos personales
  - Registrar observaciones diarias
  - Ver evaluaciones recividas 
  - Consultar horarios y actividades
  - Ver feedback de tutores

## Modelo de Datos

Entidad: ALumno
 - id: (Long, PK, AutoIncrement)
 - nombre: (String, Not Null)
 - apellido: (String, Not Null)
 - email: (String, Not Null, Unique)
 - rol: (Enum: ADMIN, TUTOR_COURSE, TUTOR_PRACTICES, ALUMNO)