import matplotlib.pyplot as plt

# 1. Datos ficticios
dias = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom']
temp_max = [28, 30, 32, 29, 31, 33, 30]
temp_min = [18, 20, 19, 17, 21, 22, 19]

# 2. Creación del gráfico
# Establecemos el tamaño de la figura (ancho x alto en pulgadas)
plt.figure(figsize=(10, 6))

# Dibujamos las líneas:
# 'marker' añade puntos en cada día, 'label' es para la leyenda
plt.plot(dias, temp_max, marker='o', label='Temp Máxima', color='red', linewidth=2)
plt.plot(dias, temp_min, marker='s', label='Temp Mínima', color='blue', linestyle='--', linewidth=2)

# 3. Personalización del gráfico
plt.title('Evolución de la Temperatura durante la Semana', fontsize=14, fontweight='bold')
plt.xlabel('Días de la semana', fontsize=12)
plt.ylabel('Temperatura (°C)', fontsize=12)

# Añadimos la leyenda (usa los 'label' definidos arriba)
plt.legend(loc='upper left')

# Añadimos una cuadrícula para que sea más fácil leer los valores
plt.grid(True, linestyle=':', alpha=0.7)

# 4. Guardar y mostrar
# Guardamos como imagen PNG
plt.savefig('clima_semanal.png')
print("✅ Gráfico guardado correctamente como 'clima_semanal.png'")

# Mostramos el gráfico por pantalla
plt.show()
