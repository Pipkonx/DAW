from typing import List, Dict, Optional, Iterator

SIGUIENTE_ID: int = 1
ESTUDIANTES: List[Dict] = []


def mostrar_menu() -> None:
    """Muestra el menú principal del sistema."""
    print("=" * 50)
    print("      SISTEMA DE GESTIÓN DE ESTUDIANTES")
    print("=" * 50)
    print("1. Agregar estudiante")
    print("2. Mostrar todos los estudiantes")
    print("3. Buscar estudiante por ID")
    print("4. Calcular promedio de notas (general)")
    print("5. Estudiantes aprobados")
    print("6. Informe completo")
    print("7. Salir")
    print("=" * 50)


def obtener_entero(mensaje: str) -> int:
    """Solicita un número entero por consola y valida la entrada."""
    while True:
        entrada = input(mensaje).strip()
        try:
            valor = int(entrada)
            return valor
        except ValueError:
            print("❌ Entrada inválida. Debe ser un número entero.")


def obtener_float(mensaje: str) -> float:
    """Solicita un número float por consola y valida la entrada."""
    while True:
        entrada = input(mensaje).strip().replace(",", ".")
        try:
            valor = float(entrada)
            return valor
        except ValueError:
            print("❌ Entrada inválida. Debe ser un número (float).")


def calcular_promedio(notas: List[float]) -> float:
    """Calcula el promedio de una lista de notas. Si la lista está vacía, retorna 0.0."""
    return round(sum(notas) / len(notas), 2) if notas else 0.0


def determinar_estado(promedio: float) -> str:
    """Determina el estado del estudiante: 'Aprobado' si promedio >= 5.0, en caso contrario 'Suspenso'."""
    return "Aprobado" if promedio >= 5.0 else "Suspenso"


def crear_estudiante(nombre: str, edad: int, notas: List[float]) -> Dict:
    """Crea la estructura de datos de un estudiante sin asignar el ID (se asigna al agregar)."""
    promedio = calcular_promedio(notas)
    estado = determinar_estado(promedio)
    return {
        "nombre": nombre,
        "edad": edad,
        "notas": notas,
        "promedio": promedio,
        "estado": estado,
    }


def agregar_estudiante() -> None:
    """Agrega un nuevo estudiante solicitando los datos por consola y asignando un ID autoincremental."""
    global SIGUIENTE_ID

    print("\n--- AGREGAR NUEVO ESTUDIANTE ---")
    nombre = input("Nombre del estudiante: ").strip()
    while not nombre:
        print("❌ El nombre no puede estar vacío.")
        nombre = input("Nombre del estudiante: ").strip()

    edad = obtener_entero("Edad del estudiante: ")
    if edad < 0:
        print("⚠️ La edad no puede ser negativa. Se ajusta a 0.")
        edad = 0

    print("Ingrese las notas del estudiante (ingrese -1 para terminar):")
    notas: List[float] = []
    while True:
        nota = obtener_float("Nota: ")
        if nota == -1:
            break
        if 0.0 <= nota <= 10.0:
            notas.append(round(nota, 2))
        else:
            print("❌ La nota debe estar entre 0 y 10 (o -1 para terminar).")

    estudiante = crear_estudiante(nombre, edad, notas)
    estudiante["id"] = SIGUIENTE_ID
    SIGUIENTE_ID += 1

    ESTUDIANTES.append(estudiante)
    print(f"✅ Estudiante '{nombre}' agregado con ID: {estudiante['id']}")


def mostrar_estudiantes(estudiantes: Optional[List[Dict]] = None) -> None:
    """Muestra los estudiantes en formato de tabla. Si no se pasa lista, usa la global."""
    lista = estudiantes if estudiantes is not None else ESTUDIANTES
    if not lista:
        print("\n📭 No hay estudiantes para mostrar.")
        return

    print("\nID  | Nombre                       | Edad | Promedio | Estado")
    print("-" * 65)
    for s in sorted(lista, key=lambda x: x.get("id", 0)):
        print(
            f"{str(s.get('id', '-')).ljust(3)} | "
            f"{s['nombre'][:27].ljust(27)} | "
            f"{str(s['edad']).rjust(4)} | "
            f"{format(s['promedio'], '.2f').rjust(8)} | "
            f"{s['estado']}"
        )
    print(f"\nTotal de estudiantes: {len(lista)}")


