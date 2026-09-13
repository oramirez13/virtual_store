<?php
// add_to_cart.php: receives the product code via POST,
// validates it and saves it to the session cart. Then it
// automatically redirects to the gallery (index.php) to
// simplify navigation without showing an intermediate
// confirmation page.
//
// This pattern is called "Post/Redirect/Get": the action
// is processed here and the browser returns to the main
// page, where the flash message saved in the session is
// displayed.

// Loads the session before any output
session_start();

// Reads the current cart; if it does not exist, starts
// as an empty array. Defensive reading: isset() checks
// before using the variable.
if(isset($_SESSION['cart'])){
    $cart = $_SESSION['cart'];
}else{
    $cart = [];
}

// Anchor fragment (#product-CODE) appended to the redirect
// URL so the gallery scrolls to the card where the user
// added the product. Defaults to empty (no anchor).
$anchor = "";

// Verifies that a code was received from the gallery form
if(isset($_POST['code'])){

    // (int): converts the value to an integer. If someone
    // sends non-numeric text, it becomes 0 and is rejected
    // by the validation.
    $code = (int)$_POST['code'];

    if($code > 0){

        // Connects and looks up the product to verify it
        // exists; connection.php in turn loads credentials
        // from config.php.
        include 'connection.php';

        // The try block wraps the database operations.
        try {

            // Prepared statement: the query is prepared with a
            // placeholder (?) and the value is sent separately
            // at execution time. The data is never interpreted
            // as part of the SQL.
            $stmt = $connection->prepare("SELECT name FROM Products WHERE code = ?");

            // bind_param("i", $code): substitutes (?) with the
            // value. The "i" declares that the data is an integer.
            $stmt->bind_param("i", $code);

            // execute(): runs the prepared query. If it fails,
            // a mysqli_sql_exception is thrown here.
            $stmt->execute();

            // get_result(): gets the result as an mysqli_result object
            $result = $stmt->get_result();

            // fetch_assoc(): reads the first row (or null if none)
            $row = $result->fetch_assoc();

            // Frees the prepared statement; connection closes at the end
            $stmt->close();

            if($row != null){
                // The product exists: its code is added to the end
                // of the array. $_SESSION supports native arrays,
                // no implode() needed.
                $cart[] = $code;

                // Saves the complete cart back to the session
                $_SESSION['cart'] = $cart;

                // Anchor: the redirect will point to this product's
                // card so the gallery does not scroll to the top.
                $anchor = "#product-" . $code;

                // Flash message: displayed only once in the gallery.
                // 'type' defines the alert color (success/danger).
                $_SESSION['flash'] = [
                    'type'    => 'success',
                    'message' => 'Added to cart: ' . htmlspecialchars($row['name'])
                ];
            }else{
                // The code does not correspond to any product
                $_SESSION['flash'] = [
                    'type'    => 'danger',
                    'message' => 'The requested product does not exist.'
                ];
            }

            // Closes the connection when no longer needed
            $connection->close();

        // catch captures only MySQL errors.
        } catch (mysqli_sql_exception $error) {

            // error_log(): writes the technical error detail to
            // the local error log (Apache log).
            error_log("Error adding product to cart: " . $error->getMessage());

            // Friendly message for the user, without technical detail.
            $_SESSION['flash'] = [
                'type'    => 'danger',
                'message' => 'An error occurred while adding the product. Please try again later.'
            ];
        }
    }else{
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => 'The received code is not valid.'
        ];
    }
}else{
    $_SESSION['flash'] = [
        'type'    => 'danger',
        'message' => 'No product was received.'
    ];
}

// Automatic redirect: sends an HTTP header that tells the
// browser to request the gallery. If a product was added,
// the URL includes the anchor (#product-CODE) and the
// browser positions itself at that card so the page does
// not scroll to the top. Must run before any HTML output.
header("Location: index.php" . $anchor);

// exit: stops execution to prevent rendering anything else.
exit;