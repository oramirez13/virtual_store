<?php
// conexion.php: instancia el objeto mysqli y valida la conexión.

// Servidor donde corre MariaDB (misma máquina con LAMPP)
$host = "localhost";
// Usuario de la base de datos
$usuario = "root";
// Contraseña del usuario (vacía por defecto en LAMPP)
$contrasena = "";
// Base de datos a utilizar
$basedatos = "Tienda";

// new mysqli(): crea la conexión; recibe host, usuario, contraseña y BD
$conexion = new mysqli($host, $usuario, $contrasena, $basedatos);

// connect_error: mensaje de error si la conexión falló (null si fue exitosa)
if ($conexion->connect_error != null) {
    die("Ocurrió un error al establecer la conexión: {$conexion->connect_error}");
}
?>
