
    # Define una tupla con los 7 días de la semana.
    # Muestra el primer y el último día.
    # Recorre la tupla con un bucle for para mostrar todos los días.
    # Usa index() para encontrar en qué posición está el día "Miércoles".

dias_semana = ("lunes", "martes", "miercoles", "Jueves", "Viernes", "Sabado", "Domingo")

print("El primer dia de la semana es : ", dias_semana[0])
print("El ultimo dia de la semana es : ", dias_semana[-1])

print("dias de la semana: ")
for dia in dias_semana :
    print(dia)


print("EL dia de la semana miercoles se encuentra en la posicion de la lista: ", dias_semana.index("miercoles"))