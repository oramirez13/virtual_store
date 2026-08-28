<?php
// conexion.php: instancia el objeto mysqli y valida la conexión.

// require 'config.php': carga las credenciales desde el archivo de
// configuración aparte (host, usuario, contraseña y base de datos).
// Así el código de conexión no mezcla datos de configuración con lógica.
require 'config.php';

// new mysqli(): crea la conexión; recibe host, usuario, contraseña y BD
$conexion = new mysqli($host, $usuario, $contrasena, $basedatos);

// connect_error: mensaje de error si la conexión falló (null si fue exitosa)
if ($conexion->connect_error != null) {
    die("Ocurrió un error al establecer la conexión: {$conexion->connect_error}");
}
?>
