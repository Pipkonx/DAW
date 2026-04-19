import tkinter as tk
from tkinter import ttk, messagebox
import database
import validations

class AppContactos:
    def __init__(self, root):
        self.root = root
        self.root.title("Gestión de Contactos - CRUD Python + MySQL")
        self.root.geometry("800x600")
        
        # ID del contacto seleccionado para actualizar
        self.id_seleccionado = None
        
        self.configurar_estilos()
        self.crear_widgets()
        self.cargar_datos()

    def configurar_estilos(self):
        """Configura la apariencia visual usando ttk.Style."""
        style = ttk.Style()
        style.theme_use('clam') # Usamos un tema que permite mejor personalización
        
        # Estilo para botones
        style.configure("TButton", padding=6, font=("Arial", 10))
        # Estilo para la tabla
        style.configure("Treeview", font=("Arial", 10), rowheight=25)
        style.configure("Treeview.Heading", font=("Arial", 10, "bold"))

    def crear_widgets(self):
        # --- Frame Superior: Búsqueda ---
        frame_busqueda = tk.Frame(self.root, pady=10)
        frame_busqueda.pack(fill="x", padx=20)
        
        tk.Label(frame_busqueda, text="Buscar (Nombre o Tel):", font=("Arial", 10, "bold")).pack(side="left")
        self.entry_busqueda = tk.Entry(frame_busqueda)
        self.entry_busqueda.pack(side="left", padx=10, fill="x", expand=True)
        # Evento de búsqueda en tiempo real
        self.entry_busqueda.bind("<KeyRelease>", lambda e: self.cargar_datos(self.entry_busqueda.get()))

        # --- Frame Central: Formulario ---
        frame_form = tk.LabelFrame(self.root, text=" Datos del Contacto ", padx=20, pady=10)
        frame_form.pack(fill="x", padx=20, pady=10)
        
        # Grid para el formulario
        tk.Label(frame_form, text="Nombre:").grid(row=0, column=0, sticky="w", pady=5)
        self.entry_nombre = tk.Entry(frame_form, width=30)
        self.entry_nombre.grid(row=0, column=1, padx=10)
        
        tk.Label(frame_form, text="Teléfono:").grid(row=0, column=2, sticky="w", pady=5)
        self.entry_telefono = tk.Entry(frame_form, width=20)
        self.entry_telefono.grid(row=0, column=3, padx=10)
        
        tk.Label(frame_form, text="Email:").grid(row=1, column=0, sticky="w", pady=5)
        self.entry_email = tk.Entry(frame_form, width=30)
        self.entry_email.grid(row=1, column=1, padx=10)

        # --- Frame Botones ---
        frame_botones = tk.Frame(frame_form)
        frame_botones.grid(row=2, column=0, columnspan=4, pady=15)
        
        self.btn_agregar = ttk.Button(frame_botones, text="Agregar", command=self.guardar_contacto)
        self.btn_agregar.pack(side="left", padx=5)
        
        self.btn_actualizar = ttk.Button(frame_botones, text="Actualizar", command=self.actualizar_datos)
        self.btn_actualizar.pack(side="left", padx=5)
        
        self.btn_eliminar = ttk.Button(frame_botones, text="Eliminar", command=self.borrar_contacto)
        self.btn_eliminar.pack(side="left", padx=5)
        
        self.btn_limpiar = ttk.Button(frame_botones, text="Limpiar", command=self.limpiar_campos)
        self.btn_limpiar.pack(side="left", padx=5)

        # --- Frame Inferior: Tabla ---
        frame_tabla = tk.Frame(self.root)
        frame_tabla.pack(fill="both", expand=True, padx=20, pady=10)
        
        # Definición de columnas
        columnas = ("id", "nombre", "telefono", "email")
        self.tabla = ttk.Treeview(frame_tabla, columns=columnas, show="headings")
        
        # Cabeceras
        self.tabla.heading("id", text="ID")
        self.tabla.heading("nombre", text="Nombre completo")
        self.tabla.heading("telefono", text="Teléfono")
        self.tabla.heading("email", text="Correo Electrónico")
        
        # Anchos de columna
        self.tabla.column("id", width=50, anchor="center")
        self.tabla.column("nombre", width=250)
        self.tabla.column("telefono", width=120)
        self.tabla.column("email", width=250)
        
        # Scrollbar
        scroll = ttk.Scrollbar(frame_tabla, orient="vertical", command=self.tabla.yview)
        self.tabla.configure(yscrollcommand=scroll.set)
        
        self.tabla.pack(side="left", fill="both", expand=True)
        scroll.pack(side="right", fill="y")
        
        # Evento al seleccionar fila
        self.tabla.bind("<<TreeviewSelect>>", self.seleccionar_fila)

    def cargar_datos(self, filtro=""):
        """Carga los contactos de la BD a la tabla."""
        # Limpiar tabla
        for item in self.tabla.get_children():
            self.tabla.delete(item)
            
        contactos = database.obtener_contactos(filtro)
        for c in contactos:
            self.tabla.insert("", "end", values=(c['id'], c['nombre'], c['telefono'], c['email']))

    def seleccionar_fila(self, event):
        """Carga los datos de la fila seleccionada en el formulario."""
        seleccion = self.tabla.selection()
        if seleccion:
            item = self.tabla.item(seleccion)
            valores = item['values']
            self.id_seleccionado = valores[0]
            
            # Llenar entradas
            self.limpiar_campos()
            self.entry_nombre.insert(0, valores[1])
            self.entry_telefono.insert(0, valores[2])
            self.entry_email.insert(0, valores[3] if valores[3] != "None" else "")

    def limpiar_campos(self):
        self.entry_nombre.delete(0, tk.END)
        self.entry_telefono.delete(0, tk.END)
        self.entry_email.delete(0, tk.END)
        self.id_seleccionado = None

    def guardar_contacto(self):
        nombre = self.entry_nombre.get()
        telef = self.entry_telefono.get()
        email = self.entry_email.get()
        
        # Validaciones
        v_nom, m_nom = validations.validar_nombre(nombre)
        if not v_nom: return messagebox.showwarning("Atención", m_nom)
        
        v_tel, m_tel = validations.validar_telefono(telef)
        if not v_tel: return messagebox.showwarning("Atención", m_tel)
        
        v_em, m_em = validations.validar_email(email)
        if not v_em: return messagebox.showwarning("Atención", m_em)
        
        if database.insertar_contacto(nombre, telef, email):
            messagebox.showinfo("Éxito", "Contacto agregado correctamente")
            self.limpiar_campos()
            self.cargar_datos()
        else:
            messagebox.showerror("Error", "No se pudo conectar con la base de datos.")

    def actualizar_datos(self):
        if not self.id_seleccionado:
            return messagebox.showwarning("Atención", "Selecciona un contacto de la tabla primero.")
        
        nombre = self.entry_nombre.get()
        telef = self.entry_telefono.get()
        email = self.entry_email.get()
        
        # Validaciones (mismas que arriba)
        v_nom, m_nom = validations.validar_nombre(nombre)
        if not v_nom: return messagebox.showwarning("Atención", m_nom)
        v_tel, m_tel = validations.validar_telefono(telef)
        if not v_tel: return messagebox.showwarning("Atención", m_tel)
        v_em, m_em = validations.validar_email(email)
        if not v_em: return messagebox.showwarning("Atención", m_em)

        if database.actualizar_contacto(self.id_seleccionado, nombre, telef, email):
            messagebox.showinfo("Éxito", "Contacto actualizado correctamente")
            self.cargar_datos()
        else:
            messagebox.showerror("Error", "Error al actualizar.")

    def borrar_contacto(self):
        if not self.id_seleccionado:
            return messagebox.showwarning("Atención", "Selecciona un contacto para eliminar.")
        
        confirmar = messagebox.askyesno("Confirmar", "¿Seguro que quieres eliminar este contacto?")
        if confirmar:
            if database.eliminar_contacto(self.id_seleccionado):
                messagebox.showinfo("Éxito", "Contacto eliminado")
                self.limpiar_campos()
                self.cargar_datos()
            else:
                messagebox.showerror("Error", "Error al eliminar.")

if __name__ == "__main__":
    root = tk.Tk()
    app = AppContactos(root)
    root.mainloop()
