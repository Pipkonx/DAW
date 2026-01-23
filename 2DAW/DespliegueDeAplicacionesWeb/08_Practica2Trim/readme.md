# Práctica 2º trimestre

## Servidor alojamiento web
Se pide las instalación, configuración y puesta en marcha de un servidor que ofrezca servicio de alojamiento web configurable:

Se dará alojamiento a páginas web tanto estáticas como dinámicas con “php”
Los clientes dispondrán de un directorio de usuario con una página web por defecto. 
Además contarán con una base de datos sql que podrán administrar con phpmyadmin
Los clientes podrán acceder mediante ftp para la administración de archivos configurando adecuadamente TLS
Se habilitará el acceso mediante ssh y sftp. 
Se automatizará mediante el uso de scripts: 
La creación de usuarios y del directorio correspondiente para el alojamiento web
Host virtual en apache
Creación de usuario del sistema para acceso a ftp, ssh, smtp, …
Se creará un subdominio en el servidor DNS con las resolución directa e inversa
Se creará una base de datos además de un usuario con todos los permisos sobre dicha base de datos (ALL PRIVILEGES)
Se habilitará la ejecución de aplicaciones Python con el servidor web 

Adicionalmente se podrá incluir:
Creación mediante mediante Docker de un contenedor DNS y al menos un contenedor que actuará como servidor (web, mysql, ssh,...) Se configurará la red, volúmenes y scripts necesarios para ponerlos en marcha. Este apartado se valorará con hasta el 10% de la nota de la práctica.

El trabajo incluirá una presentación además de los scripts necesarios. A la finalización del trabajo se procederá a una exposición en la que se detallarán los pasos llevados a cabo para la puesta en marcha de la práctica. Se explicarán de forma clara las modificaciones de los ficheros de configuración y los script utilizados.

## Instrucciones de entrega práctica
Añadir cómo colaborador del repositorio github a https://github.com/jpritin
Fecha de entrega práctica 1: Se entregará el 30 de abril.

## Enlaces: Creación mediante script de subdominios
http://bash.cyberciti.biz/domain/create-bind9-domain-zone-configuration-file/
http://www.freeos.com/guides/lsst/scripts/AddDomain
https://python-for-system-administrators.readthedocs.io/en/latest/
https://www.shellscript.sh/index.html //Tutorial de Scripting

# Ejemplos de scripts

Los ejemplos mostrados a continuación son meramente ilustrativos, quizás sean necesarios algunos “ajustes”.

## Creación de usuarios mediante Scripts:
https://www.shellscript.sh/functions.html

Creación de subdominio
```bash
#!/bin/bash
#crear_subdominio.sh nombre_subdominio ip
if [ $# -le 1 ];then
   echo Error!. Introduce subdominio e IP!
   exit 1;
fi


# Variables
USER=$1
IP=$2
SUB_DOMAIN="${USER}.marisma.local"
DOCUMENT="/var/www/html/${USER}"
ZONE_FILE="/etc/bind/db.marisma.local"


echo "Creando carpeta de usuario"
mkdir /var/www/html/$USER -m 644


echo "Actualizando fichero de zona"
echo "\$ORIGIN ${SUB_DOMAIN}."  >>$ZONE_FILE
echo "@ IN  A   ${IP}"  >>$ZONE_FILE
echo "www   IN  A   ${IP}"  >>$ZONE_FILE


echo "Reiniciar servicios"
service apache2 reload > /dev/null
service bind9 reload > /dev/null
service proftpd reload > /dev/null



Creación de vhost
#!/bin/bash
#crear_vhost.sh usuario


if [ $# -eq 0 ];then
   echo Error!. Introduce usuario !
   exit 1;
fi


USER=$1
CONF="${USER}.marisma.conf"
PATH_AVAILABLE="/etc/apache2/sites-available/${CONF}"
PATH_ENABLED="/etc/apache2/sites-enabled/${CONF}"
SUB_DOMAIN="${USER}.marisma.local"
DOCUMENT_ROOT="/var/www/html/$1"
INDEX="${DOCUMENT_ROOT}/index.html"


if ! [ -d $DOCUMENT_ROOT ] ; then
   echo "Creando documento root"
   mkdir -p "$DOCUMENT_ROOT"
fi


touch $PATH_AVAILABLE
if [ -f $PATH_AVAILABLE ] ; then
   echo "creando fichero de config"
   echo "<VirtualHost *:80>
           ServerAdmin admin@$SUB_DOMAIN
           ServerName www.$SUB_DOMAIN
           DocumentRoot $DOCUMENT_ROOT
           <Directory $DOCUMENT_ROOT>
             DirectoryIndex index.html
             Options Indexes FollowSymLinks MultiViews
             AllowOverride all
             Require all granted
           </Directory>
           ErrorLog /var/log/apache2/$SUB_DOMAIN.errorLog.log
           LogLevel error
           CustomLog /var/log/apache2/$SUB_DOMAIN.customLog.log combined
       </VirtualHost>" >>$PATH_AVAILABLE


   #index.html
   echo "Creando index.html"
   echo "<p>Subdominio: $SUB_DOMAIN</p>" >>$INDEX
   echo "<p>usuario: $USER</p>" >>$INDEX


   a2ensite $CONF
fi
```



