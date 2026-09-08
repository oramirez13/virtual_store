<?php
// config.php: archivo de configuración con los datos de acceso
// a la base de datos. Mantiene las credenciales separadas del
// código de conexión para centralizar su edición.
//
// Las credenciales se leen de las variables de entorno del sistema
// con getenv(). Si una variable de entorno NO está definida, se usa
// el valor de ejemplo que aparece como respaldo. Así el proyecto
// mantiene valores seguros por omisión, pero en un entorno real las
// credenciales pueden inyectarse desde el exterior con SetEnv o
// export, sin modificar este archivo.
//
// DETALLE IMPORTANTE: la contraseña y el usuario no usan el operador ?:
// porque una contraseña vacía ("") es un valor válido en LAMPP, y ?:
// trataría esa cadena vacía como "falsa" y pondría el respaldo.
// Por eso se compara explícitamente con false (variable no definida),
// distinguiendo así "no definida" de "definida pero vacía".

// Servidor donde se ejecuta MariaDB (misma máquina con LAMPP).
// getenv('DB_HOST') devuelve el valor de la variable de entorno DB_HOST
// o false si no existe. El operador ?: usa el respaldo si el valor
// resultante es falso o vacío; aquí "localhost" nunca es un secreto.
$host = getenv('DB_HOST') ?: 'localhost';

// Usuario de la base de datos. Se usa getenv() y se compara con false:
// si la variable no está definida, se aplica el valor de ejemplo.
$usuario = getenv('DB_USUARIO');
if ($usuario === false) {
    $usuario = 'usuario';
}

// Contraseña del usuario. En LAMPP el valor por defecto es vacío (""),
// por lo que NO se usa ?:. Se comprueba solo si la variable de entorno
// está definida; si lo está (aunque sea vacía), se respeta tal cual.
// El respaldo conserva un valor de ejemplo ("contrasena") para no
// exponer credenciales dentro del repositorio.
$contrasena = getenv('DB_CONTRA');
if ($contrasena === false) {
    $contrasena = 'contrasena';
}

// Nombre de la base de datos del proyecto.
$basedatos = getenv('DB_BASEDATOS') ?: 'Tienda';
?>