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




# Trabajando con scripts  (Debes publicarlos en Github)

### Crea un script que añada un nombre de dominio y una ip al fichero hosts. Debemos comprobar que no existe dicho dominio en el fichero hosts

### Crea un script que nos permita crear una página web con un título, una cabecera y un mensaje


# 🧠 Guía paso a paso — Creación y prueba de scripts en Linux

Este documento describe de forma visual el proceso para crear y probar tres scripts básicos en Linux:

1. **agregar_puerto_apache.sh** — Añade un puerto al fichero de configuración de Apache.  
2. **añadir_hosts.sh** — Añade una IP y un dominio al archivo `/etc/hosts`.  
3. **crear_web.sh** — Crea una página web simple con un título, cabecera y mensaje.  

Cada paso incluye un espacio donde debes colocar una **captura de pantalla**.

---

## 🥇 1) Ir a tu home y crear la carpeta del proyecto

Abre una terminal, navega hasta tu carpeta personal y crea un nuevo directorio para los scripts.  
Comprueba que te encuentras dentro del directorio recién creado.

📸 **Captura de pantalla:**  
> Muestra la terminal en el directorio `/home/tuusuario/mis-scripts-apache`.

![imagen](imagenes/paso1.png)

---

## 🥈 2) Crear el script `puerto_apache.sh`

Crea un nuevo archivo llamado `puerto_apache.sh` dentro de la carpeta del proyecto.  
Este script permitirá agregar un puerto de escucha al fichero de configuración de Apache.

📸 **Captura de pantalla:**  
> Muestra el archivo `puerto_apache.sh` creado y visible en la terminal o editor.

![imagen](imagenes/paso2.png)

---

## 🥉 3) Crear el script `añadir_hosts.sh`

Crea el script `añadir_hosts.sh`, que servirá para agregar un dominio y su IP al archivo `/etc/hosts`.  
Este script comprueba primero si el dominio ya existe antes de añadirlo.

📸 **Captura de pantalla:**  
> Muestra el archivo `añadir_hosts.sh` creado correctamente.

![imagen](imagenes/paso3.png)

---

## 🏅 4) Crear el script `crear_web.sh`

Crea el script `crear_web.sh`, que generará una página web sencilla con un título, una cabecera y un mensaje.  
La página se guardará en una carpeta que se crea automáticamente.

📸 **Captura de pantalla:**  
> Muestra el archivo `crear_web.sh` creado y visible en tu terminal o editor.

![imagen](imagenes/paso4.png)

---

## ⚙️ 5) Dar permisos de ejecución a los scripts

Concede permisos de ejecución a los tres scripts para poder ejecutarlos desde la terminal.  
Verifica que tienen permisos (`x`) activados.

📸 **Captura de pantalla:**  
> Muestra los tres scripts listos para ejecutarse, con permisos de ejecución visibles.

![imagen](imagenes/paso5.png)

---

## 🌐 6) Probar el script `crear_web.sh`

Ejecuta el script para crear una página de prueba.  
Comprueba que se ha creado la carpeta con el archivo `index.html` dentro y que el contenido del HTML se generó correctamente.

📸 **Captura de pantalla:**  
> Muestra el archivo `index.html` generado y el contenido HTML visible.

![imagen](imagenes/paso6.png)

---

## 🧩 7) Probar el script `añadir_hosts.sh`

Ejecuta el script para agregar una nueva línea al archivo `/etc/hosts`.  
Verifica que la IP y el dominio se han añadido correctamente.  
Si el dominio ya existía, el script mostrará un mensaje avisando.

📸 **Captura de pantalla:**  
> Muestra la nueva entrada añadida al archivo `/etc/hosts`.

![imagen](imagenes/paso7.png)

---

## 🔊 8) Probar el script `puerto_apache.sh`

Ejecuta el script para agregar un nuevo puerto de escucha al fichero de configuración de Apache.  
Comprueba que la línea `Listen <PUERTO>` se añadió correctamente.  
Si el puerto ya estaba presente, el script mostrará un aviso.

📸 **Captura de pantalla:**  
> Muestra el contenido del archivo `ports.conf` con el nuevo puerto añadido.

![imagen](imagenes/paso8.png)

---

✅ **Hasta aquí, has completado los pasos para crear y probar tus tres scripts en Linux.**  
Ahora puedes continuar con los pasos para crear tu repositorio y subirlo a GitHub si lo deseas.












Los scripts deben comprobar previamente el número de parámetros y en el caso de no pasar los parámetros necesarios nos mostrará un error indicando la sintaxis correcta.

Se aconseja leer previamente alguno de los siguientes enlaces sobre shell script
https://github.com/denysdovhan/bash-handbook
https://www.shellscript.sh/
http://www.freeos.com/guides/lsst/index.html
Dos comandos que te pueden ser de utilidad son:
grep
https://geekland.eu/uso-del-comando-grep-en-linux-y-unix-con-ejemplos/
sed
https://geekland.eu/uso-del-comando-sed-en-linux-y-unix-con-ejemplos/
 -->


- [Volver al índice principal](../README.md)