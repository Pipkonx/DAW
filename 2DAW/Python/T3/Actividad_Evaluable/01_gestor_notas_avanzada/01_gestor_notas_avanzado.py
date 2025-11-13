AUTO_ID = 1
estudiantes = []

def mostrar_menu():
    print("\n==================================================")
    print("     SISTEMA DE GESTIÓN DE ESTUDIANTES")
    print("==================================================")
    print("1. Agregar estudiante")
    print("2. Mostrar todos los estudiantes")
    print("3. Buscar estudiante por ID")
    print("4. Calcular promedio de notas")
    print("5. Estudiantes aprobados")
    print("6. Informe completo")
    print("7. Salir")
    print("==================================================")

def obtener_entero(mensaje):
    while True:
        try:
            return int(input(mensaje))
        except Exception:
            print("Valor no válido. Inténtalo de nuevo.")

def obtener_float(mensaje):
    while True:
        try:
            valor = input(mensaje)
            return float(valor.replace(',', '.'))
        except Exception:
            print("Valor no válido. Inténtalo de nuevo.")

def calcular_promedio(notas):
    return round(sum(notas) / len(notas), 2) if notas else 0.0

def determinar_estado(promedio):
    return "Aprobado" if promedio >= 5.0 else "Suspenso"

def crear_estudiante(nombre, edad, notas):
    global AUTO_ID
    promedio = calcular_promedio(notas)
    estado = determinar_estado(promedio)
    est = {
        'id': AUTO_ID,
        'nombre': nombre,
        'edad': edad,
        'notas': notas,
        'promedio': promedio,
        'estado': estado
    }
    AUTO_ID += 1
    return est

def agregar_estudiante():
    print("\n--- AGREGAR NUEVO ESTUDIANTE ---")
    nombre = input("Nombre del estudiante: ")
    edad = obtener_entero("Edad del estudiante: ")
    notas = []
    print("Ingrese las notas del estudiante (ingrese -1 para terminar):")
    while True:
        n = obtener_float("Nota: ")
        if n == -1:
            break
        notas.append(n)

    est = crear_estudiante(nombre, edad, notas)
    estudiantes.append(est)
    print(f"✅ Estudiante '{nombre}' agregado con ID: {est['id']}")

def mostrar_estudiantes():
    if not estudiantes:
        print("No hay estudiantes.")
        return
    print(f"{'ID':<5}{'Nombre':<25}{'Edad':<6}{'Promedio':<10}{'Estado':<10}")
    for e in estudiantes:
        print(f"{e['id']:<5}{e['nombre']:<25}{e['edad']:<6}{e['promedio']:<10}{e['estado']:<10}")

def buscar_estudiante_por_id(estudiante_id):
    for e in estudiantes:
        if e['id'] == estudiante_id:
            #recordar que el join era para unir los elementos de una lista en una cadena
            print(f"ID: {e['id']}\nNombre: {e['nombre']}\nEdad: {e['edad']}\nNotas: {', '.join(str(x) for x in e['notas'])}\nPromedio: {e['promedio']}\nEstado: {e['estado']}")
            return e
    print("Estudiante no encontrado.")
    # hacemos un return none para por si no encuentra no siga
    return None

def calcular_promedio_clase():
    if not estudiantes:
        print("No hay estudiantes.")
        return
    promedios = [e['promedio'] for e in estudiantes]
    promedio_general = round(sum(promedios) / len(promedios), 2) if promedios else 0.0
    print(f"Promedios individuales: {', '.join(str(p) for p in promedios)}")
    print(f"Promedio general de la clase: {promedio_general}")

def estudiantes_aprobados():
    if not estudiantes:
        print("No hay estudiantes.")
        return
    aprobados = [e for e in estudiantes if e['estado'] == 'Aprobado']
    if not aprobados:
        print("No hay estudiantes aprobados.")
        return
    print("Estudiantes aprobados:")
    for e in aprobados:
        print(f"{e['id']} - {e['nombre']} ({e['promedio']})")

def generar_informe_completo():
    if not estudiantes:
        print("No hay estudiantes.")
        return
    nombres = [e['nombre'] for e in estudiantes]
    promedios = [e['promedio'] for e in estudiantes]
    estados = [e['estado'] for e in estudiantes]
    print("Informe completo:")
    # el enumerate es para obtener el indice de la lista
    # el zip es para unir distintos elementos de una lista
    for i, (n, p, s) in enumerate(zip(nombres, promedios, estados), start=1):
        print(f"{i}. {n} - {p} - {s}")
    promedio_general = round(sum(promedios) / len(promedios), 2)
    mejor = max(promedios)
    peor = min(promedios)
    print(f"Promedio general: {promedio_general}")
    print(f"Mejor promedio: {mejor}")
    print(f"Peor promedio: {peor}")
    # el iter para convertir la lista en un iterador
    it = iter(estudiantes)
    primeros = []
    for _ in range(3):
        try:
            primeros.append(next(it))
        except StopIteration:
            break
    print("Primeros tres estudiantes:")
    for e in primeros:
        print(f"{e['id']} - {e['nombre']} - {e['promedio']} - {e['estado']}")

def main():
    while True:
        mostrar_menu()
        op = input("Opción: ")
        match op:
            case "1":
                agregar_estudiante()
            case "2":
                mostrar_estudiantes()
            case "3":
                estudiante_id = obtener_entero("ID del estudiante: ")
                buscar_estudiante_por_id(estudiante_id)
            case "4":
                calcular_promedio_clase()
            case "5":
                estudiantes_aprobados()
            case "6":
                generar_informe_completo()
            case "7":
                break
            case _:
                print("❌ Opción no válida")

main()
