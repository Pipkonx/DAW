def dividir(a,b):
    try:
        return a / b
    except ZeroDivisionError:
            raise ValueError("No se puede dividir por cero")
print(dividir(10,0))