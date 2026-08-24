<?php
// productos.php: consulta la tabla Productos y llena el arreglo $productos.

// Incorpora la conexión creada en conexion.php
include 'conexion.php';

// query(): envía SQL al SGBD; en SELECT retorna un objeto mysqli_result
$resultado = $conexion->query("SELECT * FROM Productos");

// error: texto del último error del SGBD ('' si no hubo errores)
if ($conexion->error != '') {
    die("Ocurrió un error al ejecutar la consulta: {$conexion->error}");
}

// Arreglo que almacenará todas las filas del resultado
$productos = array();

// fetch_assoc(): crea un arreglo asociativo con la fila actual
$datos = $resultado->fetch_assoc();

// Se repite mientras existan filas; retorna null al terminar
while ($datos != null) {
    // Agrega la fila al final del arreglo
    $productos[] = $datos;
    // Lee la siguiente fila
    $datos = $resultado->fetch_assoc();
}

// close(): cierra la conexión y libera el recurso
$conexion->close();
?>
