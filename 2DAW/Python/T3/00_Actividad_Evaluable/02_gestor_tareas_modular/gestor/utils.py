def mostrar_menu():
    print("\n--- Gestor de Tareas ---")
    print("1. Ver tareas")
    print("2. Añadir tarea")
    print("3. Salir")

def pedir_opcion():
    while True:
        opcion = input("Elige una opción: ")
        if opcion in ("1", "2", "3"):
            return opcion
        else:
            print("Opción no válida. Por favor, elige 1, 2 o 3.")
