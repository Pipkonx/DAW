    # Crea un diccionario vacío llamado agenda.

    # Pide al usuario que introduzca 3 contactos (nombre y teléfono).
    #     El nombre será la clave.
    #     El teléfono será el valor.

    # Muestra la agenda completa usando un bucle.

    # Permite al usuario buscar un contacto por nombre:
    #     Si existe, muestra el teléfono.
    #     Si no existe, muestra "Contacto no encontrado".

    # Añade un docstring explicando qué hace el programa.

agenda = []
persona = []

numero = int(input("Introduce cuantos contactos vas a introducir: "))

for i in range(numero):
    name = str(input("Introduce el nombre : "))
    tlf = int(input("Introduce el telefono : "))
    persona.append((name,tlf))

persona = (name, tlf)
for agenda in persona :
    print(name, tlf)