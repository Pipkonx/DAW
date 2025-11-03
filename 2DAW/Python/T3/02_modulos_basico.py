#     Pide al usuario un número.
#     Usa math para mostrar su raíz cuadrada y factorial.
#     Usa os para mostrar el directorio actual y los archivos en él.
#     Usa sys para mostrar la versión de Python y el sistema operativo.

# 👉 Objetivo: familiarizarse con módulos estándar de Python.



numero = int(input("Dime un numero: "))

import math
raizCuadrada = math.sqrt(numero)
factorial = math.factorial(numero)
print("\nraiz cuadrada = ", raizCuadrada)
print("factorial es = ", factorial)


# Con esto mostramos los archivos en el directorio actual con os
import os 
print("\nArchivos en la carpeta actual: ")
for nombre in os.listdir("."):
    print("-", nombre)


# Mostramos la version de python en el sistema operativo con sys
import sys
print("\nversion de python : ", sys.version)
print("sistema operativo: ", sys.platform)