# Crea un script que:

#     Pida al usuario tres notas.
#     Calcule el promedio.
#     Introduzca un error lógico en el cálculo (como en el ejemplo anterior).
#     Usa el modo depuración para encontrar y corregir el error.
#     Añade un docstring al inicio explicando qué hacía mal el programa y cómo se corrigió.

#? El error que se introdujo fue que se dividió la suma de las notas entre 2 en lugar de entre 3.

nota1 = int(input("Dame una primera nota: "))
nota2 = int(input("Dame una segunda nota: "))
nota3 = int(input("Dame una tercera nota: "))

promedio = (nota1 + nota2 + nota3) / 2

print(f"El promedio delas notas es : {promedio}")
