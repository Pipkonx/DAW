print("Calculo de horas invertidas en el proyecto")
print("Introduce las horas y el precio por hora (horas = 0 para terminar)")

total = 0
while True:
    horas = int(input("horas: "))
    if horas == 0:
        break
    precio = int(input("precio por hora: "))
    total += horas * precio

if total > 1000:
    porcentaje_descuento = 10
    descuento = total * 0.1
elif total >= 500:
    porcentaje_descuento = 5
    descuento = total * 0.05
else:
    porcentaje_descuento = 0
    descuento = 0

print("======================")
print(" RESUMEN FINAL DEL PROYECTO ")
print("======================")
print(f"Total acumulado (sin descuento): {total} €")
print(f"Porcentaje de descuento aplicado: {porcentaje_descuento}%")
print(f"Descuento aplicado: {descuento} €")
print(f"Importe final: {total - descuento} €")
