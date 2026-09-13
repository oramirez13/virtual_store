<?php
// error_example.php: educational error handling page.
// Based on the "season" parameter received via GET, it tries
// to query a seasonal inventory: "winter" or "summer". None
// of those databases exist, so the connection fails and
// error handling is demonstrated with try-catch, error_log()
// and finally.

// Enable MySQLi error reporting as exceptions. Any connection
// or query failure is thrown as a mysqli_sql_exception,
// catchable with try-catch.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// require 'config.php': loads the credentials from the central
// configuration file, so they are not repeated or hardcoded
// in this logic file (good practice: credentials outside code).
require 'config.php';

// Reads the season received via GET. If it does not arrive or
// is unknown, "winter" is used as the default value.
// isset() checks defensively before using the variable.
$season = "winter";
if(isset($_GET['season']) && ($_GET['season'] == "summer" || $_GET['season'] == "winter")){
    $season = $_GET['season'];
}

// Database name according to the season. The databases do not
// exist on the server, so the connection will raise an exception.
if($season == "summer"){
    $databaseName = "inventory_summer";     // Summer inventory database (missing)
}else{
    $databaseName = "inventory_winter";     // Winter inventory database (missing)
}

// Array that would hold the inventory listing if everything worked
$inventory = array();

// The try block attempts the operations that could fail.
try {

    // Tries to connect to the chosen season database, using the
    // credentials loaded from config.php. Only the database name
    // differs: since it does not exist, new mysqli() throws an
    // exception.
    $connection = new mysqli($host, $user, $password, $databaseName);

    // If it did connect (not the case), it would load a table
    // called Items with the seasonal products.
    $stmt = $connection->prepare("SELECT * FROM Items");
    $stmt->execute();
    $result = $stmt->get_result();

    // Reads all rows and stores them in the array
    while ($row = $result->fetch_assoc()) {
        $inventory[] = $row;
    }

    // Frees the prepared statement
    $stmt->close();

    // Closes the database connection
    $connection->close();

// catch captures the exception thrown by MySQL.
} catch (mysqli_sql_exception $error) {

    // Logs the technical error detail to the local log
    // (Apache log). Reviewers are support staff, not the
    // final user.
    error_log("Error querying the {$season} inventory: " . $error->getMessage());

    // Friendly error message for the user, without showing the
    // technical detail. Customized with the queried season name.
    $errorMessage = "A problem occurred while querying the {$season} inventory.
                     Please try again later or contact the administrator.";

// finally always runs, whether there was an error or not.
} finally {

    // If there was no error, $errorMessage is empty and nothing is shown.
    $errorMessage = isset($errorMessage) ? $errorMessage : "";
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Error handling - Virtual Store</title>

    <!-- Bootstrap CSS framework -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />
  </head>

  <body>
    <div class="container mt-5" style="max-width: 480px;">

      <?php if($errorMessage != ""){ ?>
        <!-- Card showing the friendly error message -->
        <div class="card shadow-sm border-danger">
          <div class="card-body text-center">
            <h1 class="h4 mb-3 text-danger">System error</h1>
            <p class="mb-4"><?php echo $errorMessage; ?></p>
          </div>
        </div>
      <?php } ?>

      <!-- Button to return to the main page, with the same secondary
           button style used across the site -->
      <div class="text-center mt-3">
        <a href="index.php" class="btn btn-outline-secondary btn-sm">&lt;= Back to the main page</a>
      </div>

    </div>
  </body>
</html>