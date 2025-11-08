📋 Contexto del Proyecto

Programa en Python para administrar la información de los estudiantes de los estudiantes de un instituto y realizar análisis académicos básicos.

 

El sistema debe ser modular, eficiente y fácil de mantener, por lo que deberás aplicar todos los conceptos de programación aprendidos hasta el momento.

 

 

📊 Requisitos Funcionales

1. Gestión de Datos de Estudiantes

El sistema debe permitir:

 

✅ Agregar nuevos estudiantes con: nombre, edad y lista de notas

✅ Mostrar todos los estudiantes en formato de tabla

✅ Buscar estudiantes por ID único

✅ Almacenar la información en estructuras de datos apropiadas

 

2. Cálculos Académicos

El sistema debe calcular:

✅ Promedio individual de cada estudiante

✅ Estado (Aprobado/Suspenso) basado en promedio ≥ 5.0

✅ Promedio general de la clase

✅ Lista de estudiantes aprobados

 

3. Informes y Análisis

El sistema debe generar:

 

✅ Informe completo con estadísticas

✅ Listado de estudiantes aprobados

✅ Demostración de funcionalidades avanzadas

 

🔧 Especificaciones Técnicas

Estructura de Datos del Estudiante

Cada estudiante debe representarse como un diccionario con:

{

    'id': 1,                      # Entero único, autoincremental

    'nombre': "Ana García",       # String

    'edad': 20,                   # Entero

    'notas': [7.5, 8.0, 6.5],    # Lista de floats

    'promedio': 7.33,            # Float calculado

    'estado': "Aprobado"         # String calculado

}

Funciones:

mostrar_menu()

obtener_entero(mensaje)

obtener_float(mensaje)

 

crear_estudiante(nombre, edad, notas): Retorna diccionario

calcular_promedio(notas) para un estudiante.

determinar_estado(promedio): Retorna string (Aprobado/Suspenso)

 

 

agregar_estudiante()

mostrar_estudiantes()

buscar_estudiante_por_id(estudiante_id)

 

generar_informe_completo(): Usa zip() y enumerate()

 

y las que necesites

 

🚀 Características Avanzadas Requeridas

1. Comprensión de Listas

2. Uso de zip() y enumerate()

3. Manejo de Iteradores

4. Gestión de Ámbito. Variable globales

 

📝 Flujo del Programa

Menú Principal

==================================================

      SISTEMA DE GESTIÓN DE ESTUDIANTES

==================================================

1. Agregar estudiante

2. Mostrar todos los estudiantes

3. Buscar estudiante por ID

4. Calcular promedio de notas

5. Estudiantes aprobados

6. Informe completo

7. Salir

==================================================

 

--- AGREGAR NUEVO ESTUDIANTE ---

Nombre del estudiante: Laura Martínez

Edad del estudiante: 22

Ingrese las notas del estudiante (ingrese -1 para terminar):

Nota: 8.5

Nota: 7.0

Nota: 9.0

Nota: -1

✅ Estudiante 'Laura Martínez' agregado con ID: 4

        Cada estudiante se representa como un diccionario

Se utilizará una variable global para obtener los id.

--CALCULAR PROMEDIO NOTAS—

Obtiene una lista con todos los promedios de notas de los estudiantes y muestra el promedio general.

--ESTUDIANTES APROBADOS—

Obtiene una lista de los estudiantes aprobados (utilizar comprensión de listas)  y los muestra en pantalla.

--INFORME COMPLETO—

Obtener un informe con listas de nombres, promedios y estado y utilizar zip, enumerate para recórrela. Mostrar además el promedio general, el mejor y el peor promedio.

Utilizar un iterador para ver los tres  primeros estudiantes.

 

🎯 Resultados de Aprendizaje Evaluados

RA1. Utiliza sintaxis básica, estructuras de control y operadores en Python

RA2. Funciones, estructuras de datos y manejo de colecciones

💡 Consejos para la Implementación

Planifica antes de programar: Diseña las estructuras de datos primero

 

Desarrolla incrementalmente: Implementa una función a la vez y verifica que funciona

 

Prueba exhaustivamente: Verifica todos los casos posibles (listas vacías, datos inválidos, etc.)

 

Documenta mientras programas: Escribe los docstrings inmediatamente después de cada función

 

Reutiliza código: Usa funciones existentes cuando sea posible