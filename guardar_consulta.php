<?php
// guardar_consulta.php: recibe los datos del formulario de consultas
// por POST, los valida y los almacena en la tabla Consultas de la
// base de datos. Sigue el patrón de "página receptora".

// include 'conexion.php': incorpora la conexión abierta. Si la
// conexión fallara, ese archivo ya muestra el mensaje de error.
include 'conexion.php';

// Variables para el mensaje de resultado
$mensaje = "";
$tipoMensaje = "success";  // "success" (verde) o "danger" (rojo)

// Verifica que llegaron los datos del formulario por POST.
// Lectura defensiva: isset() comprueba antes de usar la variable.
if(isset($_POST['nombre'], $_POST['email'], $_POST['detalle'])){

    // Recupera y recorta los espacios al inicio y final de cada campo.
    // trim() evita guardar valores con espacios en blanco innecesarios.
    $nombre  = trim($_POST['nombre']);
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : "";
    $email   = trim($_POST['email']);
    $detalle = trim($_POST['detalle']);

    // Validación: el nombre, el correo y el detalle son obligatorios.
    if($nombre != "" && $email != "" && $detalle != ""){

        // filter_var() con FILTER_VALIDATE_EMAIL comprueba si el correo
        // tiene un formato válido (usuario@dominio). Retorna true o false.
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){

            // El bloque try envuelve la inserción en la base de datos.
            try {

                // Prepared statement: la consulta se prepara con marcadores (?)
                // y los valores se envían por separado al momento de ejecutarla.
                // Así los datos nunca se interpretan como parte del SQL.
                $consulta = $conexion->prepare(
                    "INSERT INTO Consultas (nombre, telefono, email, detalle) VALUES (?, ?, ?, ?)"
                );

                // bind_param("ssss", ...): sustituye los (?) por los valores.
                // Las cuatro "s" declaran que los datos son cadenas (string).
                $consulta->bind_param("ssss", $nombre, $telefono, $email, $detalle);

                // execute(): ejecuta la consulta ya preparada. Si falla,
                // aquí se lanza un mysqli_sql_exception.
                $consulta->execute();

                // Libera la consulta preparada
                $consulta->close();

                // Cierra la conexión cuando ya no se necesita
                $conexion->close();

                // Mensaje de éxito para el usuario
                $mensaje = "Su consulta fue enviada correctamente. Le responderemos pronto.";
                $tipoMensaje = "success";

            // catch captura únicamente los errores de MySQL.
            } catch (mysqli_sql_exception $errorDetalle) {

                // error_log(): guarda el detalle técnico del error en la
                // bitácora local (log de Apache).
                error_log("Error al guardar la consulta: " . $errorDetalle->getMessage());

                // Mensaje amigable para el usuario, sin detalle técnico interno.
                $mensaje = "Ocurrió un error al enviar su consulta. Intente más tarde.";
                $tipoMensaje = "danger";
            }
        }else{
            // El formato del correo no es válido
            $mensaje = "El correo electrónico ingresado no es válido.";
            $tipoMensaje = "danger";
        }
    }else{
        // Faltó alguno de los campos obligatorios
        $mensaje = "Debe completar el nombre, el correo y el detalle de la consulta.";
        $tipoMensaje = "danger";
    }
}else{
    // No se recibieron todos los datos esperados
    $mensaje = "No se recibieron los datos de la consulta.";
    $tipoMensaje = "danger";
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Consulta enviada - Tienda Virtual</title>

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
      <!-- Tarjeta de confirmación centrada -->
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h1 class="h4 mb-3">Formulario de consultas</h1>

          <!-- Alert que muestra el mensaje; el color depende de $tipoMensaje.
               alert-success (verde) para éxito, alert-danger (rojo) para error -->
          <div class="alert alert-<?php echo $tipoMensaje; ?> mb-3">
            <?php echo $mensaje; ?>
          </div>

          <!-- Enlace para regresar a la galería -->
          <a href="index.php" class="btn btn-primary btn-sm">Volver a la galería</a>
        </div>
      </div>
    </div>
  </body>
</html>