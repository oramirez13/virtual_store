<?php
// productos.php: consulta la tabla Productos y guarda todas las
// filas en el arreglo $productos para que la galería las use.

// include 'conexion.php': incorpora la conexión abierta. Si la
// conexión fallara, ese archivo ya muestra el mensaje de error.
include 'conexion.php';

// El bloque try intenta ejecutar la consulta y leer los resultados.
try {

    // query(): envía el SELECT al gestor de base de datos; en una
    // consulta SELECT retorna un objeto mysqli_result con los datos.
    // Si la consulta falla, se lanza una mysqli_sql_exception.
    $resultado = $conexion->query("SELECT * FROM Productos");

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

// catch captura únicamente los errores de MySQL que se hayan lanzado.
} catch (mysqli_sql_exception $error) {

    // error_log(): guarda el detalle técnico del error en la bitácora
    // local (log de Apache) para su revisión por el personal de soporte.
    error_log("Error al consultar los productos: " . $error->getMessage());

    // Mensaje amigable para el usuario, sin detalle técnico interno.
    die("Ocurrió un error al cargar los productos. Por favor, intente
        más tarde o contacte al administrador.");
}
?>