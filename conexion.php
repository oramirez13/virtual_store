<?php
// conexion.php: crea una conexión a la base de datos con mysqli
// y la valida. Lo incluyen las páginas que necesitan consultas.

// require 'config.php': trae las credenciales desde el archivo de
// configuración, que las mantiene separadas de la lógica del código.
require 'config.php';

// new mysqli(): abre la conexión con los datos cargados antes
$conexion = new mysqli($host, $usuario, $contrasena, $basedatos);

// connect_error: si la conexión falló contiene el motivo y vale null
// si fue exitosa. En caso de fallo, el programa muestra un mensaje.
if ($conexion->connect_error != null) {
    die("Ocurrió un error al establecer la conexión: {$conexion->connect_error}");
}
?>