<?php
// config.php: archivo de configuración con los datos de acceso
// a la base de datos. Mantiene las credenciales separadas del
// código de conexión para centralizar su edición.
//
// Las credenciales se leen de las variables de entorno del sistema
// con getenv(). Si una variable de entorno no está definida, se usa
// el valor de ejemplo que aparece después del operador "?:". Así el
// proyecto funciona en cualquier máquina sin configuración previa,
// pero en un entorno de producción las credenciales reales pueden
// inyectarse desde el exterior sin modificar este archivo.
//
// NOTA: los valores de ejemplo no son credenciales verdaderas.
// Las credenciales reales son las de LAMPP/XAMPP por defecto.

// getenv('DB_HOST') devuelve el valor de la variable de entorno DB_HOST
// (por ejemplo, definida en el entorno del servidor). Si no existe,
// getenv() retorna false y el operador "?:" usa el valor de ejemplo
// ("localhost"). Es decir: entorno primero, valores de ejemplo como plan B.
$host = getenv('DB_HOST') ?: 'localhost';

// Usuario de la base de datos. Si el entorno define DB_USUARIO, se usa ese
// valor; si no, se usa el valor de ejemplo "usuario".
$usuario = getenv('DB_USUARIO') ?: 'usuario';

// Contraseña del usuario. El valor por defecto del LAMPP es vacía, pero
// aquí se mantiene un valor de ejemplo ("contrasena") para no exponer
// una credencial real dentro del repositorio.
$contrasena = getenv('DB_CONTRA') ?: 'contrasena';

// Nombre de la base de datos del proyecto. Si el entorno define
// DB_BASEDATOS, se usa ese valor; si no, se usa "Tienda".
$basedatos = getenv('DB_BASEDATOS') ?: 'Tienda';
?>
