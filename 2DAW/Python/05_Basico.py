def filtrar(numeros):
    resultado = []
    
    for num in numeros:
        if num > 0:
            resultado.append(num)
    return resultado

lista=[2,4,-5,7,-18]

print(filtrar(lista))