def buscar_estudiante_por_id(estudiante_id: int) -> Optional[Dict]:
    """Busca un estudiante por su ID único."""
    for s in ESTUDIANTES:
        if s.get("id") == estudiante_id:
            return s
    return None


def calcular_promedios_y_general() -> None:
    """Obtiene la lista de promedios de todos los estudiantes y muestra el promedio general."""
    if not ESTUDIANTES:
        print("\n📭 No hay estudiantes registrados para calcular promedios.")
        return

    promedios = [s["promedio"] for s in ESTUDIANTES]
    promedio_general = calcular_promedio(promedios)
    print("\n-- CALCULAR PROMEDIO NOTAS --")
    print(f"Promedios individuales: {promedios}")
    print(f"Promedio general de la clase: {promedio_general:.2f}")


def mostrar_estudiantes_aprobados() -> None:
    """Muestra la lista de estudiantes aprobados usando comprensión de listas."""
    aprobados = [s for s in ESTUDIANTES if s["estado"] == "Aprobado"]
    print("\n-- ESTUDIANTES APROBADOS --")
    if not aprobados:
        print("📭 No hay estudiantes aprobados.")
        return
    mostrar_estudiantes(aprobados)


def generar_informe_completo() -> None:
    """Genera un informe con listas de nombres, promedios y estado usando zip y enumerate.

    Además, muestra el promedio general, el mejor y el peor promedio. Demuestra el uso
    de un iterador para mostrar los tres primeros estudiantes.
    """
    if not ESTUDIANTES:
        print("\n📭 No hay estudiantes para generar el informe.")
        return

    nombres = [s["nombre"] for s in ESTUDIANTES]
    promedios = [s["promedio"] for s in ESTUDIANTES]
    estados = [s["estado"] for s in ESTUDIANTES]

    print("\n-- INFORME COMPLETO --")
    print("Índice | Nombre                       | Promedio | Estado")
    print("-" * 65)
    for indice, (nombre, promedio, estado) in enumerate(zip(nombres, promedios, estados), start=1):
        print(
            f"{str(indice).rjust(5)} | "
            f"{nombre[:27].ljust(27)} | "
            f"{format(promedio, '.2f').rjust(8)} | "
            f"{estado}"
        )

    promedio_general = calcular_promedio(promedios)
    mejor = max(ESTUDIANTES, key=lambda s: s["promedio"]) 
    peor = min(ESTUDIANTES, key=lambda s: s["promedio"])

    print("\nEstadísticas:")
    print(f"Promedio general: {promedio_general:.2f}")
    print(f"Mejor promedio: {mejor['promedio']:.2f} ({mejor['nombre']})")
    print(f"Peor promedio: {peor['promedio']:.2f} ({peor['nombre']})")

    print("\nPrimeros 3 estudiantes (usando iterador):")
    it: Iterator[Dict] = iter(ESTUDIANTES)
    mostrados = 0
    while mostrados < 3:
        try:
            s = next(it)
            print(
                f"ID {s['id']}: {s['nombre']} | Edad {s['edad']} | "
                f"Promedio {s['promedio']:.2f} | {s['estado']}"
            )
            mostrados += 1
        except StopIteration:
            break


def ejecutar() -> None:
    """Bucle principal del programa."""
    while True:
        mostrar_menu()
        opcion = obtener_entero("Seleccione una opción (1-7): ")

        if opcion == 1:
            agregar_estudiante()
        elif opcion == 2:
            mostrar_estudiantes()
        elif opcion == 3:
            est_id = obtener_entero("Ingrese el ID del estudiante: ")
            estudiante = buscar_estudiante_por_id(est_id)
            if estudiante:
                mostrar_estudiantes([estudiante])
            else:
                print(f"❌ No se encontró ningún estudiante con ID {est_id}.")
        elif opcion == 4:
            calcular_promedios_y_general()
        elif opcion == 5:
            mostrar_estudiantes_aprobados()
        elif opcion == 6:
            generar_informe_completo()
        elif opcion == 7:
            print("👋 Saliendo del sistema. ¡Hasta pronto!")
            break
        else:
            print("❌ Opción inválida. Intente nuevamente.")

if __name__ == "__main__":
    ejecutar()