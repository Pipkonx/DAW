Guía Completa del Proyecto DWES

1. Resumen Ejecutivo
   El proyecto consiste en el desarrollo de una aplicación web para la empresa de albañilería Bunglebuild S.L. con el objetivo de mejorar la gestión, control y seguimiento de las obras y tareas
   . La aplicación deberá funcionar como un gestor de incidencias/tareas, permitiendo la colaboración entre usuarios administrativos (encargados de crear y supervisar tareas) y operarios (encargados de cambiar estados y realizar anotaciones). El desarrollo debe aplicar obligatoriamente patrones de diseño como MVC y Singleton para la conexión a la base de datos, utilizando PHP y aprovechando parcialmente el framework Laravel solo para el gestor de plantillas Blade y el controlador frontal
   .
2. Producto Mínimo Viable (MVP)
   El MVP se define como la sección "1. Aplicación de gestión de incidencias/tareas - Básica"
   , excluyendo inicialmente las funcionalidades marcadas como "avanzado"
   .
   Funcionalidad

Descripción
Referencia (Sección/Página)
Listado Básico de Tareas (Read)
Mostrar la lista de tareas ordenadas de forma descendente por fecha de realización, mostrando información relevante (descripción, persona de contacto, fecha de realización)
.
1.1. (p. 5)
Añadir Tarea (Create)
Opción para crear una nueva incidencia/tarea, con comprobación y filtrado de datos en el servidor, permitiendo al usuario corregir errores
.
1.2. (p. 6)
Modificar Tarea (Update)
Opción para mostrar los campos de una tarea en un formulario y modificarlos. Se requiere filtrado de campos antes de guardar
.
1.3. (p. 6)
Eliminar Tarea (Delete)
Eliminar una tarea de la base de datos. Requiere una página de confirmación interactuando con el servidor (no JavaScript)
.
1.4. (p. 6)
Arquitectura Mínima
Uso obligatorio del patrón MVC, Singleton (para conexión DB), y Controlador Frontal (usando Laravel)
.
1.6 (p. 7)
, Normas de obligado cumplimiento (p. 7)
Diseño Modular
La aplicación debe utilizar un diseño web modular (encabezado, menú, pie) usando el motor de plantillas Blade
.
1.6 (p. 7) 3. Lista de Tareas Desglosadas por Prioridad y Dependencias
La planificación sugerida en el documento establece una secuencia de trabajo que define la prioridad y las dependencias lógicas
.
ID
Tarea
Prioridad
Dependencia
Referencia (Página)
T01
Preparación del Entorno (SO, Runtimes, GIT, BD)
Alta
Ninguna
T02
Diseño del Modelo de Datos (Esquema BD)
Alta
T01
T03
Creación de Formularios (Altas/Modificaciones) y Validación/Filtrado de datos en el servidor
Alta
T02 (para campos select como Provincia)
T04
Implementación de la Capa de Abstracción DB (Clase DB usando Patrón Singleton)
Alta
T02
T05
Implementación de Operaciones CRUD (Crear, Listar, Modificar, Borrar)
Alta
T03, T04
T06
Integración parcial con Laravel/Blade (MVC, Vistas modulares, Controlador Frontal)
Media
T05
T07
Gestión Básica de Usuario (Validación con un usuario fijo, uso de sesiones nativas PHP)
Media
T06
T08
Diferenciación de Roles (Administrativo/Operario) y restricción de operaciones
.
Media
T07
T09
Completar Tarea (Operario): Modificación limitada de campos (Fecha Realización, Estado, Anotaciones Posteriores)
.
Media
T08
T10
Implementación de funcionalidades avanzadas "Medio" (Paginación 1.1.1)
.
Media
T06
T11
Instalador de la aplicación (Creación/Inicialización BD)
.
Media
T02, T04
T12
Completar funcionalidades avanzadas "Avanzado" (Multi-usuario, Recordar credenciales, Adjuntar archivos, Configuración, Listar Pendientes, etc.)
Baja
T08, T10, T11
T13
Documentación Automática (Generar docs con Doxygen/ApiGen)
Baja
T12
T14
Entrega y Defensa (Subir zip sin vendor ni .git, rellenar cuestionario, defensa)
Baja
T13 4. Instrucciones Concretas por Tarea
Las instrucciones concretas asumen una estructura básica de Laravel/PHP para cumplir con el uso del Controlador Frontal, Vistas Blade y la separación de la lógica de negocio (MVC)
.
Tarea
Archivos / Rutas
Endpoints / Comandos (Asumidos)
Indicación / Restricción
T04: Capa DB (Singleton)
app/DB.php (o similar)
N/A
Implementar patrón Singleton para guardar la conexión a la base de datos
. No usar el ORM de Laravel
.
T05: Listado de Tareas
Rutas: routes.php (Laravel) <br> Controlador: app/Http/Controllers/TaskController.php <br> Vista: resources/views/tasks/list.blade.php
GET /tareas/lista
Mostrar lista, ordenada descendentemente por fecha de realización
. Se debe utilizar la Clase DB (Singleton)
.
T05: Añadir Tarea (Adm)
Controlador: TaskController.php <br> Vista: resources/views/tasks/add.blade.php
GET /tareas/crear (Formulario) <br> POST /tareas/crear (Guardar)
Filtrar todos los campos en el servidor, mostrando errores y manteniendo valores enviados
.
T06: Diseño Modular
Vista principal: resources/views/layouts/master.blade.php <br> Vistas parciales para: Encabezado, Menú Lateral, Pie
.
N/A
Uso obligatorio de Blade para asegurar un diseño modular
.
T07/T08: Validación/Roles
Módulo de Sesiones: app/SessionManager.php (debe ser nativo PHP)
.
GET /login (Formulario) <br> POST /login (Validar)
Restricción: No usar el mecanismo de sesiones o cookies de Laravel
. Usar mecanismo de sesiones nativo de php para controlar accesos y diferenciar roles
.
T09: Completar Tarea (Ope)
Controlador: TaskController.php <br> Vista: resources/views/tasks/complete.blade.php
GET /tareas/completar/{id} <br> POST /tareas/completar/{id}
Solo se permite al operario modificar: fecha de realización, estado, y anotaciones posteriores
.
T11: Instalador
Carpeta: install/ <br> Fichero: install/index.php <br> Config: app/config.php
Comando (Ejemplo): Abrir URL /install/index.php
El instalador debe crear y modificar parámetros (BD, usuario, clave) en app/config.php
. Debe borrar todas las tablas existentes y recrear la estructura
. 5. Estructura de Carpetas Recomendada y Archivos Clave
El proyecto debe seguir el patrón MVC
e integrarse parcialmente con la estructura de Laravel. Es obligatorio separar en carpetas las operaciones asociadas a la base de datos
.

