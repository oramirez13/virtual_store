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

    // Mensaje de error amigable para el usuario, sin mostrar el detalle
    // técnico. Se personaliza con el nombre de la estación consultada.
    $mensajeError = "Ocurrió un problema al consultar el inventario de {$estacion}.
                     Intente nuevamente más tarde o contacte al administrador.";

// finally se ejecuta SIEMPRE, haya o no error.
} finally {

    // Si no hubo error, $mensajeError está vacío y no se muestra nada.
    $mensajeError = isset($mensajeError) ? $mensajeError : "";
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manejo de errores - Tienda Virtual</title>

    <!-- Framework CSS Bootstrap -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />
  </head>

  <body>
    <div class="container mt-5" style="max-width: 480px;">

      <?php if($mensajeError != ""){ ?>
        <!-- Tarjeta que muestra el mensaje de error amigable -->
        <div class="card shadow-sm border-danger">
          <div class="card-body text-center">
            <h1 class="h4 mb-3 text-danger">Error en el sistema</h1>
            <p class="mb-4"><?php echo $mensajeError; ?></p>
          </div>
        </div>
      <?php } ?>

      <!-- Botón para volver a la página principal, con el mismo estilo
           de botones secundarios que usa el resto del sitio -->
      <div class="text-center mt-3">
        <a href="index.php" class="btn btn-outline-secondary btn-sm">&lt;= Regresar a la página principal</a>
      </div>

    </div>
  </body>
</html>