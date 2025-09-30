
    # Crea una lista con al menos 5 frutas.
    # Muestra la primera y la última fruta.
    # Cambia una fruta por otra.
    # Añade una nueva fruta al final con append().
    # Elimina una fruta con remove().
    # Muestra la lista final en pantalla.

frutas = ["manzana", "platano", "naranja", "pera", "uva"]
print("La primera fruta es : ",frutas[0])
print("La ultima fruta es : ",frutas[-1])

print("La fruta en la posicion 2 es (antes de cambiar): ",frutas[2])
frutas[2] = "mango"
print("La fruta en la posicion 2 es : ", frutas[2])

#annadimos una fruta al final con append()

frutas.append("fresa")
print(frutas)

#Eliminamos una fruta con remove()
frutas.remove("pera")
print(frutas)

