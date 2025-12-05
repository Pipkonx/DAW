# TEORIA - https://github.com/fencgut961/OPT_25_26/blob/main/TEORIA/BLOQUE_01/UD_1_8.md
# ACTIVIDAD - GITHUB https://github.com/Pipkonx/DAW/blob/main/2DAW/Python/T1/ActEvaluables/06_login.py
# GITHUB CON TODAS LAS ACTIVIDADES - https://github.com/Pipkonx/DAW/tree/main/2DAW/Python

credenciales = {}

# hacemos el buclie hasta uqe salgamos
while True:
    try:
        opcion = int(input("¿Qué quieres hacer? [1] Registrarse [2] Iniciar sesión [3] Salir: "))
        
        if opcion == 1:
            # Registramos al usuario
            usuario = input("Introduce un nombre de usuario: ")
            
            while True:
                passwd = input("Introduce una contraseña: ")
                
                if len(passwd) < 8:
                    print("Contraseña insegura ❌ Debe tener al menos 8 caracteres.")
                elif not any(char.isupper() for char in passwd):
                    print("Contraseña insegura ❌ Debe tener al menos una mayúscula.")
                elif not any(char.isdigit() for char in passwd):
                    print("Contraseña insegura ❌ Debe tener al menos un número.")
                elif not any(not char.isalnum() for char in passwd):
                    print("Contraseña insegura ❌ Debe tener al menos un símbolo.")
                else:
                    credenciales[usuario] = passwd
                    print("Usuario registrado con éxito ✅")
                    break
                    
        elif opcion == 2:
            # Inicimaoso sesion
            MAX_INTENTOS = 3
            usuario = input("Introduce un nombre de usuario: ")
            
            if usuario not in credenciales:
                print("El usuario no existe ❌")
            else:
                for intento in range(MAX_INTENTOS):
                    passwd = input("Introduce una contraseña: ")
                    if credenciales[usuario] == passwd:
                        print(f"Acceso concedido ✅ Bienvenid@, {usuario}.")
                        break
                    else:
                        print(f"Acceso denegado ⛔ Intento {intento + 1}/{MAX_INTENTOS}")
                        if intento == MAX_INTENTOS - 1:
                            print("Demasiados intentos fallidos 🚫 Regresando al menu principal")
                            
        elif opcion == 3:
            print("Saliendo")
            exit()
        else:
            print("Opción no válida. Por favor, elige 1, 2 o 3")
    except ValueError:
        print("Por favor, introduce un numero valido")
