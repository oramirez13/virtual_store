<?php
// funciones_carrito.php: contiene funciones reutilizables del carrito.
// Su objetivo es concentrar en un solo archivo la lógica de agrupación
// de los productos repetidos, para que carrito.php y finalizar_compra.php
// no dupliquen el mismo código.
//
// Qué problema resuelve: si el usuario agrega varias veces el mismo
// producto, el carrito guarda un arreglo de códigos, por ejemplo
// [1, 4, 4, 4]. Este archivo agrupa esos códigos repetidos para mostrar
// una sola fila por producto con una columna de cantidad (ej. producto 4
// x 3), en lugar de mostrar tres filas idénticas. Así el resumen es más
// legible, el total se calcula como precio * cantidad y se consulta la
// base de datos una sola vez por producto.

/**
 * armarItemsAgrupados: reconstruye los productos del carrito agrupados
 * por código, sumando la cantidad de unidades y calculando el subtotal.
 *
 * @param mysqli $conexion Conexión ya abierta a la base de datos Tienda.
 * @param array  $carrito  Arreglo con los códigos (pueden repetirse).
 *
 * @return array Arreglo asociativo con tres elementos:
 *               - items: filas de productos, cada una con la llave
 *                 'cantidad' y 'subtotal' agregadas a los datos de la BD.
 *               - total: suma de todos los subtotales (precio*cantidad).
 *               - error: mensaje de error si algo falló, o "" si todo
 *                 salió bien.
 */
function armarItemsAgrupados($conexion, $carrito) {

    // Caja donde se irán acumulando los productos ya agrupados
    $items = array();

    // Acumulador del total a pagar (precio * cantidad de cada uno)
    $total = 0;

    // Mensaje de error; queda vacío si no ocurre ningún fallo
    $error = "";

    // array_count_values(): cuenta cuántas veces aparece cada código
    // dentro del arreglo. Por ejemplo, con carrito [1, 4, 4, 4] genera
    // [1 => 1, 4 => 3], es decir: la llave es el código y el valor es
    // la cantidad de unidades de ese producto.
    $cantidades = array_count_values($carrito);

    // El bloque try envuelve las consultas con la base de datos.
    try {

        // Recorre cada producto único del carrito. En el foreach la
        // variable $codigo toma la llave (el código del producto) y la
        // variable $cantidad toma el valor (cuántas unidades hay).
        foreach ($cantidades as $codigo => $cantidad) {

            // (int) refuerza que el código sea un entero antes de usarlo
            // en la consulta. Si un valor no es numérico, queda en 0.
            $codigo = (int)$codigo;

            // Prepared statement: la consulta lleva un marcador (?) y el
            // valor se envía por separado, de modo que la base nunca lo
            // interpreta como parte del SQL.
            $consulta = $conexion->prepare("SELECT * FROM Productos WHERE codigo = ?");

            // bind_param("i"): la "i" declara que el dato es un entero
            $consulta->bind_param("i", $codigo);

            // execute(): ejecuta la consulta ya preparada
            $consulta->execute();

            // get_result(): obtiene el resultado como objeto mysqli_result
            $resultado = $consulta->get_result();

            // fetch_assoc(): lee la primera fila (o null si no existe)
            $fila = $resultado->fetch_assoc();

            // Libera la consulta preparada
            $consulta->close();

            // Solo si el producto existe se agrega al listado agrupado
            if ($fila != null) {

                // Agrega la cantidad de unidades a los datos del producto
                $fila['cantidad'] = $cantidad;

                // Calcula el subtotal: precio unitario * cantidad
                $fila['subtotal'] = $fila['precio'] * $cantidad;

                // Agrega la fila completa al arreglo de items
                $items[] = $fila;

                // Acumula el subtotal en el total general del carrito
                $total += $fila['subtotal'];
            }
        }

    } catch (mysqli_sql_exception $errorDetalle) {

        // error_log(): guarda el detalle técnico del error en la bitácora
        // local (log de Apache).
        error_log("Error al agrupar el carrito: " . $errorDetalle->getMessage());

        // Mensaje amigable para el usuario, sin detalle técnico interno.
        $error = "Ocurrió un error al consultar el carrito. Intente más tarde.";
    }

    // Devuelve los tres datos en un solo arreglo asociativo; la página
    // que llama a esta función decide cómo mostrarlos.
    return array(
        'items' => $items,
        'total' => $total,
        'error' => $error
    );
}
?>