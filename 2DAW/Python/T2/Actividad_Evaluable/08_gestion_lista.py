# Crea un programa que:

#     Defina una lista vacía compras.
#     Pida al usuario 5 productos y los añada a la lista con append().
#     Muestre la lista completa.
#     Pida al usuario un producto a eliminar y lo quite con remove().
#     Muestre la lista ordenada alfabéticamente con sort().
#     Incluya un docstring explicando qué hace el programa.

compras = []

CANTIDAD_PRODUCTOS = 5

for i in range(CANTIDAD_PRODUCTOS):
    # Pedimos al usuario que introduzca los productos
    producto = input(f"Introduce el {i+1} producto: ")

    #el append es para annadir un  nuevo elemento a la lista
    compras.append(producto)

print("lista de la compra: ", compras)


# input es para pedir una accion al usuario
# .remove es para eliminar un elemento de la lista
accion = input("Que producto desea eliminar: ")
compras.remove(accion)
print("lista de la compra: ", compras)

# sort() es para ordenar
compras.sort()
print("lista de la compra ordenada: ", compras)
