import re

def validar_nombre(nombre):
    """
    El nombre es obligatorio, debe tener solo letras y espacios.
    """
    if not nombre.strip():
        return False, "El nombre no puede estar vacío."
    
    # Expresión regular para solo letras (incluye tildes y ñ) y espacios
    if not re.match(r"^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$", nombre):
        return False, "El nombre solo debe contener letras y espacios."
    
    return True, ""

def validar_telefono(telefono):
    """
    El teléfono debe tener entre 7 y 15 dígitos numéricos.
    """
    # Expresión regular: solo números, de 7 a 15 dígitos
    if not re.match(r"^\d{7,15}$", telefono):
        return False, "El teléfono debe tener entre 7 y 15 números (sin espacios)."
    
    return True, ""

def validar_email(email):
    """
    El email es opcional, pero si existe debe tener formato válido.
    """
    if not email.strip():
        return True, "" # Opcional
    
    # Expresión regular estándar para email
    patron = r'^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$'
    if not re.match(patron, email):
        return False, "El formato del correo electrónico no es válido."
    
    return True, ""
