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
// Aviso que se muestra si falla la preparación de alguna consulta
$advertencia = "";

// Si hay productos en el carrito, consulta cada uno en la base de datos
if(count($carrito) > 0){
    include 'conexion.php';

    // Recorre los códigos guardados en la sesión, uno por uno
    foreach($carrito as $codigo){

        // (int): refuerza que el código sea un entero antes de la consulta
        $codigo = (int)$codigo;

        // Prepared statement: mismo patrón que agregar.php. La consulta lleva
        // un marcador (?) y el valor se envía por separado, de modo que la
        // base nunca lo interpreta como parte del SQL (evita inyección).
        $consulta = $conexion->prepare("SELECT * FROM Productos WHERE codigo = ?");

        // Si falla la preparación, se sale del bucle con un aviso
        if($consulta == false){
            $advertencia = "Error al consultar un producto del carrito.";
            break;
        }

        // bind_param("i", $codigo): la "i" declara un dato entero
        $consulta->bind_param("i", $codigo);

        // execute(): ejecuta la consulta ya preparada
        $consulta->execute();

        // get_result(): obtiene el resultado como objeto mysqli_result
        $resultado = $consulta->get_result();

        // fetch_assoc(): lee la primera fila (o null si no existe)
        $fila = $resultado->fetch_assoc();

        // Libera la consulta preparada; la conexión se cierra al final
        $consulta->close();

        // Si el producto existe, se acumula en la lista y en el total
        if($fila != null){
            $items[] = $fila;
            $total += $fila['precio'];
        }
    }
    $conexion->close();
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

      <?php if($advertencia != ""){ ?>
        <!-- Aviso de error si falló la preparación de alguna consulta -->
        <div class="alert alert-danger"><?php echo $advertencia; ?></div>
      <?php } ?>

      <?php if(count($items) == 0){ ?>
        <!-- Aviso cuando no hay productos en el carrito -->
        <div class="alert alert-info">
          Tu carrito está vacío. <a href="index.php">Ir a la galería</a>
        </div>
      <?php } else { ?>
        <!-- Tabla de productos: una fila por cada unidad del carrito -->
        <table class="table table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th></th>
              <th>Código</th>
              <th>Producto</th>
              <th>Precio</th>
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
              </tr>
            <?php } ?>
          </tbody>
          <!-- Fila final con el total de todos los precios -->
          <tfoot>
            <tr>
              <td colspan="3" class="text-end fw-bold">Total</td>
              <td class="fw-bold text-success">&#8353; <?php echo number_format($total, 2); ?></td>
            </tr>
          </tfoot>
        </table>

        <!-- Botón para borrar el carrito: envía POST a vaciar.php.
             Se usa formulario y no enlace, porque vaciar modifica datos -->
        <form method="post" action="vaciar.php" class="d-inline">
          <button type="submit" class="btn btn-danger">Vaciar carrito</button>
        </form>
        <a href="index.php" class="btn btn-outline-secondary">&lt;= Seguir comprando</a>
      <?php } ?>
    </div>
  </body>
</html>