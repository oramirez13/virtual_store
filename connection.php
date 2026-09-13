<?php
// connection.php: creates and validates a database
// connection using mysqli. Pages that need queries
// include this file.
//
// Note: this file ONLY opens the connection and stores
// it in the $connection variable. If someone includes
// this file and the connection fails, it is caught here,
// logged to the error log, and a friendly message is
// shown to the user.

// Enable MySQLi error reporting as exceptions. With this,
// any connection or query error is thrown as a
// mysqli_sql_exception, which can be caught with try-catch.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// require 'config.php': loads the credentials from the
// configuration file, keeping them separate from code logic.
require 'config.php';

// The try block attempts to open the connection using
// the loaded config values.
try {

    // new mysqli(): opens the connection with the data
    // loaded above. If the credentials or database are
    // incorrect, this call throws a mysqli_sql_exception
    // and skips the entire try block.
    $connection = new mysqli($host, $user, $password, $database);

// catch captures only mysqli_sql_exception exceptions.
} catch (mysqli_sql_exception $error) {

    // error_log(): writes the technical error detail to the
    // local error log (Apache log). This information is NOT
    // for the user, but for support staff.
    error_log("Database connection error: " . $error->getMessage());

    // Friendly message: shown to the user without revealing
    // internal technical details.
    die("A database error occurred. Please
        try again later or contact the administrator.");
}
?>