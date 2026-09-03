<?php
// ejemplo_errores.php: ejemplo didáctico de manejo de errores.
// Simula que un usuario busca datos dentro de un inventario de
// invierno, pero la base de datos de ese inventario no existe.
// El objetivo es demostrar cómo capturar el error, registrarlo
// en la bitácora y mostrar al usuario un mensaje amigable, sin
// revelar los detalles técnicos internos.

// Habilita el reporte de errores de MySQLi como excepciones. Esto
// hace que cualquier fallo de conexión o consulta se lance como una
// excepción de tipo mysqli_sql_exception, que podemos capturar con
// try-catch en lugar de revisar error por error.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Arreglo que contendrá el listado del inventario si todo va bien
$inventario = array();

// El bloque try intenta ejecutar las operaciones que podrían fallar.
// Si en cualquier punto se lanza una excepción, el código restante
// del try se salta y pasa directamente al bloque catch.
try {

    // Se intenta conectar a la base de datos "inventario_invierno".
    // Como esa base de datos no existe en el servidor, new mysqli()
    // lanzará una excepción de tipo mysqli_sql_exception.
    $conexion = new mysqli("localhost", "root", "", "inventario_invierno");

    // Si se llegara a conectar (no es el caso), se cargaría el
    // inventario desde una tabla llamada Articulos.
    $consulta = $conexion->prepare("SELECT * FROM Articulos");
    $consulta->execute();
    $resultado = $consulta->get_result();

    // Se leen todas las filas y se guardan en el arreglo
    while ($fila = $resultado->fetch_assoc()) {
        $inventario[] = $fila;
    }

    // Se libera la consulta preparada
    $consulta->close();

    // Se cierra la conexión a la base de datos
    $conexion->close();

// catch captura la excepción lanzada. El tipo mysqli_sql_exception
// solo captura los errores de MySQL; cualquier otra excepción pasaría
// de largo, por eso va primero.
} catch (mysqli_sql_exception $error) {

    // Se registra el detalle del error en la bitácora local (log de
    // Apache). Aquí SÍ se guarda el motivo técnico completo, porque
    // esta información la revisa el personal de soporte, no el usuario.
    error_log("Error al consultar el inventario de invierno: " . $error->getMessage());

    // Mensaje amigable para el usuario, sin mostrar el detalle técnico
    $mensaje = "Ocurrió un problema al consultar el inventario de invierno.
                Intente nuevamente más tarde o contacte al administrador.";

    // echo: se muestra el mensaje amigable en la página
    echo "<div style='font-family: sans-serif; color: #842029; background: #f8d7da;
          border: 1px solid #f5c2c7; padding: 1rem; border-radius: .5rem; max-width: 480px;
          margin: 2rem auto; text-align: center;'>
          <h2>Error en el sistema</h2>
          <p>{$mensaje}</p>
          <p style='font-size: .8rem; color: #6c757d;'>Se ha registrado el detalle en la bitácora.</p>
          </div>";

// finally se ejecuta SIEMPRE, haya o no error. Sirve para tareas de
// limpieza, como cerrar conexiones que pudieron quedar abiertas.
} finally {

    // Muestra un mensaje indicando que el procesamiento terminó
    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 1rem;
          color: #6c757d;'>Finalizado</div>";
}
?>