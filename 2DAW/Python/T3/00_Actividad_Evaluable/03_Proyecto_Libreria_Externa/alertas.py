def comprobar_alerta(precio_actual, umbral, tipo_alerta):
    """
    comparamos el precio con el actual
    
    tipo_alerta puede ser:
    - 'sube': mayor o igual
    - 'baja': menor o igual
    """
    if tipo_alerta == "sube":
        if precio_actual >= umbral:
            return True
    elif tipo_alerta == "baja":
        if precio_actual <= umbral:
            return True
            
    # no se cumple, puues nada
    return False
