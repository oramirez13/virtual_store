<?php
// vaciar.php: borra todos los productos del carrito de la sesión
// (botón "Vaciar carrito" de la página del carrito).

// Carga la sesión antes de cualquier salida
session_start();

// unset() elimina UNA llave del arreglo $_SESSION:
// se borra solo el carrito y el resto de la sesión sigue viva.
//
// Nota didáctica: session_destroy() destruye
// la sesión COMPLETA del usuario; aqui no la usamos porque solo
// queremos descartar el carrito, no cerrar la sesión entera.
unset($_SESSION['carrito']);
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Carrito vaciado - Tienda Virtual</title>

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
      <!-- Confirmacion de que el carrito quedó vacío -->
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h1 class="h4 mb-3">Carrito de compras</h1>
          <p class="text-danger fw-bold mb-3">El carrito se ha vaciado.</p>

          <!-- Enlaces para continuar navegando -->
          <a href="index.php" class="btn btn-outline-secondary btn-sm">&lt;= Regresar a la galería</a>
          <a href="carrito.php" class="btn btn-primary btn-sm">Ver el carrito</a>
        </div>
      </div>
    </div>
  </body>
</html>
