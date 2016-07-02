## SCAD

SCAD (Sistema de Carga y Administración de Dedicaciones) es un sistema que permite que los desarrolladores de una empresa carguen las horas dedicadas a cada proyecto en el que están involucrados.
A su vez, un administrador puede modificar y eliminar las dedicaciones y ver un informe con los costos adjudicados a cada proyecto.

## Tecnologías usadas

El sistema fue desarrollado en PHP, utilizando el framework Agile Toolkit.
El almacenamiento de datos se realiza mediante MySQL.

## Sobre los entregables

Para los propósitos del entorno los entregables del proyecto consisten en 2 archivos.
El primero consiste en el package de código fuente, incluyendo tests. El segundo archivo consiste en un script SQL llamado scad.sql.
El mismo fue diseñado para ser ejecutado una vez y creará todo el esquema de tablas que necesitará la aplicación para ser ejecutada.

## Instalación y ejecución de la base de datos

Se debe crear una base de datos local para almacenar las tablas del sistema (base de producción) llamada "scad".
Se debe crear una base de datos local para almacenar la base que utilizarán las pruebas (base de test) llamada "tests".
El sistema está diseñado para ser ejecutado en una base de datos MySQL.
La instalación y administración de la base de datos queda por fuera del alcance de este documento.
La documentación de MySQL puede encontrarse en: https://www.mysql.com
Las bases de datos debería poblarse con las tablas y datos estáticos ejecutando el script scad.sql.

## Instalación de entorno

Instalar PHP y Apache.
Copiar los archivos del sistema a la carpeta de Apache (por defecto en Linux: var/www/html).
Configurar la conexión con la base de datos de producción en el archivo config-default.php (la línea a modificar es "$config['dsn'] = 'mysql://root@localhost/scad';") dentro de la carpeta raíz.
Configurar la conexion con la base de datos de tests en el archivo tests.php (la línea a modificar es "$app->dbConnect('mysql://root:@localhost/tests');") dentro de la carpeta raíz.

## Ejecución

Ingresar a http://localhost:puerto/ (o la URL correspondiente).
Iniciar sesión o registrarse con un usuario válido.