/ProyectoRaiz
|-- /app/
| |-- config.php <-- **Archivo Clave:** Configuración de BD (Ubicación, Usuario, Clave, BD) [27, 38].
| |-- DB.php <-- **Archivo Clave:** Capa de Abstracción de Datos (Patrón Singleton) [3, 14].
| |-- SessionManager.php <-- Gestión de Sesiones (nativo PHP, no Laravel) [4, 37].
| |-- /Http/
| |-- /Controllers/
| |-- TaskController.php <-- Lógica del negocio / Control de tareas (Adm/Ope) [14].
| |-- AuthController.php <-- Lógica de validación de usuario [20].
|-- /install/
| |-- index.php <-- **Archivo Clave:** Script de Instalación y creación de tablas [26].
|-- /public/
| |-- index.php <-- Controlador Frontal (de Laravel, gestionando peticiones) [4, 5].
|-- /resources/
| |-- /views/
| |-- /tasks/
| | |-- list.blade.php
| | |-- add.blade.php
| |-- /layouts/
| |-- master.blade.php <-- Diseño modular (Encabezado, Menú, Pie) [13].
|-- routes/
| |-- web.php (o routes.php) <-- Rutas (rutas.php mencionado, asumiendo su función en Laravel) [14].
|-- /storage/
|-- /uploads/ <-- Carpeta para Ficheros Resumen/Fotos (no accesible por URL sin validación) [39].

