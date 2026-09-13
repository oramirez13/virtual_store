<?php
// save_inquiry.php: receives the inquiry form data via POST,
// validates it and stores it in the Inquiries table of the
// database. It follows the "receiver page" pattern.

// include 'connection.php': incorporates the open connection.
// If the connection failed, that file already shows the error.
include 'connection.php';

// Variables for the result message
$message = "";
$messageType = "success";  // "success" (green) or "danger" (red)

// Verifies that the form data arrived via POST.
// Defensive reading: isset() checks before using the variable.
if(isset($_POST['name'], $_POST['email'], $_POST['detail'])){

    // Retrieves and trims leading/trailing spaces of each field.
    // trim() avoids saving values with unnecessary whitespace.
    $name   = trim($_POST['name']);
    $phone  = isset($_POST['phone']) ? trim($_POST['phone']) : "";
    $email  = trim($_POST['email']);
    $detail = trim($_POST['detail']);

    // Validation: name, email and detail are required.
    if($name != "" && $email != "" && $detail != ""){

        // filter_var() with FILTER_VALIDATE_EMAIL checks if the
        // email has a valid format (user@domain). Returns true/false.
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){

            // The try block wraps the database insertion.
            try {

                // Prepared statement: the query is prepared with
                // placeholders (?) and the values are sent separately
                // at execution time. The data is never interpreted
                // as part of the SQL.
                $stmt = $connection->prepare(
                    "INSERT INTO Inquiries (name, phone, email, detail) VALUES (?, ?, ?, ?)"
                );

                // bind_param("ssss", ...): substitutes (?) with the values.
                // The four "s" declare that the data are strings.
                $stmt->bind_param("ssss", $name, $phone, $email, $detail);

                // execute(): runs the prepared query. If it fails,
                // a mysqli_sql_exception is thrown here.
                $stmt->execute();

                // Frees the prepared statement
                $stmt->close();

                // Closes the connection when no longer needed
                $connection->close();

                // Success message for the user
                $message = "Your inquiry was sent successfully. We will reply soon.";
                $messageType = "success";

            // catch captures only MySQL errors.
            } catch (mysqli_sql_exception $errorDetail) {

                // error_log(): writes the technical error detail to
                // the local error log (Apache log).
                error_log("Error saving the inquiry: " . $errorDetail->getMessage());

                // Friendly message for the user, without internal detail.
                $message = "An error occurred while sending your inquiry. Please try again later.";
                $messageType = "danger";
            }
        }else{
            // The email format is not valid
            $message = "The entered email address is not valid.";
            $messageType = "danger";
        }
    }else{
        // One of the required fields was missing
        $message = "You must complete the name, email and inquiry detail.";
        $messageType = "danger";
    }
}else{
    // Not all expected data was received
    $message = "The inquiry data was not received.";
    $messageType = "danger";
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inquiry sent - Virtual Store</title>

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
      <!-- Centered confirmation card -->
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h1 class="h4 mb-3">Inquiry form</h1>

          <!-- Alert that shows the message; the color depends on $messageType.
               alert-success (green) for success, alert-danger (red) for error -->
          <div class="alert alert-<?php echo $messageType; ?> mb-3">
            <?php echo $message; ?>
          </div>

          <!-- Link to return to the gallery -->
          <a href="index.php" class="btn btn-primary btn-sm">Back to the gallery</a>
        </div>
      </div>
    </div>
  </body>
</html>