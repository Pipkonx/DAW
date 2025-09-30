# Crea un script que contenga 4 funciones separadas:

#     sumar(a, b) → devuelve la suma.
#     restar(a, b) → devuelve la resta.
#     multiplicar(a, b) → devuelve la multiplicación.
#     dividir(a, b) → devuelve la división (controlando la división por cero).

# El programa principal debe:

#     Pedir dos números al usuario.
#     Llamar a cada función y mostrar los resultados.
#     Incluir un docstring explicativo en cada función.


def sumar(a, b):
    # Suma dos numeros y devuelve el numero
    return a + b


def restar(a, b):
    # resta dos numeros y devuelve el numero
    return a - b


def multiplicar(a, b):
    # multipica dos numeros y devuelve el numero
    return a * b


def dividir(a, b):
    # divide dos numeros y devuelve el numero
    if b == 0:
        return "Error: Division por cero"
    else:
        return a / b


primero = int(input("Introduce el primer numero: "))
segundo = int(input("Introduce el segundo numero: "))

print(f"La suma de {primero} y {segundo} es: {sumar(primero, segundo)}")
print(f"La resta de {primero} y {segundo} es: {restar(primero, segundo)}")
print(f"La multiplicacion de {primero} y {segundo} es: {multiplicar(primero, segundo)}")
print(f"La division de {primero} y {segundo} es: {dividir(primero, segundo)}")
