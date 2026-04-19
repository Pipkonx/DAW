import numpy as np

# 1. Simular el experimento de física
# Generamos 1000 valores de temperatura con media 25 y desviación 3
print("--- Iniciando simulación de temperaturas ---")
media = 25
desviacion = 3
datos = np.random.normal(media, desviacion, 1000)

print(f"Generados {len(datos)} valores.")

# 2. Filtrar valores atípicos (fuera de media ± 2*desviación)
# El rango aceptable es [25 - 6, 25 + 6] -> [19, 31]
umbral_bajo = media - 2 * desviacion
umbral_alto = media + 2 * desviacion

# Aplicamos el filtro usando indexación booleana de NumPy
datos_filtrados = datos[(datos >= umbral_bajo) & (datos <= umbral_alto)]

print(f"Valores después de eliminar atípicos: {len(datos_filtrados)}")
print(f"Se eliminaron {len(datos) - len(datos_filtrados)} valores fuera del rango [{umbral_bajo:.1f}, {umbral_alto:.1f}]\n")

# 3. Calcular estadísticas básicas
promedio = np.mean(datos_filtrados)
mediana = np.median(datos_filtrados)
desv_puntual = np.std(datos_filtrados)

print("--- Estadísticas de los datos filtrados ---")
print(f"Promedio: {promedio:.2f} °C")
print(f"Mediana: {mediana:.2f} °C")
print(f"Desviación Estándar: {desv_puntual:.2f} °C\n")

# 4. Normalizar los datos al rango [0, 1]
# La fórmula es: (valor - min) / (max - min)
min_val = np.min(datos_filtrados)
max_val = np.max(datos_filtrados)

datos_normalizados = (datos_filtrados - min_val) / (max_val - min_val)

print("--- Normalización completada ---")
print(f"Valor mínimo original: {min_val:.2f} -> Normalizado: {np.min(datos_normalizados):.2f}")
print(f"Valor máximo original: {max_val:.2f} -> Normalizado: {np.max(datos_normalizados):.2f}")
print(f"Primeros 5 valores normalizados: {datos_normalizados[:5]}\n")
