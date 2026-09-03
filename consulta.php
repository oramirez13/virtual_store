<?php
// consulta.php: muestra el formulario para que el cliente envíe
// una consulta. Este archivo solo presenta el formulario; el
// procesamiento lo hace guardar_consulta.php al recibir el POST.
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Formulario de consultas - Tienda Virtual</title>

    <!-- Framework CSS Bootstrap -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />
  </head>

  <body>
    <div class="container mt-5" style="max-width: 560px;">
      <div class="card shadow-sm">
        <div class="card-body">
          <h1 class="h4 mb-4">Formulario de consultas</h1>

          <!-- El formulario envía los datos por POST a guardar_consulta.php.
               Los atributos require/required activan la validación del navegador -->
          <form method="post" action="guardar_consulta.php">

            <!-- Campo de nombre del cliente -->
            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="nombre" name="nombre"
                     required placeholder="Escriba su nombre completo">
            </div>

            <!-- Campo de teléfono (opcional) -->
            <div class="mb-3">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="tel" class="form-control" id="telefono" name="telefono"
                     placeholder="Número de contacto">
            </div>

            <!-- Campo de correo electrónico, validado por el navegador -->
            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="email" name="email"
                     required placeholder="usuario@ejemplo.com">
            </div>

            <!-- Campo de detalle de la consulta -->
            <div class="mb-3">
              <label for="detalle" class="form-label">Detalle de la consulta</label>
              <textarea class="form-control" id="detalle" name="detalle" rows="4"
                        required placeholder="Describa su consulta"></textarea>
            </div>

            <!-- Botón para enviar el formulario -->
            <button type="submit" class="btn btn-primary w-100">Enviar consulta</button>
          </form>

          <!-- Enlace para regresar a la galería -->
          <div class="text-center mt-3">
            <a href="index.php" class="btn btn-outline-secondary btn-sm">&lt;= Regresar a la galería</a>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>