bugs = [{
        'ticket_id':'BUG-401',
        'descripcion':'Fallo de login intermitente',
        'severidad': 'alta',
        'tiempo_estimado_h':4.5},
        {
        'ticket_id' : 'BUG-402',
        'descripcion':'boton mal alineado en movil',
        'severidad':'baja',
        'tiempo_estimado_h':0.5,},
        {
        'ticket_id' : 'BUG-403',
        'descripcion':'Error en cálculo de impuestos',
        'severidad':'alta',
        'tiempo_estimado_h':6,},
        {
        'ticket_id' : 'BUG-404',
        'descripcion':'Icono pixelado en pantalla HD',
        'severidad':'media',
        'tiempo_estimado_h':4.0,
        }
]


def evaluar_prioridad(severidad, tiempo_estimado_h):
    severidad_num = {'alta':3, 'media':2, 'baja':1}
    factor_impacto = severidad_num[severidad] * tiempo_estimado_h
    
    if factor_impacto >= 4.99 and factor_impacto <= 9.99:
        return (factor_impacto, 'IMPORTANTE')
    elif factor_impacto > 10:
        return (factor_impacto, 'CRÍTICO')
    else:
        return (factor_impacto, 'NORMAL')

print('GESTION DE BUGS REPORTADOS')
print('============================')
for bug in bugs:
    prioridad = evaluar_prioridad(bug['severidad'], bug['tiempo_estimado_h'])
    print(f"Ticket ID: {bug['ticket_id']} - {bug['descripcion']} - Severidad: {bug['severidad']} \nTiempo estimado: {bug['tiempo_estimado_h']}horas\nFactor impacto: {prioridad[0]:.2f} - Prioridad: {prioridad}\n ---------------------------------------------------------------------")

