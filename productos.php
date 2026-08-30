<?php
// productos.php: consulta la tabla Productos y guarda todas las
// filas en el arreglo $productos para que la galería las use.

// include 'conexion.php': incorpora la conexión ya abierta
include 'conexion.php';

// query(): envía el SELECT al gestor de base de datos; en una
// consulta SELECT retorna un objeto mysqli_result con los datos
$resultado = $conexion->query("SELECT * FROM Productos");

// error: texto del último error del gestor (vacío si no hubo)
if ($conexion->error != '') {
    die("Ocurrió un error al ejecutar la consulta: {$conexion->error}");
}

// Arreglo que se llenará con una fila por cada producto
$productos = array();

// fetch_assoc(): lee la fila actual como arreglo asociativo;
// retorna null cuando ya no quedan filas por leer
$datos = $resultado->fetch_assoc();

// El bucle se repite mientras existan filas en el resultado
while ($datos != null) {
    // Agrega la fila al final del arreglo de productos
    $productos[] = $datos;
    // Lee la siguiente fila del resultado
    $datos = $resultado->fetch_assoc();
}

// close(): cierra la conexión y libera los recursos del servidor
$conexion->close();
?>