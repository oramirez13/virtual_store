<?php
// index.php: presentación de la galería (plantilla skeletor.html + Bootstrap).
// Los datos llegan en $productos gracias a productos.php.

// Ejecuta la consulta y genera el arreglo $productos
include 'productos.php';
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tienda Virtual - Galería de Camisetas UNIX</title>

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
    <!-- Barra superior; "inicio" es destino del botón volver arriba -->
    <nav id="inicio" class="navbar navbar-dark bg-dark mb-4">
      <div class="container">
        <span class="navbar-brand mb-0 h1">Tienda Virtual de Camisetas UNIX</span>
      </div>
    </nav>

    <div class="container">
      <h1 class="mb-4">Galería de Productos</h1>

      <div class="row">
        <?php
        // Recorre un producto por vuelta; $producto es un arreglo asociativo
        foreach ($productos as $producto) {

            // htmlspecialchars(): escapa caracteres especiales HTML (evita XSS)
            $codigo  = htmlspecialchars($producto['codigo']);
            $nombre  = htmlspecialchars($producto['nombre']);
            $detalle = htmlspecialchars($producto['detalle']);
            $imagen  = htmlspecialchars($producto['imagen']);

            // number_format(): precio con 2 decimales y separador de miles
            $precio = number_format($producto['precio'], 2);
        ?>
          <!-- Tarjeta: ancho completo en móvil, tercio de pantalla en PC -->
          <div class="col-12 col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
              <!-- Foto del producto; al hacer clic script.js abre el modal -->
              <img src="<?php echo $imagen; ?>" class="card-img-top img-producto" alt="<?php echo $nombre; ?>">

              <div class="card-body">
                <h5 class="card-title"><?php echo $nombre; ?></h5>
                <p class="card-text"><?php echo $detalle; ?></p>
              </div>

              <div class="card-footer bg-white">
                <small class="text-muted">Código: <?php echo $codigo; ?></small>
                <!-- fw-bold y text-success: clases de Bootstrap -->
                <p class="mb-0 fw-bold text-success">&#8353; <?php echo $precio; ?></p>
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
            <!-- Título con el nombre del producto -->
            <h5 class="modal-title" id="modalImagenTitulo">Producto</h5>
            <!-- btn-close: botón X de cierre nativo de Bootstrap -->
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body p-0">
            <!-- Imagen ampliada; src lo llena script.js -->
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
