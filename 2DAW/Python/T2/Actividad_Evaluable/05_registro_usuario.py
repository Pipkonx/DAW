# Crea una función registrar_usuario(nombre, edad, ciudad="Madrid").
# La función debe mostrar en pantalla: "Usuario: [nombre], Edad: [edad], Ciudad: [ciudad]".
# Debe poder llamarse con:
#     Todos los argumentos posicionales.
#     Algún argumento omitido, usando el valor por defecto.
#     Argumentos nombrados en distinto orden.
# Incluye un docstring en la función.
# Desde el programa principal, llama a la función al menos 3 veces con diferentes combinaciones de argumentos.

def registrar_usuario(nombre, edad, ciudad="Madrid"):
    # Ejemplo de docstring en la funcion, aqui se registra y se muestra los datos de un usuario
    print(f"Usuario: {nombre}, Edad: {edad}, Ciudad: {ciudad}")

registrar_usuario("Rafa", 25)
registrar_usuario("Luis", 30, "Barcelona")
registrar_usuario(nombre="Marta", edad=28)