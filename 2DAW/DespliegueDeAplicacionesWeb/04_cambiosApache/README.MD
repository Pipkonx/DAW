### 📁 Ejercicio 4: Cambiar configuracion de apache

1.  Apache utilizará el puerto 81 además del 80

    Para esto primero primero entramos al archvio de configuracion de los puertos
    ![paso1](./images/01.png)

    Ahora agregamos para que esecuche por el puerto 81 `control + o` y `control + x` para guardar y salir
    ![paso2](./images/02.png)

    Ahora simplemente reiniciamos el servicio con `sudo systemctl restart apache2` y ya estaría escuchando por ese puerto
    ![paso3](./images/03.png)

2.  Añadir el dominio “marisma.intranet” en el fichero “hosts”

    Ejecuta el comando para editar: Escribe `sudo nano /etc/hosts` y presiona Enter.
    ![paso4](./images/04.png)

    Añade la entrada: En una nueva línea, escribe la dirección IP del servidor, un espacio, y luego el dominio: 192.168.1.100 marisma.intranet
    ![paso5](./images/05.png)

3.  Cambia la directiva “ServerTokens” para mostrar el nombre del producto.
    ![paso6](./images/06.png)
    ![paso7](./images/07.png)

4.  Comprueba si se visualiza el pie de página en las páginas generadas por Apache (por ejemplo, en las páginas de error). Cambia el valor de la directiva “ServerSignature” y comprueba que funciona correctamente.
    ![paso8](./images/08.png)
    ![paso9](./images/09.png)
    ![paso10](./images/10.png)

5.  Crea un directorio “prueba” y otro directorio “prueba2”. Incluye un par de páginas en cada una de ellas.
    ![paso11](./images/11.png)
    ![paso12](./images/12.png)
    ![paso13](./images/13.png)
6.  Redirecciona el contenido de la carpeta “prueba” hacia “prueba2”
    ![paso14](./images/14.png)
    ![paso15](./images/15.png)
    ![paso16](./images/16.png)

7.  Es posible redireccionar tan solo una página en lugar de toda la carpeta. Pruébalo.
    ![paso17](./images/17.png)

8.  Usa la directiva userdir
    ![paso18](./images/18.png)

9.  Usa la directiva alias para redireccionar a una carpeta dentro del directorio de usuario.
    ![paso19](./images/19.png)
    ![paso20](./images/20.png)
    ![paso21](./images/21.png)
    ![paso22](./images/22.png)

10. ¿Para qué sirve la directiva Options y dónde aparece. Comprueba si apache indexa los directorios. Si es así, ¿cómo lo desactivamos?

- `Options` controla características para un directorio (como listar archivos).
- `Indexes` permite mostrar el contenido de un directorio si no hay archivo índice.
- Para desactivar la indexación, usar:

apache
Options -Indexes

Recargamos apache tras los cambios con `sudo systemctl reload apache2`

- [Volver al índice principal](../README.md)
