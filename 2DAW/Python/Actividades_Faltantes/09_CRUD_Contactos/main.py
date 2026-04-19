import tkinter as tk
from gui import AppContactos

# Punto de entrada de la aplicación CRUD de Contactos

def main():
    root = tk.Tk()
    
    # Iniciamos la aplicación pasando la ventana raíz a la clase AppContactos
    app = AppContactos(root)
    
    # Bucle principal para que la ventana permanezca abierta
    root.mainloop()

if __name__ == "__main__":
    main()
