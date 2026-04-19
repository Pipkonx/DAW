# 📂 Actividades Faltantes (Implementadas)

Este directorio contiene las actividades que no estaban presentes en el repositorio original. Se han desarrollado siguiendo criterios de simplicidad, legibilidad y cumpliendo estrictamente con los enunciados proporcionados.

---

## 📝 Lista de Actividades y Enunciados

### 1. [Pandas - Análisis de datos de ventas](./01_Pandas_Ventas)
**Enunciado:** Crea un script que procese un archivo CSV `ventas_2023.csv` con las columnas: fecha, producto, cantidad, precio_unitario.
- Calcula el total de ventas por producto.
- Encuentra el mes con mayores ventas.
- Genera un nuevo DataFrame con un 10% de descuento aplicado a los productos con precio unitario mayor a 50.
- Guarda los resultados en un nuevo archivo Excel `ventas_analizadas.xlsx`.

### 2. [NumPy - Simulación de datos científicos](./02_NumPy_Simulacion)
**Enunciado:** Simula un experimento de física donde se miden 1000 valores de temperatura con una distribución normal (media=25°C, desviación=3°C).
- Genera un array de NumPy con los valores simulados.
- Aplica un filtro para eliminar valores atípicos (fuera de ±2 desviaciones estándar).
- Calcula el promedio, mediana y desviación estándar de los datos filtrados.
- Normaliza los datos restantes al rango [0, 1].

### 3. [Tkinter - Calculadora de IMC](./03_Tkinter_IMC)
**Enunciado:** Desarrolla una interfaz gráfica con Tkinter que calcule el Índice de Masa Corporal (IMC).
- Incluye dos campos de entrada para peso (kg) y altura (m).
- Agrega un botón "Calcular" que muestre el IMC y su categoría (bajo peso, normal, sobrepeso, obesidad).
- Implementa un botón "Limpiar" para reiniciar los campos.
- Añade una etiqueta con la fórmula `IMC = peso / altura²`.

### 4. [Flask - API de tareas (To-Do)](./04_Flask_Tasks)
**Enunciado:** Crea una API REST con Flask para gestionar una lista de tareas.
- Implementa los endpoints:
    - GET `/tasks`: Devuelve todas las tareas.
    - POST `/tasks`: Agrega una nueva tarea (recibe JSON con title y status).
    - PUT `/tasks/<id>`: Actualiza el estado de una tarea.
    - DELETE `/tasks/<id>`: Elimina una tarea.
- Usa una lista en memoria para almacenar las tareas (sin base de datos).
- Incluye manejo básico de errores.

### 5. [Matplotlib - Visualización de clima](./05_Matplotlib_Clima)
**Enunciado:** Genera un gráfico de líneas con Matplotlib que muestre la temperatura máxima y mínima de una ciudad durante 7 días.
- Usa datos ficticios: `dias = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom']`, `temp_max = [28, 30, 32, 29, 31, 33, 30]`, `temp_min = [18, 20, 19, 17, 21, 22, 19]`.
- Muestra ambas líneas en el mismo gráfico con leyenda y colores diferenciados.
- Añade título, etiquetas en ejes X/Y y una grilla.
- Guarda el gráfico como `clima_semanal.png`.

### 6. [Scikit-learn - Clasificador de flores (Iris)](./06_Sklearn_Iris)
**Enunciado:** Entrena un modelo de clasificación con el dataset Iris de Scikit-learn.
- Divide los datos en 80% entrenamiento y 20% prueba.
- Estandariza los datos con StandardScaler.
- Entrena un modelo de K-Vecinos más Cercanos (KNN) con k=3.
- Evalúa el modelo calculando precisión, matriz de confusión y reporte de clasificación.
- Permite al usuario ingresar manualmente características de una flor para predecir su especie.

### 7. [PyGame - Juego de evitar obstáculos](./07_PyGame_Obstaculos)
**Enunciado:** Crea un juego simple con PyGame donde un jugador (cuadrado) esquiva obstáculos que caen desde la parte superior de la pantalla.
- Controla al jugador con las teclas izquierda/derecha.
- Genera obstáculos (rectángulos) aleatorios en posición y velocidad.
- Incrementa la velocidad de los obstáculos cada 10 segundos.
- Muestra un contador de tiempo y puntaje (obstáculos esquivados).
- El juego termina si un obstáculo toca al jugador, mostrando "Game Over".

### 8. [Python Panda - Competición de Saltos](./08_Pandas_Competicion)
**Enunciado Especial:** Gestión de saltos en trampolín con Pandas.
- Lógica de puntuación de jueces (eliminar nota alta/baja).
- Factor multiplicativo de dificultad aleatorio.
- Inclusión obligatoria del saltador **Raúl Capablanca** (Cuba, Ranking 2780).
- Generación de listados ordenados, mejores saltos por ronda y gestión de puestos.
- Simulación de descalificación por anti-doping.

### 9. [Aplicación CRUD para Gestión de Contactos](./09_CRUD_Contactos)
**Enunciado Completo:** Aplicación de escritorio con Python, Tkinter y MySQL.
- Operaciones CRUD completas (Agregar, Ver, Modificar, Eliminar).
- Búsqueda en tiempo real (cláusula LIKE).
- Validaciones con Regex (Nombre, Teléfono, Correo).
- Interfaz mejorada con `ttk.Style` y Scrollbars.
- Organización modular del código.

---

## 🚀 Cómo empezar

He incluido un archivo **[requirements.txt](./requirements.txt)** con todas las librerías necesarias. Puedes instalarlas todas usando:

```bash
pip install -r requirements.txt
```

Cada actividad tiene su propia carpeta con instrucciones específicas en caso de ser necesario.
