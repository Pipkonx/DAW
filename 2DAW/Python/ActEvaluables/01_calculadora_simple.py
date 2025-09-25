num1 = float(input("Dime un primer numero: "))
num2 = float(input("Dime un segundo numero: "))
operacion = input("Dime que operacion desea realizar: ")

if operacion == "suma" :
    print(f"El resultado de la suma de {num1} y {num2} es: ",int(num1) + int(num2))
elif operacion == "resta" :
    print(f"El resultado de la resta de {num1} y {num2} es: ",int(num1) - int(num2))
elif operacion == "multiplicar" :
    print(f"El resultado de la multiplicacion de {num1} y {num2} es: ",int(num1) * int(num2))
elif operacion == "dividir" :
    print(f"El resultado de la dividir de {num1} y {num2} es: ",int(num1) / int(num2))
else : 
    print("Operacion no valida")