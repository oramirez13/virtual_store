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

// require 'funciones_carrito.php': une el archivo con la función que
// agrupa los productos repetidos. Se usa require porque este archivo
// es indispensable para preparar el resumen de la compra.
require 'funciones_carrito.php';

// Si hay productos en el carrito, los reconstruye agrupados por código
if(count($carrito) > 0){
    // include 'conexion.php': incorpora la conexión abierta. Si la
    // conexión fallara, ese archivo ya muestra el mensaje de error.
    include 'conexion.php';

    // armarItemsAgrupados($conexion, $carrito): llama a la función del
    // archivo funciones_carrito.php. Cuenta cuántas veces aparece cada
    // código, consulta cada producto una sola vez en la base de datos y
    // calcula la cantidad y el subtotal de cada uno.
    // Devuelve un arreglo asociativo con tres llaves: items, total y error.
    $resultadoItems = armarItemsAgrupados($conexion, $carrito);

    // Copia los tres resultados de la función a las variables locales
    $items = $resultadoItems['items'];
    $total = $resultadoItems['total'];
    $error = $resultadoItems['error'];

    // Cierra la conexión cuando ya no se necesita
    $conexion->close();
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

        <!-- Tabla con los artículos comprados (una fila por producto,
             agrupado por cantidad cuando se repite en el carrito) -->
        <table class="table table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th>Código</th>
              <th>Producto</th>
              <th>Precio</th>
              <th>Cantidad</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($items as $item){ ?>
              <tr>
                <td><?php echo htmlspecialchars($item['codigo']); ?></td>
                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                <td>&#8353; <?php echo number_format($item['precio'], 2); ?></td>
                <td><?php echo $item['cantidad']; ?></td>
                <td>&#8353; <?php echo number_format($item['subtotal'], 2); ?></td>
              </tr>
            <?php } ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="text-end fw-bold">Total</td>
              <td></td>
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