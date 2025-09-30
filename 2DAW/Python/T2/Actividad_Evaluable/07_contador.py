# Crea un script que:

#     Defina una variable global contador = 0.

#     Implemente tres funciones:
#         incrementar() → suma 1 al contador.
#         decrementar() → resta 1 al contador.
#         mostrar_contador() → imprime el valor actual.

#     Use la palabra clave global para modificar el contador dentro de las funciones.

#     Desde el programa principal, llama a las funciones en este orden:
#         incrementar() dos veces.
#         decrementar() una vez.
#         mostrar_contador().

#     Añade un docstring en cada función explicando lo que hace.

contador = 0

def incrementar():
    # suma 1 al contador
    global contador
    contador+=1

def decrementar():
    # resta 1 al contador
    global contador
    contador -=1

def mostrar_contador():
    # muestra el contador
    print(contador)

mostrar_contador()
incrementar()
mostrar_contador()
decrementar()
mostrar_contador()