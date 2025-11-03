import os
import sys
import math

def menu():
    print("\n --- MENU DE HERRAMIENTAS --- \n 1. Calculos matematicos \n 2. Explorador de directorios \n 3. Consultar a API (requests) \n 4. Salir")
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
            menu()


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
    import requests
    print("\n --- CONSULTA a API ---\n 1. Hacer petición GET\n 2. Detalles y errores\n 3. Volver")
    op2 = int(input("\n\tQue opción desea realizar: "))

    match op2:
        case 1:
            print("\n --- Petición GET ---")
            try:
                respuesta = requests.get("https://api.github.com")
                print(f"\nCódigo de estado: {respuesta.status_code}")
                print(f"Tamaño de la respuesta: {len(respuesta.text)} caracteres")
                print("Primeros 200 caracteres del contenido:")
                print(respuesta.text[:200])
            except requests.exceptions.RequestException as e:
                print("\n❌ Error al realizar la petición:", e)
            api()

        case 2:
            print("\n --- Detalles y errores ---")
            print("Versión de Python:", sys.version)
            print("Sistema operativo:", sys.platform)
            api()

        case 3:
            print("\n --- VOLVER AL MENÚ PRINCIPAL ---\n")
            menu()

        case _:
            print("Opción no válida.")
            api()


def directorio():
    print("\n --- Explorador de directorios ---")
    print("Archivos en la carpeta actual:")
    for nombre in os.listdir("."):
        print("-", nombre)

    op = int(input("Desea crear una carpeta? \n 1. Si \n 2. No\nRespuesta : "))
    match op:
        case 1:
            nombre = input("Dime el nombre de la carpeta: ")
            # probamos con try catch para ver si podemos crear la carpeta sin problemas
            try:
                # mkdir es para crear la carpeta
                os.mkdir(nombre)
                print(" Exito: Carpeta creada con exito")
            except FileExistsError:
                print("❌ Error: La carpeta ya existe.")
        case 2:
            print("\nVolviendo al menu principal\n")
            menu()

menu()
