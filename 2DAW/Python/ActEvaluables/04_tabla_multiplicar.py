# Crea un script tabla_multiplicar.py que:

#     Pida un número al usuario.
#     Use un bucle for para mostrar su tabla de multiplicar del 1 al 10.

num1 = int(input("Dame un numero: "))

for i in range(1,11) :
    print(num1, "x", i, "=", num1 * i)
