# Define una función sumar(a, b) que imprima la suma de dos números.
# Define una función saludo_personal(nombre, saludo="Hola") que muestre un saludo personalizado.
# Llama a ambas funciones con distintos parámetros desde el programa principal

def sumar(a, b):
    print(a + b)

def saludo_personal(nombre, saludo = "Hola"):
    print(f"{saludo}, {nombre}")

sumar(2, 3)
saludo_personal("Rafa")