6. Requisitos de Entorno y Cómo Montar el Entorno Paso a Paso
   Requisitos de Entorno
   Categoría

Requisito
Versión (Asumida/Requerida)
Sistema Operativo
Windows, Máquina virtual Windows, Máquina virtual Linux
.
N/A
Runtime/Servidor
PHP (ejecución de código en el servidor)
. Servidor Web (ej. Apache vía XAMPP)
.
N/A
Base de Datos
Almacén de datos relacional (ej. MySQL/MariaDB)
.
N/A
Frameworks/Librerías
Laravel (Uso parcial para Blade y Controlador Frontal)
. Gestor de plantillas Blade
.
N/A
Herramientas
GIT (Control de versiones)
. Editor de código.
N/A
Pasos para Montar el Entorno (Asumiendo XAMPP y GIT)

1. Instalación del Entorno Servidor (XAMPP/VM): Instalar XAMPP en la máquina local (Windows)
   o configurar una Máquina Virtual Linux/Windows para simular el entorno de servidor
   .
2. Configuración de GIT: Crear un repositorio GIT y publicarlo en un repositorio público (tipo GIT) para que el profesor pueda acceder
   . Se recomienda tener un repositorio GIT asociado a GitHub
   .
3. Diseño del Esquema de BD: Definir con claridad el modelo de datos (Esquema de base de datos), utilizando herramientas como MySQL Workbench
   . Asegurarse de que cada registro tenga un campo "id" numérico autogenerado como clave principal
   .
4. Configuración Inicial: Crear el archivo app/config.php para definir la ubicación, usuario, clave y nombre de la base de datos
   .
5. Estrategia de Pruebas y Comandos para Ejecutarlas
   La estrategia de pruebas se centra en la verificación obligatoria del filtrado en el servidor y la funcionalidad demostrable.
   Tipo de Prueba

