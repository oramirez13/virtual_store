<?php
// cerrar.php: destruye la sesion completa del usuario,
// siguiendo el patrón estándar de cierre de sesión.

// Carga la sesion antes de cualquier salida (obligatorio)
session_start();

// session_name(): devuelve el nombre de la cookie de sesion
// (por defecto se llama PHPSESSID)
$nombre = session_name();

// session_get_cookie_params(): arreglo con los atributos reales
// de la cookie de sesion (path, domain, secure, etc.)
$parametros = session_get_cookie_params();

// Borra la cookie del navegador: valor vacio, fecha 1 (1970, ya paso)
// y el MISMO path leido de los parametros; si no coincide el path,
// el navegador no eliminaria la original
setcookie($nombre, '', 1, $parametros["path"]);

// Destruye todos los datos de la sesion EN EL SERVIDOR
// (aqui si se usa destroy porque se cierra TODO, no solo el carrito;
// comparar con vaciar.php, que solo borra la llave del carrito)
session_destroy();
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sesión destruida - Tienda Virtual</title>

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
      <!-- Confirmacion de cierre, mismo estilo que las demas paginas -->
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <!-- Confirmación del cierre de sesión -->
          <h1 class="h4 mb-3">Sesión destruida</h1>
          <p class="mb-3">Se cerró la sesión y se vació el carrito.</p>

          <!-- Unica navegacion posible: volver a empezar -->
          <a href="index.php" class="btn btn-primary btn-sm">Ir a la galería</a>
        </div>
      </div>
    </div>
  </body>
</html>
