import pandas as pd

# 1. Cargar los datos del archivo CSV
# Usamos pandas para leer el archivo ventas_2023.csv
try:
    df = pd.read_csv('ventas_2023.csv')
    print("✅ Archivo cargado correctamente.\n")
except FileNotFoundError:
    print("❌ Error: No se ha encontrado el archivo 'ventas_2023.csv'.")
    exit()

# 2. Calcular el total de ventas por producto
# Convertimos las columnas a tipos numéricos por seguridad
df['cantidad'] = pd.to_numeric(df['cantidad'])
df['precio_unitario'] = pd.to_numeric(df['precio_unitario'])

# Creamos una columna 'total_fila' multiplicando cantidad por precio
df['total_fila'] = df['cantidad'] * df['precio_unitario']

# Agrupamos por producto y sumamos los totales
ventas_por_producto = df.groupby('producto')['total_fila'].sum()
print("--- Total de ventas por producto ---")
print(ventas_por_producto)
print("\n")

# 3. Encontrar el mes con mayores ventas
# Convertimos la columna 'fecha' a tipo datetime
df['fecha'] = pd.to_datetime(df['fecha'])

# Extraemos el mes y calculamos el total mensual
df['mes'] = df['fecha'].dt.month
ventas_por_mes = df.groupby('mes')['total_fila'].sum()

# Buscamos el mes con el valor máximo
mes_top = ventas_por_mes.idxmax()
print(f"--- El mes con mayores ventas fue el mes {mes_top} ---")
print(f"Total de ese mes: {ventas_por_mes[mes_top]} €\n")

# 4. Aplicar un 10% de descuento a productos con precio unitario > 50
# Creamos una copia del dataframe original para el nuevo análisis
df_descuento = df.copy()

# Definimos una función simple para aplicar el descuento
def aplicar_descuento(precio):
    if precio > 50:
        return precio * 0.90  # Quitamos el 10%
    return precio

# Aplicamos la función a la columna precio_unitario
df_descuento['precio_unitario'] = df_descuento['precio_unitario'].apply(aplicar_descuento)

# Recalculamos el total de fila con el nuevo precio
df_descuento['total_fila'] = df_descuento['cantidad'] * df_descuento['precio_unitario']

print("--- Aplicado descuento del 10% a productos caros (>50€) ---")
print(df_descuento[['producto', 'precio_unitario', 'total_fila']].head())
print("\n")

# 5. Guardar los resultados en un archivo Excel
try:
    # Necesitas la librería 'openpyxl' instalada para esto
    with pd.ExcelWriter('ventas_analizadas.xlsx') as writer:
        ventas_por_producto.to_excel(writer, sheet_name='Ventas por Producto')
        df_descuento.to_excel(writer, sheet_name='Detalle con Descuento', index=False)
    print("🚀 Resultados guardados con éxito en 'ventas_analizadas.xlsx'")
except Exception as e:
    print(f"❌ Error al guardar el archivo Excel: {e}")
