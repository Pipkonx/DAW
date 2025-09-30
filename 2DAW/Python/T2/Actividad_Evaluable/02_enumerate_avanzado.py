## Objetivo
# Recorrer varias listas de estudiantes y sus notas usando enumerate(), calcular promedios y asignar calificaciones, mostrando todo en un reporte completo con índices.

estudiantes = ["Ana", "Luis", "Marta", "Carlos"]
notas_matematicas = [8, 7, 9, 6]
notas_fisica = [9, 6, 10, 7]
notas_quimica = [7, 8, 9, 5]

# Calculamos el promedio
# ? El zip() nos permite iterar sobre varias listas al mismo tiempoo
# ? el enumerate() nos permite iterar sobre una lista y obtener el indice
promedio = []
for estudiantes, notas_matematicas, notas_fisica, notas_quimica in zip(
    estudiantes, notas_matematicas, notas_fisica, notas_quimica
):
    promedio.append((notas_matematicas + notas_fisica + notas_quimica) / 3)

# Asignamos calificacciones
calificacion = []
for p in promedio:
    if p >= 6.5:
        calificacion.append("Aprobado")
    elif p >= 5:
        calificacion.append("En recuperación")
    else:
        calificacion.append("Reprobado")
    

#mostramos
for i, (estudiante, p , c) in enumerate(zip(estudiantes, promedio, calificacion), start=1):
    print(f"{i}. {estudiante}: Promedio = {p:.2f}, Calificación = {c}")