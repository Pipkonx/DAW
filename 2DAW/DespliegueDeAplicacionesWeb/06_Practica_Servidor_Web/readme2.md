# Práctica de Configuración de Servidor Web

Este documento detalla la resolución de varias preguntas relacionadas con la configuración de un servidor web, incluyendo la gestión de directorios, redirecciones, control de acceso, módulos de Apache, Virtual Hosts, Alias y expresiones regulares.

---

## Pregunta 1: Crear directorios "int" y "adm", cambiar propietario y permisos

### Código:

```bash
cd /var/www/html
sudo mkdir int adm
sudo chown www-data:www-data int adm
sudo chmod 764 int adm
```

---

## Pregunta 2: Redireccionar el contenido de "int" hacia "adm"

### Código:

**Opción 1: En .htaccess dentro de /var/www/html/int**

```apache
Redirect / /adm/
```

**Opción 2: En configuración del VirtualHost**

```apache
Redirect "/int" "/adm"


sudo mv /var/www/html/inf/* /var/www/html/adm/
```

---

## Pregunta 3: Permitir acceso solo desde la red 10.9.0.0/16 al directorio "int"

### Código:

```apache
<Directory /var/www/html/int>
    Require ip 10.9.0.0/16
    Require all denied
</Directory>
```

---

## Pregunta 4: Activar módulo "user_dir" y permitir uso de directorios de usuario

### Código:

```bash
sudo a2enmod userdir
sudo systemctl restart apache2
mkdir ~/public_html
chmod 711 ~
chmod 755 ~/public_html
```

---

## Pregunta 5: Crear VirtualHost para "int.prueba.intranet"

```apache
 sudo nano /etc/hosts
```

y copiamos y pegamos` 127.0.0.1   inf.prueba.intranet`

```apache
sudo mkdir -p /var/www/int.prueba.intranet
sudo chown -R  $user:$user /var/www/int.prueba.intranet
```

Crear `/etc/apache2/sites-available/inf.prueba.intranet.conf:`

### Código:

```apache
<VirtualHost *:80>
ServerName int.prueba.intranet
DocumentRoot /var/www/html/int

<Directory /var/www/html/int>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>

</VirtualHost>
```

Por ultimo activar el sitio:

```apache
sudo a2ensite inf.prueba.intranet.conf
sudo systemctl reload apache2
```

---

## Pregunta 6: Crear Alias "/img" hacia "/home/img"

### Código:

```apache
Alias /img /home/img

<Directory /home/img>
Options Indexes FollowSymLinks
AllowOverride None
Require all granted
</Directory>
```

---

## Pregunta 7: Usar FilesMatch para denegar acceso a archivos .txt y .pdf

### Código:

```apache
<FilesMatch "\.(txt|pdf)$">
Require all denied
</FilesMatch>
```

---

## Pregunta 8: Interpretación de RewriteRule

### Directiva:

```apache
RewriteRule ^([^/]+)/(+)$ ver.php?c=$1&num=$2
```

### Petición:

```
http://localhost/noticia/236
```

### Archivo servido por Apache:

```
ver.php?c=noticia&num=236
```

---

## Pregunta 9: Expresiones regulares

### Nombres de ficheros GIF con 4 dígitos:

```regex
^.*[0-9]{4}\.gif$
```

### Números reales positivos/negativos, con o sin decimales:

```regex
^[+-]?[0-9]+(\.[0-9]+)?$
```

### Códigos de color RGB hexadecimales:

```regex
^#[A-Fa-f0-9]{6}$
```

---

## Pregunta 10: Crear directorio "ejemplo" con página index.html

### Código:

```bash
cd /var/www/html
mkdir ejemplo
echo "<!DOCTYPE html><html><body><h1>Ejemplo</h1></body></html>" > ejemplo/index.html
```
