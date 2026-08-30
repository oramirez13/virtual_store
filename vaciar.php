<?php
// vaciar.php: elimina todos los productos del carrito de la sesión.
// Es el destino del botón "Vaciar carrito" de la página del carrito.

// session_start(): abre la sesión antes de cualquier salida
session_start();

// unset(): elimina una llave específica del arreglo $_SESSION.
// Aquí borra solo el carrito; el resto de la sesión sigue activa.
// No se usa session_destroy() porque cerraría la sesión completa.
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
      <!-- Confirmación de que el carrito quedó vacío -->
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h1 class="h4 mb-3">Carrito de compras</h1>
          <p class="text-danger fw-bold mb-3">El carrito se ha vaciado.</p>

          <!-- Opciones de navegación para continuar -->
          <a href="index.php" class="btn btn-outline-secondary btn-sm">&lt;= Regresar a la galería</a>
          <a href="carrito.php" class="btn btn-primary btn-sm">Ver el carrito</a>
        </div>
      </div>
    </div>
  </body>
</html>