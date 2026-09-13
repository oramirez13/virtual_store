<?php
// products.php: queries the Products table and stores all
// rows in the $products array for the gallery to use.

// include 'connection.php': loads the open connection. If
// the connection failed, that file already displays the
// error message.
include 'connection.php';

// The try block attempts to execute the query and read
// the results.
try {

    // query(): sends the SELECT to the database engine; for a
    // SELECT query it returns an mysqli_result object.
    // If the query fails, a mysqli_sql_exception is thrown.
    $result = $connection->query("SELECT * FROM Products");

    // Array that will hold one row per product
    $products = array();

    // fetch_assoc(): reads the current row as an associative
    // array; returns null when there are no more rows
    $row = $result->fetch_assoc();

    // The loop continues while there are rows in the result
    while ($row != null) {
        // Adds the row to the end of the products array
        $products[] = $row;
        // Reads the next row from the result
        $row = $result->fetch_assoc();
    }

    // close(): closes the connection and frees server resources
    $connection->close();

// catch captures only MySQL errors that were thrown.
} catch (mysqli_sql_exception $error) {

    // error_log(): writes the technical error detail to the
    // local error log (Apache log) for review by support staff.
    error_log("Error loading products: " . $error->getMessage());

    // Friendly message for the user, without internal detail.
    die("A product loading error occurred. Please try
        again later or contact the administrator.");
}
?>