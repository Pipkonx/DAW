num1 = float(input("Dime un primer numero: "))
num2 = float(input("Dime un segundo numero: "))
operacion = input("Dime que operacion desea realizar (suma, resta, multiplicar, dividir): ")

match operacion:
    case "suma":
        print(f"El resultado de la suma de {num1} y {num2} es: {int(num1) + int(num2)}")
    case "resta":
        print(f"El resultado de la resta de {num1} y {num2} es: {int(num1) - int(num2)}")
    case "multiplicar":
        print(f"El resultado de la multiplicacion de {num1} y {num2} es: {int(num1) * int(num2)}")
    case "dividir":
        print(f"El resultado de dividir de {num1} y {num2} es: {int(num1) / int(num2)}")
    case _:
        print("Operacion no valida")