<?php
// carrito.php: página donde se visualizan todos los productos
// almacenados en el carrito de la sesión.

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
// es indispensable para que el carrito funcione.
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

// Mensaje flash: si existe en la sesión, se copia a una variable local
// y se borra de la sesión para que solo se muestre una vez.
$flash = "";
$flashTipo = "success";
if(isset($_SESSION['flash'])){
    $flash = $_SESSION['flash']['texto'];
    $flashTipo = $_SESSION['flash']['tipo'];
    // unset(): elimina el mensaje flash ya consumido de la sesión
    unset($_SESSION['flash']);
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Carrito de compras - Tienda Virtual</title>

    <!-- Framework CSS Bootstrap -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />
  </head>

  <body>
    <div class="container mt-4">
      <h1 class="mb-4">
        <!-- Icono del carrito junto al título, mismo estilo que la barra -->
        <img src="img/icons8-shopping-cart-48.png" alt="Carrito" style="width: 28px;" class="me-2">
        Carrito de Compras
      </h1>

      <?php if($flash != ""){ ?>
        <!-- Alerta de Bootstrap con el mensaje flash (ej. "carrito vaciado").
             El color depende de $flashTipo (success/warning/danger) -->
        <div class="alert alert-<?php echo $flashTipo; ?> alert-dismissible fade show" role="alert">
          <?php echo $flash; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
      <?php } ?>

      <?php if($error != ""){ ?>
        <!-- Mensaje de error amigable cuando falla la consulta -->
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php } ?>

      <?php if(count($items) == 0){ ?>
        <!-- Aviso cuando no hay productos en el carrito -->
        <div class="alert alert-info">
          Tu carrito está vacío. <a href="index.php">Ir a la galería</a>
        </div>
      <?php } else { ?>
        <!-- Tabla de productos: una fila por producto, agrupado por
             cantidad cuando se repite varias veces en el carrito -->
        <table class="table table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th></th>
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
                <td style="width: 80px;">
                  <!-- Miniatura de la imagen del producto -->
                  <img src="<?php echo htmlspecialchars($item['imagen']); ?>"
                       alt="<?php echo htmlspecialchars($item['nombre']); ?>"
                       class="img-thumbnail">
                </td>
                <td><?php echo htmlspecialchars($item['codigo']); ?></td>
                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                <td>&#8353; <?php echo number_format($item['precio'], 2); ?></td>
                <td><?php echo $item['cantidad']; ?></td>
                <td>&#8353; <?php echo number_format($item['subtotal'], 2); ?></td>
              </tr>
            <?php } ?>
          </tbody>
          <!-- Fila final con el total de todos los subtotales -->
          <tfoot>
            <tr>
              <td colspan="4" class="text-end fw-bold">Total</td>
              <td></td>
              <td class="fw-bold text-success">&#8353; <?php echo number_format($total, 2); ?></td>
            </tr>
          </tfoot>
        </table>

        <!-- Botones de acción del carrito.
             "Finalizar compra" envía POST a finalizar_compra.php para ver
             el resumen; "Vaciar carrito" envía POST a vaciar.php. Ambos
             se envían por POST porque modifican el estado del carrito -->
        <form method="post" action="finalizar_compra.php" class="d-inline">
          <button type="submit" class="btn btn-success">Finalizar compra</button>
        </form>
        <form method="post" action="vaciar.php" class="d-inline">
          <button type="submit" class="btn btn-danger">Vaciar carrito</button>
        </form>
        <a href="index.php" class="btn btn-outline-secondary">&lt;= Seguir comprando</a>
      <?php } ?>
    </div>
  </body>
</html>