import pandas as pd
import random

# Actividad: Competición de Saltos (Pandas)

def generar_nota():
    """Genera una nota aleatoria entre 0 y 10 en pasos de 0.5."""
    return random.randint(0, 20) / 2.0

def calcular_puntos_salto(notas):
    """Quita la nota más alta y la más baja, y suma las restantes."""
    notas_ordenadas = sorted(notas)
    notas_finales = notas_ordenadas[1:-1] # Quitamos el primero y el último
    return sum(notas_finales)

def ejecutar_competicion():
    print("--- 🏆 COMPETICIÓN DE SALTOS EN TRAMPOLÍN ---")
    
    # Configuración inicial
    num_saltadores = int(input("Introduce el número de saltadores (máximo 8): "))
    if num_saltadores > 8: num_saltadores = 8
    
    # Grado de dificultad aleatorio entre 0 y 1 (igual para todos en cada salto)
    dificultades = [round(random.uniform(0.1, 1.0), 2) for _ in range(3)]
    
    lista_saltadores = []
    
    # Aseguramos que Raúl Capablanca esté presente
    saltadores_nombres = [
        {"nombre": "RAÚL", "apellido": "CAPABLANCA", "ranking": 2780, "pais": "Cuba"},
        {"nombre": "PEDRO", "apellido": "GARCÍA", "ranking": 2500, "pais": "España"},
        {"nombre": "ANA", "apellido": "MARTÍNEZ", "ranking": 2300, "pais": "México"},
        {"nombre": "JOHN", "apellido": "DOE", "ranking": 2100, "pais": "USA"},
        {"nombre": "MARÍA", "apellido": "LÓPEZ", "ranking": 2600, "pais": "Argentina"},
        {"nombre": "LUIS", "apellido": "PÉREZ", "ranking": 2200, "pais": "Chile"},
        {"nombre": "ELENA", "apellido": "SANZ", "ranking": 2400, "pais": "Francia"},
        {"nombre": "TOM", "apellido": "SMITH", "ranking": 2150, "pais": "Reino Unido"}
    ]
    
    # Seleccionamos el número de saltadores indicado
    participantes = saltadores_nombres[:num_saltadores]
    
    for p in participantes:
        datos_p = {
            "NOMBRE": p["nombre"],
            "APELLIDO": p["apellido"],
            "RANKING": p["ranking"],
            "PAIS": p["pais"]
        }
        
        totales_saltos = []
        for i in range(3): # 3 saltos
            notas_jueces = [generar_nota() for _ in range(5)]
            puntos_base = calcular_puntos_salto(notas_jueces)
            resultado_salto = puntos_base * dificultades[i]
            datos_p[f"SALTO{i+1}"] = round(resultado_salto, 2)
            totales_saltos.append(resultado_salto)
            
        datos_p["TOTAL"] = round(sum(totales_saltos), 2)
        lista_saltadores.append(datos_p)
        
    # Crear DataFrame
    df = pd.DataFrame(lista_saltadores)
    
    # Ordenar por TOTAL y asignar PUESTO
    df = df.sort_values(by="TOTAL", ascending=False)
    df["PUESTO"] = range(1, len(df) + 1)
    
    # Actividad 1: Listado de salida ordenado
    print("\n--- CLASIFICACIÓN GENERAL ---")
    print(df[["NOMBRE", "APELLIDO", "RANKING", "SALTO1", "SALTO2", "SALTO3", "TOTAL", "PUESTO"]])
    
    # Actividad 2: Identidad del mejor salto por ronda
    print("\n--- MEJORES SALTOS POR RONDA ---")
    for i in range(1, 4):
        col = f"SALTO{i}"
        mejor_fila = df.loc[df[col].idxmax()]
        nombre_completo = f"{mejor_fila['NOMBRE']} {mejor_fila['APELLIDO']}"
        print(f"El saltador \"{nombre_completo.upper()}\" hizo el mejor salto {i} obteniendo: {mejor_fila[col]} puntos.")

    # Actividad 3: Clasificación provisional (segunda ronda)
    print("\n--- CLASIFICACIÓN PROVISIONAL (RONDA 2) ---")
    df["TOTAL_PROV"] = df["SALTO1"] + df["SALTO2"]
    df_prov = df.sort_values(by="TOTAL_PROV", ascending=False)
    # Mostramos sin el ranking como pide el enunciado
    print(df_prov[["NOMBRE", "APELLIDO", "SALTO1", "SALTO2", "TOTAL_PROV"]])

    # Actividad 4: Top 5 final con País
    print("\n--- TOP 5 FINAL ---")
    top_5 = df.head(5)
    print(top_5[["PUESTO", "NOMBRE", "APELLIDO", "RANKING", "PAIS"]])

    # Actividad 5: Descalificación de Raúl Capablanca
    print("\n--- CLASIFICACIÓN TRAS DESCALIFICACIÓN (RAÚL CAPABLANCA) ---")
    df_final = df[df["NOMBRE"] != "RAÚL"].copy()
    # Recalculamos puestos
    df_final["PUESTO"] = range(1, len(df_final) + 1)
    print(df_final[["NOMBRE", "APELLIDO", "RANKING", "SALTO1", "SALTO2", "SALTO3", "TOTAL", "PUESTO"]])

if __name__ == "__main__":
    ejecutar_competicion()
