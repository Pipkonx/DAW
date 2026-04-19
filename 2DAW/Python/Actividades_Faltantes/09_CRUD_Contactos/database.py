import mysql.connector
from mysql.connector import Error

def crear_conexion():
    """Establece la conexión con la base de datos MySQL."""
    try:
        conexion = mysql.connector.connect(
            host='localhost',
            user='root',       # Cambia esto si tienes otra configuración
            password='',       # Pon tu contraseña de MySQL aquí
            database='contact_db'
        )
        if conexion.is_connected():
            return conexion
    except Error as e:
        print(f"Error al conectar a MySQL: {e}")
        return None

def obtener_contactos(filtro=""):
    """Obtiene la lista de contactos, opcionalmente filtrada por nombre o teléfono."""
    conexion = crear_conexion()
    if conexion:
        cursor = conexion.cursor(dictionary=True)
        if filtro:
            # Usamos la cláusula LIKE para la búsqueda en tiempo real
            query = "SELECT * FROM contactos WHERE nombre LIKE %s OR telefono LIKE %s"
            valor = (f"%{filtro}%", f"%{filtro}%")
            cursor.execute(query, valor)
        else:
            cursor.execute("SELECT * FROM contactos")
        
        resultados = cursor.fetchall()
        conexion.close()
        return resultados
    return []

def insertar_contacto(nombre, telefono, email):
    """Inserta un nuevo contacto en la BD."""
    conexion = crear_conexion()
    if conexion:
        cursor = conexion.cursor()
        query = "INSERT INTO contactos (nombre, telefono, email) VALUES (%s, %s, %s)"
        cursor.execute(query, (nombre, telefono, email))
        conexion.commit()
        conexion.close()
        return True
    return False

def actualizar_contacto(id_contacto, nombre, telefono, email):
    """Actualiza los datos de un contacto existente."""
    conexion = crear_conexion()
    if conexion:
        cursor = conexion.cursor()
        query = "UPDATE contactos SET nombre=%s, telefono=%s, email=%s WHERE id=%s"
        cursor.execute(query, (nombre, telefono, email, id_contacto))
        conexion.commit()
        conexion.close()
        return True
    return False

def eliminar_contacto(id_contacto):
    """Elimina un contacto de la BD."""
    conexion = crear_conexion()
    if conexion:
        cursor = conexion.cursor()
        query = "DELETE FROM contactos WHERE id=%s"
        cursor.execute(query, (id_contacto,))
        conexion.commit()
        conexion.close()
        return True
    return False
