<?php
// cerrar.php: cierra la sesión del usuario por completo, tanto la
// cookie del navegador como los datos guardados en el servidor.

// session_start(): abre la sesión antes de modificar su cookie
session_start();

// session_name(): nombre de la cookie de sesión (por defecto PHPSESSID)
$nombre = session_name();

// session_get_cookie_params(): atributos reales de la cookie (path, etc.)
$parametros = session_get_cookie_params();

// setcookie(): envía una cookie con fecha pasada (1) y el mismo path,
// con lo cual el navegador elimina la cookie original de la sesión
setcookie($nombre, '', 1, $parametros["path"]);

// session_destroy(): elimina los datos de la sesión en el servidor.
// A diferencia de vaciar.php, aquí se cierra la sesión completa.
session_destroy();
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iniciar sesión - Tienda Virtual</title>

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
      <!-- Confirmación del cierre de sesión -->
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h1 class="h4 mb-3">Iniciar sesión</h1>
          <p class="mb-3">La sesión se cerró y el carrito se vació.</p>

          <!-- Opción de navegación para iniciar sesión de nuevo -->
          <a href="index.php" class="btn btn-primary btn-sm">Iniciar sesión</a>
        </div>
      </div>
    </div>
  </body>
</html>