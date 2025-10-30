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


archivo = "2DAW/Python/T3/act1/Actividad_Evaluable/01_gestor_notas_avanzada/notas.txt"

try:
    with open(archivo, 'r') as file:
        content = file.read()
except FileNotFoundError:
    print("El fichero no existe")


# Con el with podemos asegurarnos de que el archivo se cierre automaticamente
with open("2DAW/Python/T3/act1/Actividad_Evaluable/01_gestor_notas_avanzada/notas.txt", "r") as f:
    contenido = f.read()
    print(contenido)