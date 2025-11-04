
## Lista de tareas pendientes

1. **Rehacer y verificar las tablas de la base de datos**
   - Revisar el esquema actual
   - Comprobar que todo funciona correctamente con el resto del proyecto

2. **Gestión de fotos subidas por operarios**
   - Las imágenes se almacenarán en el servidor local
   - Utilizar el método POST para recibir los archivos
   - Verificar previamente la existencia del fichero con `file_exists`
   - Nombrar el archivo con el ID del usuario o crear una carpeta específica por usuario

3. **Manejo de errores 404**
   - Si falta un parámetro obligatorio en `index.php`, mostrar una página de error 404 personalizada

4. **Control de salida (output buffering)**
   - Implementar `ob_start` para evitar que los `include` envíen contenido antes de enviar las cabeceras HTTP

5. **Sistema de vistas**
   - Emplear `extract` para pasar variables a las plantillas
   - Cargar las vistas de forma progresiva:
     - Primero invocar `MuestraVista(TEMPLATE_PATH.'layout')`
     - Después ir construyendo el HTML parcialmente, por ejemplo:
       ```php
       $menu_html = CargaVista(TEMPLATE_PATH, $vista);
       ```
