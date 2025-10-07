<!-- https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-20-04-es#paso-4-crear-un-host-virtual-para-su-sitio-web -->

### 📁 Ejercicio 3: Instalación de Apache, MySQL y PHP

#### Paso 1: Instalación de Apache

1. Primero actualizamos nuestra máquina con apt update y upgrade
   ![Paso 1](./images/1.png)

2. Ahora instalamos apache2
   ![Paso 2](./images/2.png)

3. sudo ufw app list es para ver las aplicaciones disponibles en ufw
   ![Paso 3](./images/3.png)

4. ahora ponemos sudo ufw allow in apache para permitir el tráfico entrante
   ![Paso 4](./images/4.png)

5. Y ahora vemos el estado, en nuestro caso nos sale desactivado, lo mantenemos
   ![Paso 5](./images/5.png)

6. Ahora abrimos el navegador y vemos el localhost para verificar que se nos instaló bien el apache
   ![Paso 6](./images/6.png)

#### Paso 2: Instalación de MySQL

7. Ahora instalamos mysql-server
   ![Paso 7](./images/7.png)

1. Descargamos mysql secure
   ![Paso 1](./images/8.png)

1. Ejecutamos mysql
   ![Paso 2](./images/9.png)

1. Salimos con el comando exit
   ![Paso 3](./images/10.png)

#### Paso 3: Instalación de MySQL

4. Instalamos PHP
   ![Paso 4](./images/11.png)

5. Verificamos la version de php
   ![Paso 5](./images/12.png)

### Paso 4: Crear un host virtual para su sitio web

6. Creamos el directorio para your_domain de la siguiente manera:
   ![Paso 6](./images/13.png)

7. Ahora le asignamos la propiedad al directorio con la variable de entorno `$USER`, que hará referencia al usuario del sistema actual:
   ![Paso 7](./images/14.png)

8. Ahora abrimos un nuevo archivo de configuracion en el siguiente directorio de apache usando el editor de linea de comandos:
   ![Paso 8](./images/15.png)

9. De esta manera nos creará un nuevo archivo en blanco y deberemos copiar lo siguiente:
   ![Paso 9](./images/16.png)

10. Ahora podemos usar `a2ensite` para habilitar el nuevo host virtual:
    ![Paso 10](./images/17.png)

11. Ahora nos aseguramos de que el archivo de configuración no contenga errores de sintaxis para esto ejecutamos el siguiente comando
    ![Paso 11](./images/18.png)

12. Ahora conviene que deshabilitemos el sitio web predeterminado que viene instalado con apache para esto hacemos el siguiente comando:
    ![Paso 12](./images/19.png)

13. Procedemos a reininiciar apache2:
    ![Paso 13](./images/20.png)

14. procedemos a deshabilitar la web que viene predeterminada:
    ![Paso 14](./images/21.png)

15. reverificamos que el archivo no tenga errores:
    ![Paso 15](./images/22.png)

16. y ahora entramos a nuestro para empezar a escribir nuestra landing page
    ![Paso 16](./images/23.png)

17. Ponemso que funciona
    ![Paso 17](./images/24.png)

18. Abrimos el localhost y comprobamos de que realmente nuestra página cambio y se muestra lo que escribimos anteriormente
    ![Paso 18](./images/25.png)

    ## 🔙 Navegación
- [Volver al índice principal](../README.md)