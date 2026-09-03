<?php
// index.php: muestra la galería de productos y permite agregar
// cada uno al carrito de compras. El carrito se guarda en la
// sesión del usuario.

// session_start(): abre la sesión o retoma la existente. Debe
// ejecutarse antes de enviar cualquier salida HTML, porque envía
// la cabecera HTTP de la cookie de sesión.
session_start();

// include 'productos.php': consulta los productos y deja listo
// el arreglo $productos para recorrerlo en la galería
include 'productos.php';

// Lee el carrito de la sesión; si no existe, se trata como vacío.
// isset() comprueba la existencia de la llave antes de usarla.
$conteo = [];
if(isset($_SESSION['carrito'])){
    // array_count_values(): cuenta cuántas veces aparece cada código;
    // con [4, 7, 4] devuelve [4 => 2, 7 => 1]
    $conteo = array_count_values($_SESSION['carrito']);
}
// array_sum(): suma las cantidades para obtener el total de ítems
$cantidad = array_sum($conteo);
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tienda Virtual - Carrito de Compras</title>

    <!-- Framework CSS Bootstrap -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />

    <!-- Estilos propios del proyecto -->
    <link rel="stylesheet" href="css/style.css" />
  </head>

  <body>
    <!-- Barra superior; "inicio" es el destino del botón volver arriba -->
    <nav id="inicio" class="navbar navbar-dark bg-dark mb-4">
      <!-- Flex de Bootstrap: marca a la izquierda, enlaces a la derecha -->
      <div class="container d-flex justify-content-between align-items-center">
        <span class="navbar-brand mb-0 h1">Tienda Virtual de Camisetas UNIX</span>
        <div>
        <?php if($cantidad > 0){ ?>
          <!-- Enlace al carrito con el contador de ítems -->
          <a href="carrito.php" class="text-white text-decoration-none">
            <img src="img/icons8-shopping-cart-48.png" alt="Carrito" style="width: 22px;" class="me-1">
            Carrito (<?php echo $cantidad; ?>)
          </a>
        <?php } else { ?>
          <!-- Enlace al carrito sin contador cuando no hay ítems -->
          <a href="carrito.php" class="text-white text-decoration-none">
            <img src="img/icons8-shopping-cart-48.png" alt="Carrito" style="width: 22px;" class="me-1">
            Carrito
          </a>
        <?php } ?>
          <!-- Cierra la sesión completa: borra la cookie y los datos
               guardados en el servidor -->
          <a href="consulta.php" class="text-white text-decoration-none ms-3">Consultas</a>
          <!-- Ejemplos de manejo de errores por estación: cada botón
               consulta un inventario cuyo acceso falla y se muestra un
               mensaje de error al usuario -->
          <a href="ejemplo_errores.php?estacion=verano" class="text-white text-decoration-none ms-3">Verano</a>
          <a href="ejemplo_errores.php?estacion=invierno" class="text-white text-decoration-none ms-3">Invierno</a>
          <a href="cerrar.php" class="text-white text-decoration-none ms-3">Cerrar sesión</a>
        </div>
      </div>
    </nav>

    <div class="container">
      <h1 class="mb-4">Galería de Productos</h1>

      <div class="row">
        <?php
        // foreach(): recorre los productos; en cada vuelta $producto es una fila
        foreach ($productos as $producto) {

            // htmlspecialchars(): escapa los datos antes de imprimirlos,
            // para evitar que contenido de la BD se ejecute como HTML
            $codigo  = htmlspecialchars($producto['codigo']);
            $nombre  = htmlspecialchars($producto['nombre']);
            $detalle = htmlspecialchars($producto['detalle']);
            $imagen  = htmlspecialchars($producto['imagen']);

            // number_format(): da formato al precio con 2 decimales
            $precio = number_format($producto['precio'], 2);
        ?>
          <!-- Tarjeta: ancho completo en móvil y un tercio en computadora -->
          <div class="col-12 col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
              <!-- Foto del producto; al hacer clic, script.js abre el modal -->
              <img src="<?php echo $imagen; ?>" class="card-img-top img-producto" alt="<?php echo $nombre; ?>">

              <div class="card-body">
                <?php if(isset($conteo[$producto['codigo']])){ ?>
                  <!-- Insignia que indica que el producto ya está en el carrito;
                       usa el conteo leído de la sesión -->
                  <span class="badge text-bg-success mb-2">
                    En tu carrito (x<?php echo $conteo[$producto['codigo']]; ?>)
                  </span>
                <?php } ?>
                <h5 class="card-title"><?php echo $nombre; ?></h5>
                <p class="card-text"><?php echo $detalle; ?></p>
              </div>

              <div class="card-footer bg-white">
                <small class="text-muted">Código: <?php echo $codigo; ?></small>
                <!-- Clases de Bootstrap: texto en negrita de color verde -->
                <p class="mb-0 fw-bold text-success">&#8353; <?php echo $precio; ?></p>

                <!-- Formulario que envía el código del producto a agregar.php.
                     El campo oculto viaja por POST sin mostrarse en pantalla -->
                <form method="post" action="agregar.php" class="mt-2">
                  <input type="hidden" name="codigo" value="<?php echo $codigo; ?>">
                  <button type="submit" class="btn btn-primary btn-sm w-100">
                    Agregar al carrito
                  </button>
                </form>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>

    <!-- Modal oculto que muestra la imagen ampliada al hacer clic en una foto -->
    <div class="modal fade" id="modalImagen" tabindex="-1" aria-labelledby="modalImagenTitulo" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <!-- Título del modal con el nombre del producto -->
            <h5 class="modal-title" id="modalImagenTitulo">Producto</h5>
            <!-- Botón X de cierre nativo de Bootstrap -->
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body p-0">
            <!-- Imagen ampliada; su origen lo asigna script.js -->
            <img id="imagenAmpliada" src="" alt="" class="img-fluid w-100">
          </div>
        </div>
      </div>
    </div>

    <!-- Botón flotante volver arriba (ancla a #inicio) -->
    <a href="#inicio" class="btn btn-dark btn-volver-arriba position-fixed bottom-0 end-0 m-4 shadow-sm"
       aria-label="Volver arriba">&#8593;</a>

    <!-- JavaScript de Bootstrap (modal) -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
      crossorigin="anonymous"
    ></script>

    <!-- Lógica propia del modal -->
    <script src="js/script.js" type="text/javascript"></script>
  </body>
</html>