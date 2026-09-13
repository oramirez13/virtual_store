<?php
// config.php: configuration file holding the database
// credentials. It keeps connection details separate from
// the connection logic for easy maintenance.
//
// Credentials are read from system environment variables
// using getenv(). If an environment variable is NOT defined,
// a safe example value is used as a fallback. This way the
// project works out of the box with safe defaults, while
// production credentials can be injected from the server
// environment using SetEnv or export, without touching this file.
//
// IMPORTANT: password and username do NOT use the ?: operator
// because an empty password ("") is valid in LAMPP, and ?:
// would treat that empty string as "false" and apply the
// fallback. That is why we explicitly compare against false
// (variable not defined), distinguishing "not defined" from
// "defined but empty".

// Server where MariaDB runs (same machine with LAMPP).
// getenv('DB_HOST') returns the value of the DB_HOST
// environment variable or false if it does not exist.
// The ?: operator applies the fallback if the result is
// falsy or empty; "localhost" is never a secret.
$host = getenv('DB_HOST') ?: 'localhost';

// Database username. getenv() is used and compared to false:
// if the variable is not defined, the example value is applied.
$user = getenv('DB_USER');
if ($user === false) {
    $user = 'user';
}

// Database password. In LAMPP the default is empty (""),
// so ?: is NOT used. We only check if the environment
// variable is defined; if it is (even if empty), it is
// used as-is. The fallback keeps an example value ("password")
// to avoid exposing credentials in the repository.
$password = getenv('DB_PASSWORD');
if ($password === false) {
    $password = 'password';
}

// Project database name.
$database = getenv('DB_NAME') ?: 'Store';
?>