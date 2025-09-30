#Crear una lista de frutas y luego recorrerlas mostrandolas con un indice


frutas = ["manzana", "banana", "cereza"]


enumerate(frutas)
for i , fruta in enumerate(frutas, start = 1):
    print(i, fruta)