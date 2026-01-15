import time
import os

from conexion import obtener_precio_actual
from alertas import comprobar_alerta

def iniciar_vigilante():
    print("================================")
    print("   EL VIGILANTE DE CRIPTOS      ")
    print("================================")
    
    # 1 Pedimos datos al usuario
    # el strip es el trim de js
    # Y EL UPPER es para que no importe las mayos o las minusculas
    simbolo = input("\nQue moneda o accion quieres vigilar? (ej: BTC-USD, XRP): ").strip().upper()
    
    try:
        umbral = float(input("A que precio quieres poner la alerta?: "))
    except Exception:
        print("Error: Tienes que introducir un numero valido.")
        return

    print("\nCuando quieres recibir el aviso?")
    print("1. Cuando sea MAYOR o IGUAL")
    print("2. Cuando sea MENOR o IGUAL")

    opcion = input("Elige una opcion 1 o 2: ")
    
    tipo_alerta = "sube" if opcion == "1" else "baja"
    
    print(f"--- Iniciando vigilancia para {simbolo} ---")
    print(f"Objetivo: Avisar cuando {tipo_alerta} de {umbral}")
    print("Pulsas Ctrl + C para salir del programa.\n")

    # 2 Bucle infinito para vigilar
    try:
        while True:
            precio = obtener_precio_actual(simbolo)
            
            if precio is not None:
                # strftime es para formatear la hora en formato HH:MM:SS
                # https://www.programiz.com/python-programming/datetime/strftime
                print(f"[{time.strftime('%H:%M:%S')}] Precio actual de {simbolo}: {precio} $")
                
                # Comprobamos y avisamos
                if comprobar_alerta(precio, umbral, tipo_alerta):
                    print("\n*********************************")
                    print(f"!!! ALERTA LOGRADA: {precio} $ !!!")
                    print("*********************************\n")
                    break 
            else:
                print("No se ha podido obtener el precio. Tienes que escribir bien el Ticker")
            
            # Esperamos un poco para no saturar la API , para cerrar tiene que darle control+c un par de veces
            time.sleep(10)

    except Exception:
        print("\n\nInterrupción por el usuario.")
    
    print("Cerrando el programa...")


# el __name__ es para que solo se ejecute si se llama a este archivo
if __name__ == "__main__":
    iniciar_vigilante()
