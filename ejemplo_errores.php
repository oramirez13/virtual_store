<?php
// ejemplo_errores.php: página didáctica de manejo de errores.
// Según el parámetro "estacion" que llega por GET, intenta consultar
// el inventario de una temporada: "verano" o "invierno". Ninguna de
// esas bases de datos existe, por lo que la conexión falla y se
// demuestra el manejo de errores con try-catch, error_log() y finally.

// Habilita el reporte de errores de MySQLi como excepciones. Así
// cualquier fallo de conexión o consulta se lanza como excepción de
// tipo mysqli_sql_exception, capturable con try-catch.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Lee la estación recibida por GET. Si no llega o es desconocida,
// se usa "invierno" como valor por defecto.
// isset() comprueba de forma defensiva antes de usar la variable.
$estacion = "invierno";
if(isset($_GET['estacion']) && ($_GET['estacion'] == "verano" || $_GET['estacion'] == "invierno")){
    $estacion = $_GET['estacion'];
}

// Nombre de la base de datos según la estación. Las bases no existen
// en el servidor, por lo que la conexión provocará una excepción.
if($estacion == "verano"){
    $baseDatos = "inventario_verano";     // Base de datos del inventario de verano (inexistente)
}else{
    $baseDatos = "inventario_invierno";   // Base de datos del inventario de invierno (inexistente)
}

// Arreglo que contendría el listado del inventario si todo saliera bien
$inventario = array();

// El bloque try intenta las operaciones que podrían fallar.
try {

    // Intenta conectar a la base de datos de la estación elegida.
    // Como no existe, new mysqli() lanza un mysqli_sql_exception.
    $conexion = new mysqli("localhost", "root", "", $baseDatos);

    // Si se llegara a conectar (no es el caso), cargaría una tabla
    // llamada Articulos con los productos de la temporada.
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

// catch captura la excepción lanzada por MySQL.
} catch (mysqli_sql_exception $error) {

    // Se registra el detalle técnico del error en la bitácora local
    // (log de Apache). Esta información la revisa el personal de
    // soporte, no el usuario final.
    error_log("Error al consultar el inventario de {$estacion}: " . $error->getMessage());

    // Mensaje amigable para el usuario, sin mostrar el detalle técnico.
    // Se personaliza con el nombre de la estación consultada.
    $mensaje = "Ocurrió un problema al consultar el inventario de {$estacion}.
                Intente nuevamente más tarde o contacte al administrador.";

    // echo: se muestra el mensaje amigable en la página mediantestilos
    // en línea (sin Bootstrap, para que el ejemplo sea autocontenido).
    echo "<div style='font-family: sans-serif; color: #842029; background: #f8d7da;
          border: 1px solid #f5c2c7; padding: 1rem; border-radius: .5rem; max-width: 480px;
          margin: 2rem auto; text-align: center;'>
          <h2>Error en el sistema</h2>
          <p>{$mensaje}</p>
          </div>";

// finally se ejecuta SIEMPRE, haya o no error.
} finally {

    // Muestra un mensaje indicando que el procesamiento terminó, junto a
    // un enlace para regresar a la página principal de la tienda.
    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 1rem;
          color: #6c757d;'>Finalizado
          <br>
          <a href='index.php' class='btn btn-outline-secondary btn-sm'
             style='margin-top: .75rem;'>&lt;= Regresar a la página principal</a>
          </div>";
}
?>