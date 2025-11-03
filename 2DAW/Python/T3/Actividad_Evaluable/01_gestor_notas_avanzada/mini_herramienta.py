import os
import sys
import math

def menu():
    print("\n --- MENU DE HERRAMIENTAS --- \n 1. Calculos matematicos \n 2. Explorador de directorios \n 3. Consultar a API (requiests) \n 4. Salir")
    op = int(input("\n\t Que opcion desea realizar: "))
    match op:
        case 1:
            matematicas()
        case 2:
            directorio()
        case 3:
            api()
        case 4:
            print("\n -- SALIR --")
            exit()
        case _:
            print("Opción no válida")


def matematicas():
    print("\n --- Calculos matematicos ---\n 1. Raiz cuadrada\n 2. Factorial\n 3. Potencia al cuadrado \n 4. Volver")
    op2 = int(input("\n\tQue opcion desea realizar: "))

    match op2:
        case 1:
            numero = int(input("Dime un numero: "))
            print(f"\nRaiz cuadrada de {numero} es {math.sqrt(numero)}")
            matematicas()
        case 2:
            numero = int(input("Dime un numero: "))
            print(f"\nEl factorial de {numero} es {math.factorial(numero)}")
            matematicas()
        case 3:
            numero = int(input("Dime un numero: "))
            print(f"\n{numero} al cuadrado es {math.pow(numero, 2)}")
            matematicas()
        case 4:
            menu()
        case _:
            print("Opción no válida")
            matematicas()

def api():
    print("\n --- Consultar a API ---")
    print("\nVersión de Python:", sys.version)
    print("\nSistema operativo:", sys.platform)
    menu()

def directorio():
    print("\n --- Explorador de directorios ---")
    print("Archivos en la carpeta actual:")
    for nombre in os.listdir("."):
        print("-", nombre)
    menu()
    
menu()

