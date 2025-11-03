    #Crea un archivo llamado mi_archivo.txt y escribe 3 frases (una por línea).

    #Vuelve a abrirlo en modo lectura y muestra:
        #Todos el contenido de golpe.
        #La primera línea con .readline().
        #Todas las líneas en una lista con .readlines().

    #Abre el archivo en modo anexar y agrega una nueva línea.

# 👉 Objetivo: practicar read, readline, readlines, write y el uso de modo como variable.


archivo = "2DAW/Python/T3/act1/mi_archivo.txt"
modo = "r" # r es lectura , a agrega contenido al final , w sobreescribe el archivo 
encoding = "utf-8"

contador = 0
with open(archivo, "r") as archivo:
    for numero_linea, linea in enumerate(archivo, start=1):
        contador = numero_linea

for i in range(5):
#! por algun motivo no permite meter directamente la variable de archivo ni en este ni en el siguiente
    with open("2DAW/Python/T3/act1/mi_archivo.txt", "a") as f:
        suma = contador + int(i)
        f.write(f"Linea : {suma}  \n")


# Con el with podemos asegurarnos de que el archivo se cierre automaticamente
with open("2DAW/Python/T3/act1/mi_archivo.txt", "r", encoding=encoding) as f:
    contenido = f.read()
    print(contenido)