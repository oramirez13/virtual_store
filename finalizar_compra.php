<?php
// finalizar_compra.php: muestra el detalle de los artículos que el
// cliente compró y el monto total. Al finalizar, el carrito de la
// sesión se vacía, como si la venta ya se hubiera completado.

// Carga la sesión antes de cualquier salida
session_start();

// Lee el carrito; si no existe, inicia como arreglo vacío.
// Lectura defensiva: isset() comprueba antes de usar la variable.
if(isset($_SESSION['carrito'])){
    $carrito = $_SESSION['carrito'];
}else{
    $carrito = [];
}

// Arreglo donde se guardarán los datos completos de cada producto
$items = [];
// Acumulador del total a pagar
$total = 0;
// Mensaje que se mostrará si ocurre un error
$error = "";

// Si hay productos en el carrito, consulta cada uno en la base de datos
if(count($carrito) > 0){
    include 'conexion.php';

    // El bloque try envuelve las consultas con la base de datos.
    try {

        // Recorre los códigos guardados en la sesión, uno por uno
        foreach($carrito as $codigo){

            // (int): refuerza que el código sea un entero antes de la consulta
            $codigo = (int)$codigo;

            // Prepared statement: la consulta lleva un marcador (?) y el
            // valor se envía por separado, de modo que la base nunca lo
            // interpreta como parte del SQL.
            $consulta = $conexion->prepare("SELECT * FROM Productos WHERE codigo = ?");

            // bind_param("i", $codigo): la "i" declara un dato entero
            $consulta->bind_param("i", $codigo);

            // execute(): ejecuta la consulta ya preparada. Si falla, aquí
            // se lanza un mysqli_sql_exception.
            $consulta->execute();

            // get_result(): obtiene el resultado como objeto mysqli_result
            $resultado = $consulta->get_result();

            // fetch_assoc(): lee la primera fila (o null si no existe)
            $fila = $resultado->fetch_assoc();

            // Libera la consulta preparada
            $consulta->close();

            // Si el producto existe, se acumula en la lista y en el total
            if($fila != null){
                $items[] = $fila;
                $total += $fila['precio'];
            }
        }

        // Cierra la conexión cuando ya no se necesita
        $conexion->close();

    // catch captura únicamente los errores de MySQL.
    } catch (mysqli_sql_exception $errorDetalle) {

        // error_log(): guarda el detalle técnico del error en la bitácora
        // local (log de Apache).
        error_log("Error al finalizar la compra: " . $errorDetalle->getMessage());

        // Mensaje amigable para el usuario, sin detalle técnico interno.
        $error = "Ocurrió un error al finalizar la compra. Intente más tarde.";
    }
}

// Si hay productos y no hubo error, se considera la compra completada
// y se vacía el carrito de la sesión.
if(count($items) > 0 && $error == ""){
    // unset(): elimina la llave 'carrito' de la sesión. No se usa
    // session_destroy() porque eso terminaría la sesión completa; aquí
    // solo se limpia el carrito tras confirmar la compra.
    unset($_SESSION['carrito']);
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Compra finalizada - Tienda Virtual</title>

    <!-- Framework CSS Bootstrap -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />
  </head>

  <body>
    <div class="container mt-4" style="max-width: 640px;">
      <h1 class="mb-4">Resumen de tu compra</h1>

      <?php if($error != ""){ ?>
        <!-- Mensaje de error amigable cuando falla la consulta -->
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <a href="index.php" class="btn btn-primary">Volver a la galería</a>
      <?php } elseif(count($items) == 0){ ?>
        <!-- Aviso cuando no hay productos en el carrito -->
        <div class="alert alert-info">
          Tu carrito estaba vacío, no hay compra que finalizar.
          <a href="index.php">Ir a la galería</a>
        </div>
      <?php } else { ?>
        <!-- Confirmación de que la compra fue registrada -->
        <div class="alert alert-success">
          ¡Gracias por tu compra! A continuación se detallan los artículos.
        </div>

        <!-- Tabla con los artículos comprados -->
        <table class="table table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th>Código</th>
              <th>Producto</th>
              <th class="text-end">Precio</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($items as $item){ ?>
              <tr>
                <td><?php echo htmlspecialchars($item['codigo']); ?></td>
                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                <td class="text-end">&#8353; <?php echo number_format($item['precio'], 2); ?></td>
              </tr>
            <?php } ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="2" class="text-end fw-bold">Total</td>
              <td class="text-end fw-bold text-success">&#8353; <?php echo number_format($total, 2); ?></td>
            </tr>
          </tfoot>
        </table>

        <!-- Enlace para volver a la galería -->
        <a href="index.php" class="btn btn-primary">Volver a la galería</a>
      <?php } ?>
    </div>
  </body>
</html>