#     Se tienen cuatro listas:

# ! REVISAR

estudiantes = ["Ana", "Luis", "Marta", "Carlos"]
notas_matematicas = [8, 7, 9, 6]
notas_fisica = [9, 6, 10, 7]
notas_quimica = [7, 8, 9, 5]

#     Crear un diccionario vacío llamado resultado_final.

resultado_final = {}

#     Usar zip() para recorrer todas las listas al mismo tiempo y calcular:

for estudiante, matematicas, fisica, quimica in zip(estudiantes, notas_matematicas, notas_fisica, notas_quimica):
    promedio = (matematicas + fisica + quimica) / 3
    if promedio >= 6.5:
        estado = "Aprobado"
    elif promedio >= 5:
        estado = "En recuperación"
    else:
        estado = "Reprobado"

    # Guardar en el diccionario resultado_final de esta forma:
    # Clave: nombre del estudiante
    # Valor: diccionario con notas, promedio y estado
    resultado_final[estudiante] = {
        "Matemáticas": matematicas,
        "Física": fisica,
        "Química": quimica,
        "Promedio": round(promedio, 2),
        "Estado": estado
    }

#     Imprimir un reporte ordenado usando for y zip():

for estudiante, datos in resultado_final.items():
    print(f"{estudiante} - Matemáticas: {datos['Matemáticas']}, Física: {datos['Física']}, Química: {datos['Química']}, Promedio: {datos['Promedio']}, Estado: {datos['Estado']}")

# Ana - Matemáticas: 8, Física: 9, Química: 7, Promedio: 8.0, Estado: Aprobado
# Luis - Matemáticas: 7, Física: 6, Química: 8, Promedio: 7.0, Estado: Aprobado
# Marta - Matemáticas: 9, Física: 10, Química: 9, Promedio: 9.33, Estado: Aprobado
# Carlos - Matemáticas: 6, Física: 7, Química: 5, Promedio: 6.0, Estado: En recuperación
