import time
import os

from conexion import obtener_precio_actual
from alertas import comprobar_alerta

def limpiar_pantalla():
    """Limpia la consola para que se vea todo más ordenado."""
    # 'cls' windows, 'clear' linux
    os.system('cls' if os.name == 'nt' else 'clear')

def iniciar_vigilante():
    limpiar_pantalla()
    print("================================")
    print("   EL VIGILANTE DE CRIPTOS      ")
    print("================================")
    
    # 1 Pedimos datos al usuario
    # el strip es el trim de js
    simbolo = input("\nQue moneda o accion quieres vigilar? (ej: BTC-USD, XRP): ").strip().upper()
    
    try:
        umbral = float(input("A que precio quieres poner la alerta?: "))
    except ValueError:
        print("Error: Tienes que introducir un numero valido.")
        return

    print("\nCuando quieres recibir el aviso?")
    print("1. Cuando el precio sea MAYOR o IGUAL al limite")
    print("2. Cuando el precio sea MENOR o IGUAL al limite")
    opcion = input("Elige una opcion (1 o 2): ")
    
    tipo_alerta = "sube" if opcion == "1" else "baja"
    
    limpiar_pantalla()
    print(f"--- Iniciando vigilancia para {simbolo} ---")
    print(f"Objetivo: Avisar cuando {tipo_alerta} de {umbral}")
    print("Pulsas Ctrl + C para salir del programa.\n")

    # 2 Bucle infinito para vigial
    try:
        while True:
            precio = obtener_precio_actual(simbolo)
            
            if precio is not None:
                # strftime es para formatear la hora en formato HH:MM:SS
                print(f"[{time.strftime('%H:%M:%S')}] Precio actual de {simbolo}: {precio} $")
                
                # Cmprobamos y avisamo
                if comprobar_alerta(precio, umbral, tipo_alerta):
                    print("\n*********************************")
                    print(f"!!! ALERTA LOGRADA: {precio} $ !!!")
                    print("*********************************\n")
                    break 
            else:
                print("No se ha podido obtener el precio. ¿Has escrito bien el ticker?")
    except Exception:
        print("\n\nCerrando el programa")

if __name__ == "__main__":
    iniciar_vigilante()
