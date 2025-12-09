from gestor.utils import mostrar_menu, pedir_opcion
from gestor.tareas import listar_tareas, guardar_tarea

def main():
    while True:
        mostrar_menu()
        opcion = pedir_opcion()
        if opcion == "1":
            listar_tareas()
        elif opcion == "2":
            tarea = input("Escribe la nueva tarea: ")
            guardar_tarea(tarea)
            print("✅ Tarea guardada con éxito.")
        elif opcion == "3":
            print("👋 ¡Hasta la próxima!")
            break

if __name__ == "__main__":
    main()