Estrategia / Objetivo
Comandos (No proporcionados en el PDF, se asume ejecución manual)
Referencia
Pruebas Unitarias/Integración (Filtrado)
Asegurar que todos los campos se filtren en el servidor antes de guardar, mostrando el error pertinente y reteniendo el valor enviado
. Pruebas específicas para formatos (NIF/CIF correcto, Teléfono válido, CP 5 números, Correo correcto, Fecha posterior a la actual)
.
Ejecución manual a través de formularios
Pruebas de Flujo (E2E funcionales)
Verificar el funcionamiento sin errores de las operaciones CRUD (Añadir, Modificar, Eliminar, Listar, Paginación, Completar tarea)
. Verificar la correcta restricción de acceso por roles (Adm. vs. Operario)
.
Ejecución manual navegando por la aplicación
Pruebas de Aceptación
Demostrar el funcionamiento de la aplicación al profesor antes de la fecha límite
. Realizar un cuestionario sobre las tareas y defender la práctica
.
N/A 8. Plan de Despliegue Paso a Paso
El plan de despliegue se centra en la preparación del entorno de ejecución (XAMPP/VM) y el uso del instalador
. No se detallan pasos para CI/CD o Rollback en los fuentes.
Paso
Tarea
Comandos (Asumidos, si aplican)
Verificación (Checklist)
Referencia
P1
Preparación del Entorno
Iniciar servicios (Apache, MySQL) en XAMPP
.
Aplicación funciona en máquina Windows + XAMMP
.
P2
Configuración de Conexión
Editar app/config.php con los datos de acceso a la BD (Usuario, Clave, Esquema)
.
Fichero de configuración accesible y con datos correctos
.
P3
Ejecución del Instalador
Navegar a la carpeta install y arrancar index.php (vía URL)
.
El instalador borra tablas existentes y crea la estructura de las tablas de la aplicación
.
P4
Verificación del Acceso
Acceder a la página inicial de la aplicación (Lista de tareas o menú)
.
La aplicación carga sin errores de sintaxis
.
P5
Entrega Final
Comprimir el proyecto excluyendo las carpetas vendor y .git
. Subir a Moodle
.
Entrega realizada en plazo
. 9. Estimación de Esfuerzo (Horas) y Calendario Sugerido (Sprints)
El documento no proporciona estimación de esfuerzo en horas (solo fechas límite)
. Se sugiere un calendario basado en los hitos de la Planificación de realización
.
Hito / Sprint
Tareas Incluidas (Ejemplos de T)
Fecha de Finalización Sugerida
Sprint 1: Fundamentos (T01-T03)
Definir modelo de datos (T02), Crear y validar formularios (T03).
29 de octubre
Sprint 2: Core BD (T04-T05)
Capa de abstracción BD (T04), Operaciones CRUD completas (T05).
Jueves 13 de noviembre
Sprint 3: Integración y Modularidad (T06, T10)
Adaptar proyecto a Laravel (Controlador Frontal/MVC), Vistas Blade modulares (T06), Paginación (T10).
Martes 18 de noviembre
Sprint 4: Seguridad y Roles (T07-T09)
Control de acceso con sesiones (T07), Diferenciación de roles (T08), Completar tarea de operarios (T09).
Miércoles 26 de noviembre
Sprint 5: Avanzado y Entrega (T11-T14)
Instalador (T11), Funcionalidades avanzadas restantes (T12), Documentación (T13).
Martes 2 de diciembre 10. Riesgos Identificados en el PDF y Acciones para Mitigarlos
Riesgo Identificado
Acción de Mitigación (Obligatoria/Sugerida)
Referencia
Ficheros adjuntos accesibles públicamente.
Almacenar ficheros en una carpeta en el servidor, no accesible desde ninguna ruta URL sin validación previa
.
Almacenamiento inseguro de credenciales en cookies.
No almacenar datos de usuario y clave sin encriptar
. Considerar el uso de criptografía en PHP (openssl_encrypt) si se recuerda la clave.
Errores en la modificación de tareas.
Confirmar la operación de eliminación interactuando con el servidor (página de confirmación, no JavaScript)
.
Copia de código.
La copia resultará en la inmediata eliminación de la parte copiada, división de la nota entre implicados y penalización
.
Pérdida de trabajo o falta de seguimiento.
Utilizar un repositorio GIT (asociado a GitHub) para seguimiento y recuperación del trabajo
. 11. Lista de Preguntas Abiertas / Datos que Faltan
Esta lista detalla aspectos del proyecto que se mencionan pero no se definen completamente, requiriendo decisiones por parte del desarrollador o del profesor.

1. Algoritmos de Validación: ¿Qué algoritmos específicos se deben utilizar para verificar la validez del NIF o CIF? Se menciona que se pueden usar "múltiples algoritmos existentes en la web"
   .
2. Lista de Operarios: Se indica que la lista de operarios se mostrará con un campo select, pero no es preciso que estén almacenados en la BBDD
   . Se requiere definir dónde se almacenará esta lista (código, archivo de configuración, etc.).
3. Uso de Controles HTML5: Aunque se desaconseja el uso de controles y atributos avanzados de HTML5 (required, max, etc.), si se utilizan, se debe incluir una opción para permitir desactivarlos
   . Se debe confirmar si su uso está permitido bajo la condición de desactivación.
4. Temas de Configuración: ¿Qué otros valores, además de los campos por defecto (provincia, población, zona) y el número de elementos en lista, se considerarán configurables en el apartado 2.5?
   .
5. Programa de Documentación: Se recomienda utilizar DoxyGen por su sencillez, aunque se mencionan ApiGen y PHPDoc
   . Se requiere confirmación si cualquiera es válido o si Doxygen es obligatorio.
6. Separación Futura de Interfaz: Se realiza una interfaz común para clientes, administrativos y operarios, pero se debe tener presente que las operaciones son diferentes y podrían separarse en el futuro
   . Se requiere claridad sobre cómo esta previsión debe afectar el diseño actual.
