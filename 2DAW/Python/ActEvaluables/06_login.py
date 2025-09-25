# TEORIA - https://github.com/fencgut961/OPT_25_26/blob/main/TEORIA/BLOQUE_01/UD_1_8.md


credenciales = {}


def registrarUsu():
    usuario = input("Introduce un nombre de usuario: ")
    passwd = input("Introduce una contraseña: ")
    
    
# Si no cumple las reglas → muestra un mensaje de error y vuelve a pedir la contraseña.
    if len(passwd) < 8:
        print("Contraseña insegura ❌. Debe tener al menos 8 caracteres, un número y un símbolo.")
    elif passwd.isupper():
        print("Contraseña insegura ❌. Debe tener al menos una mayúscula.")
    elif not any(char.isdigit() for char in passwd):
        print("Contraseña insegura ❌. Debe tener al menos un número.")
    elif not any(char.isalnum() for char in passwd):
        print("Contraseña insegura ❌. Debe tener al menos un carácter alfanumérico.")
        credenciales[usuario] = passwd

    print("Usuario registrado con exito ✅")




def iniciarSesion(usuario, passwd):
    usuario = input("Introduce un nombre de usuario: ")
    if usuario not in credenciales:
            print("El usuario no existe. ❌")
            exit()
    passwd = input("Introduce una contraseña: ")
    if credenciales[usuario] != passwd:
            for i in range (2, 4):
                print(f"Acceso denegado ⛔. Intento {i}/3")
                if i == 3:
                    print("Demasiados intentos fallidos 🚫. Regresando al menú principal.")
                    exit()
            else :
                print(f"Acceso concedido ✅. Bienvenid@, {usuario}.")






# El programa debe ejecutarse en bucle hasta que el usuario elija la opción Salir.

while True:
    opcion = int(input(f"Que quieres hacer? [1] Registrarse [2] Iniciar sesion [3] Salir: "))
    
    if opcion == 1 :
        registrarUsu()
    # Si falla las 3 veces seguidas → mostrar “Demasiados intentos fallidos 🚫. Regresando al menú principal.” y volver al menú.
    if opcion == 2 :
        iniciarSesion()
# El programa debe ejecutarse en bucle hasta que el usuario elija la opción Salir.
    if opcion == 3:
        print("Saliendo del programa...")
        exit()