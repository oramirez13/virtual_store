<?php
// vaciar.php: elimina todos los productos del carrito de la sesión y
// redirige automáticamente a la página del carrito. Es el destino del
// botón "Vaciar carrito". Sigue el patrón Post/Redirect/Get: el mensaje
// de resultado se guarda en la sesión y se muestra en la página destino.

// session_start(): abre la sesión antes de cualquier salida
session_start();

// unset(): elimina una llave específica del arreglo $_SESSION.
// Aquí borra solo el carrito; el resto de la sesión sigue activa.
// No se usa session_destroy() porque cerraría la sesión completa.
unset($_SESSION['carrito']);

// Redirección automática: el navegador solicita la página del carrito.
// El carrito vacío se muestra por sí solo (el aviso "está vacío" lo
// genera carrito.php), por lo que no se usa ningún mensaje flash.
header("Location: carrito.php");

// exit: detiene la ejecución.
exit;
