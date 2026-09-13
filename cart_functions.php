<?php
// cart_functions.php: contains reusable cart functions.
// Its goal is to concentrate the grouping logic for
// repeated products in a single file, so cart.php and
// checkout.php do not duplicate the same code.
//
// Problem solved: if the user adds the same product
// several times, the cart stores an array of codes, e.g.
// [1, 4, 4, 4]. This file groups those repeated codes
// to show a single row per product with a quantity column
// (e.g. product 4 x 3), instead of showing three identical
// rows. This makes the summary more readable, computes the
// total as price * quantity, and queries the database
// only once per product.

/**
 * buildGroupedCartItems: rebuilds the cart products grouped
 * by code, adding the unit count and computing the subtotal.
 *
 * @param mysqli $connection Already open connection to the
 *                           Store database.
 * @param array  $cart       Array with the codes (may repeat).
 *
 * @return array Associative array with three elements:
 *                - items: product rows, each with the 'quantity'
 *                  and 'subtotal' keys added to the DB data.
 *                - total: sum of all subtotals (price*qty).
 *                - error: error message if something failed,
 *                  or "" if everything went fine.
 */
function buildGroupedCartItems($connection, $cart) {

    // Container where the grouped products will accumulate
    $items = array();

    // Accumulator for the total to pay (price * qty of each)
    $total = 0;

    // Error message; stays empty if no failure occurs
    $error = "";

    // array_count_values(): counts how many times each code
    // appears in the array. For example, cart [1, 4, 4, 4]
    // generates [1 => 1, 4 => 3], i.e. the key is the code
    // and the value is the quantity of that product.
    $quantities = array_count_values($cart);

    // The try block wraps the database queries.
    try {

        // Iterates over each unique cart product. In the foreach,
        // $code takes the key (product code) and $quantity takes
        // the value (how many units there are).
        foreach ($quantities as $code => $quantity) {

            // (int) enforces the code as an integer before using
            // it in the query. Non-numeric values become 0.
            $code = (int)$code;

            // Prepared statement: the query has a placeholder (?)
            // and the value is sent separately, so the database
            // never interprets it as part of the SQL.
            $stmt = $connection->prepare("SELECT * FROM Products WHERE code = ?");

            // bind_param("i"): the "i" declares the data as integer
            $stmt->bind_param("i", $code);

            // execute(): runs the prepared query
            $stmt->execute();

            // get_result(): gets the result as an mysqli_result object
            $result = $stmt->get_result();

            // fetch_assoc(): reads the first row (or null if none)
            $row = $result->fetch_assoc();

            // Frees the prepared statement
            $stmt->close();

            // Only if the product exists it is added to the list
            if ($row != null) {

                // Adds the unit count to the product data
                $row['quantity'] = $quantity;

                // Computes the subtotal: unit price * quantity
                $row['subtotal'] = $row['price'] * $quantity;

                // Adds the full row to the items array
                $items[] = $row;

                // Accumulates the subtotal in the cart total
                $total += $row['subtotal'];
            }
        }

    } catch (mysqli_sql_exception $errorDetail) {

        // error_log(): writes the technical error detail to the
        // local error log (Apache log).
        error_log("Error grouping the cart: " . $errorDetail->getMessage());

        // Friendly message for the user, without internal detail.
        $error = "An error occurred while loading the cart. Please try again later.";
    }

    // Returns the three values in a single associative array;
    // the page calling this function decides how to display them.
    return array(
        'items' => $items,
        'total' => $total,
        'error' => $error
    );
}
?>