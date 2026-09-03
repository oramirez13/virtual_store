<?php
// conexion.php: crea una conexión a la base de datos con mysqli y
// la valida. Lo incluyen las páginas que necesitan consultas.
//
// Nota: este archivo SOLO abre la conexión y la deja en la variable
// $conexion. Si alguien incluye este archivo y la conexión falla,
// se captura aquí mismo, se registra en la bitácora y se muestra un
// mensaje amigable al usuario.

// Habilita el reporte de errores de MySQLi como excepciones. Con esto,
// cualquier error de conexión o de consulta se lanza como una excepción
// de tipo mysqli_sql_exception, que se puede capturar con try-catch.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// require 'config.php': trae las credenciales desde el archivo de
// configuración, que las mantiene separadas de la lógica del código.
require 'config.php';

// El bloque try intenta abrir la conexión con los datos del config.
try {

    // new mysqli(): abre la conexión con los datos cargados antes.
    // Si las credenciales o la base de datos son incorrectas, esta
    // llamada lanza un mysqli_sql_exception y se salta todo el try.
    $conexion = new mysqli($host, $usuario, $contrasena, $basedatos);

// catch captura únicamente las excepciones de tipo mysqli_sql_exception.
} catch (mysqli_sql_exception $error) {

    // error_log(): guarda el detalle técnico del error en la bitácora
    // local (el log de Apache). Esta información NO es para el usuario,
    // sino para el personal de soporte que mantiene el sistema.
    error_log("Error al conectar a la base de datos: " . $error->getMessage());

    // Mensaje amigable: se muestra al usuario sin revelar el detalle
    // técnico interno de la falla.
    die("Ocurrió un error al conectar con la base de datos. Por favor,
        intente más tarde o contacte al administrador.");
}
?>