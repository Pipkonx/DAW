from sklearn.datasets import load_iris
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler
from sklearn.neighbors import KNeighborsClassifier
from sklearn.metrics import accuracy_score, confusion_matrix, classification_report
import pandas as pd

# 1. Cargar el dataset de flores Iris
print("--- Cargando datos del dataset Iris ---")
iris = load_iris()
X = iris.data  # Características: largo y ancho de sépalos y pétalos
y = iris.target  # Especie: 0=Setosa, 1=Versicolor, 2=Virginica

# 2. Dividir los datos (80% entrenamiento, 20% prueba)
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.20, random_state=42)
print(f"Datos divididos: {len(X_train)} para entrenamiento y {len(X_test)} para prueba.")

# 3. Estandarizar los datos (Media=0, Desviación=1)
# Esto ayuda a que el algoritmo KNN funcione mucho mejor
scaler = StandardScaler()
X_train = scaler.fit_transform(X_train)
X_test = scaler.transform(X_test)

# 4. Entrenar el modelo K-Vecinos más Cercanos (KNN) con k=3
print("--- Entrenando modelo KNN (k=3) ---")
clf = KNeighborsClassifier(n_neighbors=3)
clf.fit(X_train, y_train)

# 5. Evaluar el modelo
y_pred = clf.predict(X_test)
precision = accuracy_score(y_test, y_pred)

print(f"\n✅ Precisión del modelo: {precision * 100:.2f}%")
print("\n--- Reporte de Clasificación ---")
print(classification_report(y_test, y_pred, target_names=iris.target_names))

# 6. Predicción manual por el usuario
print("\n--- ¡Ahora prueba el modelo tú mismo! ---")
try:
    s_largo = float(input("Introduce largo del sépalo (cm): "))
    s_ancho = float(input("Introduce ancho del sépalo (cm): "))
    p_largo = float(input("Introduce largo del pétalo (cm): "))
    p_ancho = float(input("Introduce ancho del pétalo (cm): "))

    # Preparamos los datos introducidos (mismo formato que el entrenamiento)
    datos_usuario = [[s_largo, s_ancho, p_largo, p_ancho]]
    datos_usuario_norm = scaler.transform(datos_usuario)

    # Realizamos la predicción
    prediccion = clf.predict(datos_usuario_norm)
    especie = iris.target_names[prediccion[0]]

    print(f"\n🔮 El modelo predice que esta flor es una: **{especie.upper()}**")

except ValueError:
    print("❌ Error: Introduce solo números válidos.")
