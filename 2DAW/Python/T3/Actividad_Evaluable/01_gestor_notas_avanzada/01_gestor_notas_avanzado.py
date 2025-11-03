# Requisitos:

# Al iniciar:

#     Si el archivo existe → mostrar todas las notas numeradas (ej: 1. Comprar pan).
#     Si no existe → mostrar un mensaje y crearlo vacío automáticamente.

# Menú de opciones: El usuario debe poder elegir entre:

#     1. Ver notas → muestra todas las notas guardadas.
#     2. Añadir nota → pide un texto y lo añade al final del archivo.
#     3. Eliminar nota → pide el número de la nota y la elimina del archivo.
#     4. Salir.

# Control de errores:

#     Manejar FileNotFoundError y PermissionError.
#     Manejar errores de entrada del usuario (ej: si introduce un número inválido al eliminar).

# Detalles técnicos:

#     Usa siempre una variable modo para abrir los ficheros ("r", "a", "w").
#     Usa with open(...) para asegurar el cierre automático.
#     Documenta el programa con un docstring al inicio y comentarios en las funciones



ARCHIVO = "2DAW/Python/T3/act1/Actividad_Evaluable/01_gestor_notas_avanzada/notas.txt"

try:
    with open(ARCHIVO, "x"):
        print("\n✅ Archivo creado vacío\n")
except FileExistsError:
    # Pass Sirve para marcar la posici'on para indicar que no se necesita ejecutar
    pass


def agregar():
    nota = input("\nNota: ")
    # Abrir el archivo en modo lectura y escritura ('a+')
    # El modo 'a+' permite agregar al final y también leer el archivo
    with open(ARCHIVO, 'a+') as f:
        # para que se vaya al principio del archivo
        f.seek(0)
        lineas = f.readlines()
        resta = str(len(lineas))
        # Escribir la nueva línea al final del archivo
        f.write("\n" + resta + ". " + nota)

    print("\n✅ Nota añadida\n")

def eliminar():
    with open(ARCHIVO) as f:
        lineas = f.readlines()
    ver()
    num = int(input("Número a borrar: "))

    # Se usa para eliminar variables, elementos de listas, o atributos de objetos
    del lineas[num]

    with open(ARCHIVO, "w") as f:
        f.writelines(lineas)
    print("\n✅ Nota borrada\n")

def ver():
    with open(ARCHIVO, "r") as archivo:
        contenido = archivo.read()
        print(contenido)

while True:
    print("\n-- Menu --\n\n1) Ver\n2) Añadir\n3) Eliminar\n4) Salir\n")
    op = input("\nOpción: ")
    match op:
        case "1":
            ver()
        case "2":
            agregar()
        case "3":
            eliminar()
        case "4":
            break
        case _:
            print("\n❌ Opción no válida\n")
