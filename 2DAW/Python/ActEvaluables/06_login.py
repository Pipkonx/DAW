# TEORIA - https://github.com/fencgut961/OPT_25_26/blob/main/TEORIA/BLOQUE_01/UD_1_8.md
credenciales = {}

def registrarUsu():
    usuario = input("Introduce un nombre de usuario: ")
    
    # Validación de contraseña
    while True:
        passwd = input("Introduce una contraseña: ")
        
        if len(passwd) < 8:
            print("Contraseña insegura ❌. Debe tener al menos 8 caracteres.")
            continue
        elif passwd.isupper():
            print("Contraseña insegura ❌. Debe tener al menos una minúscula.")
            continue
        elif not any(char.isdigit() for char in passwd):
            print("Contraseña insegura ❌. Debe tener al menos un número.")
            continue
        elif not any(not char.isalnum() for char in passwd):
            print("Contraseña insegura ❌. Debe tener al menos un símbolo.")
            continue
        else:
            credenciales[usuario] = passwd
            print("Usuario registrado con éxito ✅")
            break

def iniciarSesion():
    usuario = input("Introduce un nombre de usuario: ")
    if usuario not in credenciales:
        print("El usuario no existe. ❌")
        return
    
    # 3 intentos de contraseña
    for intento in range(1, 4):
        passwd = input("Introduce una contraseña: ")
        if credenciales[usuario] == passwd:
            print(f"Acceso concedido ✅. Bienvenid@, {usuario}.")
            return
        else:
            print(f"Acceso denegado ⛔. Intento {intento}/3")
            if intento == 3:
                print("Demasiados intentos fallidos 🚫. Regresando al menú principal.")
                return

# El programa debe ejecutarse en bucle hasta que el usuario elija la opción Salir.
while True:
    try:
        opcion = int(input("¿Qué quieres hacer? [1] Registrarse [2] Iniciar sesión [3] Salir: "))
        
        if opcion == 1:
            registrarUsu()
        elif opcion == 2:
            iniciarSesion()
        elif opcion == 3:
            print("Saliendo del programa...")
            exit()
        else:
            print("Opción no válida. Por favor, elige 1, 2 o 3.")
    except ValueError:
        print("Por favor, introduce un número válido.")