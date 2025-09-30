#     Define una función area_rectangulo(base, altura) que devuelva el área del rectángulo.

#     Define una función perimetro_rectangulo(base, altura) que devuelva el perímetro.

#     Desde el programa principal, pide al usuario la base y la altura, y muestra:
#         El área calculada.
#         El perímetro calculado.

# 👉 Objetivo: practicar funciones con un solo resultado devuelto.


def area_rectangulo(base, altura) :
    print(f"El area de un rectangulo es : {base * altura}")

def perimetro_rectangulo(base, altura):
    print(f"El perimetro de un rectangulo es : {2 * (base + altura)}")

base = int(input("Introduce la base: "))
altura = int(input("Introduce la altura: "))

area_rectangulo(base, altura)
perimetro_rectangulo(base, altura)