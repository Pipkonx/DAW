import tkinter as tk
from tkinter import messagebox

def calcular_imc():
    """Función que recoge los datos y calcula el IMC."""
    try:
        # Obtenemos peso y altura de las entradas y los convertimos a número
        # Reemplazamos coma por punto por si el usuario usa formato europeo
        peso = float(entrada_peso.get().replace(",", "."))
        altura = float(entrada_altura.get().replace(",", "."))
        
        if altura <= 0:
            messagebox.showerror("Error", "La altura debe ser mayor que cero")
            return
            
        # Fórmula: IMC = peso / altura²
        imc = peso / (altura ** 2)
        
        # Determinamos la categoría según el resultado
        if imc < 18.5:
            categoria = "Bajo peso"
        elif 18.5 <= imc < 25:
            categoria = "Normal"
        elif 25 <= imc < 30:
            categoria = "Sobrepeso"
        else:
            categoria = "Obesidad"
            
        # Mostramos el resultado en la etiqueta
        resultado_valor.config(text=f"{imc:.2f}", fg="blue")
        resultado_categoria.config(text=f"Categoría: {categoria}", fg="blue")
        
    except ValueError:
        # Si el usuario introduce algo que no sea un número
        messagebox.showerror("Error", "Por favor, introduce números válidos para peso y altura.")

def limpiar():
    """Limpia los campos de entrada y los resultados."""
    entrada_peso.delete(0, tk.END)
    entrada_altura.delete(0, tk.END)
    resultado_valor.config(text="-", fg="black")
    resultado_categoria.config(text="Categoría: -", fg="black")

# Crear la ventana principal
ventana = tk.Tk()
ventana.title("Calculadora de IMC")
ventana.geometry("300x350")

# Estilo y fuentes
fuente_label = ("Arial", 10, "bold")
fuente_titulo = ("Arial", 12, "bold")

# Título
tk.Label(ventana, text="CALCULADORA DE IMC", font=fuente_titulo).pack(pady=10)

# Campo Peso
tk.Label(ventana, text="Peso (kg):", font=fuente_label).pack()
entrada_peso = tk.Entry(ventana, justify="center")
entrada_peso.pack(pady=5)

# Campo Altura
tk.Label(ventana, text="Altura (m):", font=fuente_label).pack()
entrada_altura = tk.Entry(ventana, justify="center")
entrada_altura.pack(pady=5)

# Fórmula Informativa
tk.Label(ventana, text="Fórmula: IMC = peso / altura²", font=("Arial", 8, "italic"), fg="gray").pack()

# Botones
tk.Button(ventana, text="Calcular", command=calcular_imc, bg="#4CAF50", fg="white", width=15).pack(pady=10)
tk.Button(ventana, text="Limpiar", command=limpiar, bg="#f44336", fg="white", width=15).pack()

# Área de Resultados
tk.Label(ventana, text="Tu IMC:", font=fuente_label).pack(pady=(15, 0))
resultado_valor = tk.Label(ventana, text="-", font=("Arial", 16, "bold"))
resultado_valor.pack()
resultado_categoria = tk.Label(ventana, text="Categoría: -", font=("Arial", 10))
resultado_categoria.pack(pady=5)

# Ejecutar la aplicación
ventana.mainloop()
