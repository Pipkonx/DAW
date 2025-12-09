import os

RUTA_ARCHIVO = "tareas.txt"

def cargar_tareas():
    tareas = []
    if os.path.exists(RUTA_ARCHIVO):
        with open(RUTA_ARCHIVO, "r") as f:
            for linea in f:
                tareas.append(linea.strip())
    return tareas

def guardar_tarea(tarea):
    with open(RUTA_ARCHIVO, "a") as f:
        f.write(tarea + "\n")

def listar_tareas():
    print("\n--- Tareas actuales ---")
    tareas = cargar_tareas()
    if not tareas:
        print("No hay tareas.")
    for i, tarea in enumerate(tareas):
        print(f"{i + 1}. {tarea}")
