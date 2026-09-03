<?php
// agregar.php: recibe el código del producto por POST, lo valida y lo
// guarda en el carrito de la sesión. Después redirige automáticamente a
// la galería (index.php) para simplificar la navegación, sin mostrar una
// página intermedia de confirmación.
//
// Este patrón se llama "Post/Redirect/Get": la acción se procesa aquí y
// el navegador vuelve solo a la página principal, donde se muestra el
// mensaje flash guardado en la sesión.

// Carga la sesión antes de cualquier salida
session_start();

// Lee el carrito actual; si no existe, inicia como arreglo vacío.
// Lectura defensiva: isset() comprueba antes de usar la variable.
if(isset($_SESSION['carrito'])){
    $carrito = $_SESSION['carrito'];
}else{
    $carrito = [];
}

// Fragmento de ancla (#producto-CODIGO) que se agrega a la URL de
// redirección para que la galería se ubique en la tarjeta donde el
// usuario agregó el producto. Por defecto queda vacío (sin ancla).
$ancla = "";

// Verifica que llegó un código desde el formulario de la galería
if(isset($_POST['codigo'])){

    // (int): convierte el valor a número entero. Si alguien envía
    // texto no numérico, queda en 0 y se rechaza con la validación.
    $codigo = (int)$_POST['codigo'];

    if($codigo > 0){

        // Conecta y busca el producto para validar que exista;
        // conexion.php a su vez carga las credenciales desde config.php.
        include 'conexion.php';

        // El bloque try envuelve las operaciones con la base de datos.
        try {

            // Prepared statement: la consulta se prepara con un marcador (?)
            // y el valor se envía por separado al momento de ejecutarla.
            // Así el dato nunca se interpreta como parte del SQL.
            $consulta = $conexion->prepare("SELECT nombre FROM Productos WHERE codigo = ?");

            // bind_param("i", $codigo): sustituye el (?) por el valor.
            // La "i" declara que el dato enviado es un entero (integer).
            $consulta->bind_param("i", $codigo);

            // execute(): ejecuta la consulta ya preparada. Si falla,
            // aquí se lanza un mysqli_sql_exception.
            $consulta->execute();

            // get_result(): obtiene el resultado como objeto mysqli_result
            $resultado = $consulta->get_result();

            // fetch_assoc(): lee la primera fila (o null si no existe)
            $fila = $resultado->fetch_assoc();

            // Libera la consulta preparada; la conexión se cierra al final
            $consulta->close();

            if($fila != null){
                // El producto existe: se guarda su código al final del arreglo.
                // $_SESSION admite arreglos nativos, sin implode()
                $carrito[] = $codigo;

                // Guarda el carrito completo de vuelta en la sesión
                $_SESSION['carrito'] = $carrito;

                // Ancla: la redirección apuntará a la tarjeta de este
                // producto para que la galería no suba al inicio.
                $ancla = "#producto-" . $codigo;

                // Mensaje flash: se mostrará solo una vez en la galería.
                // 'tipo' define el color de la alerta (success/danger).
                $_SESSION['flash'] = [
                    'tipo'    => 'success',
                    'texto'   => 'Se agregó al carrito: ' . htmlspecialchars($fila['nombre'])
                ];
            }else{
                // El código no corresponde a ningún producto
                $_SESSION['flash'] = [
                    'tipo'  => 'danger',
                    'texto' => 'El producto solicitado no existe.'
                ];
            }

            // Cierra la conexión cuando ya no se necesita
            $conexion->close();

        // catch captura únicamente los errores de MySQL.
        } catch (mysqli_sql_exception $error) {

            // error_log(): guarda el detalle técnico del error en la
            // bitácora local (log de Apache).
            error_log("Error al agregar el producto al carrito: " . $error->getMessage());

            // Mensaje flash amigable para el usuario, sin detalle técnico.
            $_SESSION['flash'] = [
                'tipo'  => 'danger',
                'texto' => 'Ocurrió un error al agregar el producto. Intente más tarde.'
            ];
        }
    }else{
        $_SESSION['flash'] = [
            'tipo'  => 'danger',
            'texto' => 'El código recibido no es válido.'
        ];
    }
}else{
    $_SESSION['flash'] = [
        'tipo'  => 'danger',
        'texto' => 'No se recibió ningún producto.'
    ];
}

// Redirección automática: envía una cabecera HTTP que indica al navegador
// que solicite la galería. Si se agregó un producto, la URL incluye la ancla
// (#producto-CODIGO) y el navegador se posiciona en esa tarjeta, de modo que
// la página no sube al inicio. Debe ejecutarse antes de enviar cualquier HTML.
header("Location: index.php" . $ancla);

// exit: detiene la ejecución para no renderizar nada más.
exit;
