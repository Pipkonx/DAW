
# Crea un programa que:

# Genere una lista de 20 números enteros (pueden ser introducidos manualmente o generados con range).

# Obtenga mediante comprensiones de listas:

# Una lista con los cuadrados de todos los números.
# Una lista con solo los números pares.
# Una lista con los números mayores que 10.
# Cree un diccionario que relacione cada número con su doble.

# Muestre en pantalla todos los resultados.

# Incluya un docstring explicando qué hace el programa

#creamos la lista de 20 números enteros con el range()
numeros = [i for i in range(20)]

#creamos las listas y diccionario con comprensiones de listas
cuadrados = [i**2 for i in numeros]
pares = [i for i in numeros if i%2 == 0]
mayores_10 = [i for i in numeros if i > 10]
dobles = {i:i*2 for i in numeros}

#mostramos los resultados por pantalla
print("Números:", numeros)
print("Cuadrados:", cuadrados)
print("Pares:", pares)
print("Mayores que 10:", mayores_10)
print("Dobles:", dobles)

