# MEMORIA TÉCNICA: COMPETICIÓN DE SALTOS EN TRAMPOLÍN

**Asignatura:** Programación en Python  
**Actividad:** Práctica de Manipulación de Datos con Pandas  
**Autor:** [Tu Nombre y Apellidos]

---

## 1. Introducción
El presente documento detalla el diseño y desarrollo de una aplicación en Python para la gestión de una competición de saltos de trampolín. El objetivo principal es aplicar la librería **Pandas** para el manejo de estructuras de datos tabulares, integrando lógica de procesamiento estadístico y generación de clasificaciones.

## 2. Decisiones de Diseño

### 2.1. Estructura de Datos (Data Panda)
Se ha optado por utilizar el componente `DataFrame` de la librería Pandas como estructura central. Esta decisión se justifica por la facilidad que ofrece para realizar ordenaciones, filtrados y cálculos columna a columna, lo cual es esencial para determinar los puestos de la competición de forma dinámica.

### 2.2. Lógica de Puntuación
Para cumplir con la normativa de la competición, se ha implementado un algoritmo que:
1. Genera 5 notas aleatorias por salto (pasos de 0.5).
2. Ordena las notas para identificar y descartar los valores extremos (máximo y mínimo).
3. Suma las notas centrales para obtener la base del salto.

### 2.3. Aleatoriedad y Dificultad
Se utiliza el módulo `random` para simular la variabilidad de las puntuaciones de los jueces. El **Grado de Dificultad (GD)** se define al inicio del programa de forma aleatoria (entre 0.1 y 1.0) y se aplica uniformemente a todos los participantes en cada ronda, garantizando la equidad deportiva según el enunciado.

## 3. Implementación de Actividades

### Actividad 1: Listado General
Se implementó un bucle que recolecta los datos de hasta 8 saltadores. Los datos se consolidan en el DataFrame y se ordenan de mayor a menor puntuación total. Se incluyó obligatoriamente a **Raúl Capablanca** con un ranking de 2780 puntos.

### Actividad 2: Mejores Saltos por Ronda
Mediante el método `.idxmax()` de Pandas, el programa identifica automáticamente la fila con la mayor puntuación en cada una de las tres columnas de saltos, mostrando los nombres en mayúsculas de forma clara.

### Actividad 3: Clasificación Provisional
Se diseñó una vista intermedia que suma únicamente el primer y segundo salto. Para cumplir con el requisito estético, se utilizó la selección de columnas de Pandas para excluir el Ranking en esta salida específica.

### Actividad 4: Pódium (Top 5)
Utilizando el método `.head(5)`, se extraen los cinco primeros saltadores tras la ordenación final, detallando su nombre, ranking y país de origen.

### Actividad 5: Gestión de Descalificación (Anti-doping)
Se implementó un filtro de exclusión booleano (`df[df["NOMBRE"] != "RAÚL"]`) para eliminar a Raúl Capablanca tras su positivo en el control. Posteriormente, se recalculan los puestos para mantener la integridad de la tabla de clasificación.

## 4. Conclusiones técnicas
La modularización del proyecto en funciones separadas permite que la lógica sea escalable y fácil de leer. El uso de Pandas ha reducido significativamente la complejidad del código comparado con el uso de listas de diccionarios tradicionales, permitiendo un manejo robusto de la persistencia en memoria de los datos ficticios.

---
*Este documento ha sido generado como soporte técnico para la entrega de la práctica en Moodle Centros.*
