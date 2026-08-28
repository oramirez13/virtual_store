<?php
// agregar.php: recibe el código del producto por POST y lo guarda
// en el carrito de la sesión. Sigue el patrón de "página receptora":
// recibe, valida, guarda y muestra una confirmación.

// Carga la sesión antes de cualquier salida
session_start();

// Mensajes que se mostrarán en la confirmación
$mensaje = "";
$productoNombre = "";
// Cuántos productos lleva el carrito después de la operación
$cantidadAhora = 0;

// Lee el carrito actual; si no existe todavia, inicia vacío.
// Lectura defensiva: isset() comprueba antes de usar la variable.
if(isset($_SESSION['carrito'])){
    $carrito = $_SESSION['carrito'];
}else{
    $carrito = [];
}

// Verifica que llegó un código desde el formulario de la galería
if(isset($_POST['codigo'])){

    // (int) convierte el valor a numero entero:
    // si alguien envía texto malicioso, queda en 0 y se rechaza
    $codigo = (int)$_POST['codigo'];

    if($codigo > 0){

        // Conecta y busca el producto para validar que exista
        include 'conexion.php';
        $resultado = $conexion->query("SELECT nombre FROM Productos WHERE codigo = $codigo");

        if($resultado != false){
            $fila = $resultado->fetch_assoc();

            if($fila != null){
                // El producto existe: se agrega al arreglo del carrito.
                // $_SESSION acepta arreglos nativos, sin implode()
                $carrito[] = $codigo;

                // Guarda el carrito completo de vuelta en la sesión
                $_SESSION['carrito'] = $carrito;

                $mensaje = "Se agregó al carrito:";
                // htmlspecialchars(): escapa el nombre al mostrarlo (evita XSS)
                $productoNombre = htmlspecialchars($fila['nombre']);
                // count(): cuántos elementos tiene el carrito ahora
                $cantidadAhora = count($carrito);
            }else{
                $mensaje = "El producto solicitado no existe.";
            }
        }else{
            $mensaje = "Error en la consulta: {$conexion->error}";
        }

        // Cierra la conexión cuando ya no se necesita
        $conexion->close();
    }else{
        $mensaje = "El código recibido no es válido.";
    }
}else{
    $mensaje = "No se recibió ningún producto.";
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Producto agregado - Tienda Virtual</title>

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
          <h1 class="h4 mb-3">Carrito de compras</h1>
          <p class="mb-1"><?php echo $mensaje; ?></p>
          <?php if($productoNombre != ""){ ?>
            <!-- Nombre del producto agregado, resaltado -->
            <p class="fw-bold text-success mb-1"><?php echo $productoNombre; ?></p>
            <!-- Total acumulado leído de la misma sesión -->
            <p class="text-muted small mb-3">
              Tu carrito ahora tiene <?php echo $cantidadAhora; ?> producto(s).
            </p>
          <?php } ?>

          <!-- Enlaces para continuar navegando -->
          <a href="index.php" class="btn btn-outline-secondary btn-sm">&lt;= Regresar a la galería</a>
          <a href="carrito.php" class="btn btn-primary btn-sm">Ver el carrito</a>
        </div>
      </div>
    </div>
  </body>
</html>
