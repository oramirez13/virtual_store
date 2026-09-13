<?php
// checkout.php: shows the detail of the items the customer
// bought and the total amount. Once finished, the session
// cart is emptied, as if the sale had already been completed.

// Loads the session before any output
session_start();

// Reads the cart; if it does not exist, starts as an empty array.
// Defensive reading: isset() checks before using the variable.
if(isset($_SESSION['cart'])){
    $cart = $_SESSION['cart'];
}else{
    $cart = [];
}

// Array that will hold the complete data of each product
$items = [];
// Accumulator for the total to pay
$total = 0;
// Message shown if an error occurs
$error = "";

// require 'cart_functions.php': joins the file with the function
// that groups repeated products. require is used because this
// file is essential to prepare the purchase summary.
require 'cart_functions.php';

// If there are products in the cart, rebuilds them grouped by code
if(count($cart) > 0){
    // include 'connection.php': incorporates the open connection.
    // If the connection failed, that file already shows the error.
    include 'connection.php';

    // buildGroupedCartItems($connection, $cart): calls the function
    // from cart_functions.php. It counts how many times each code
    // appears, queries each product once in the database and
    // computes the quantity and subtotal of each one.
    // Returns an associative array with three keys: items, total
    // and error.
    $grouped = buildGroupedCartItems($connection, $cart);

    // Copies the three results of the function to local variables
    $items = $grouped['items'];
    $total = $grouped['total'];
    $error = $grouped['error'];

    // Closes the connection when no longer needed
    $connection->close();
}

// If there are products and no error, the purchase is considered
// completed and the session cart is emptied.
if(count($items) > 0 && $error == ""){
    // unset(): removes the 'cart' key from the session. session_destroy()
    // is not used because that would end the whole session; here only
    // the cart is cleared after confirming the purchase.
    unset($_SESSION['cart']);
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Purchase completed - Virtual Store</title>

    <!-- Bootstrap CSS framework -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />
  </head>

  <body>
    <div class="container mt-4" style="max-width: 640px;">
      <h1 class="mb-4">Your Purchase Summary</h1>

      <?php if($error != ""){ ?>
        <!-- Friendly error message when the query fails -->
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <a href="index.php" class="btn btn-primary">Back to the gallery</a>
      <?php } elseif(count($items) == 0){ ?>
        <!-- Notice when there are no products in the cart -->
        <div class="alert alert-info">
          Your cart was empty, there is no purchase to complete.
          <a href="index.php">Go to the gallery</a>
        </div>
      <?php } else { ?>
        <!-- Confirmation that the purchase was registered -->
        <div class="alert alert-success">
          Thank you for your purchase! The purchased items are listed below.
        </div>

        <!-- Table with the purchased items (one row per product,
             grouped by quantity when it repeats in the cart) -->
        <table class="table table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th>Code</th>
              <th>Product</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($items as $item){ ?>
              <tr>
                <td><?php echo htmlspecialchars($item['code']); ?></td>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td>&#8353; <?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>&#8353; <?php echo number_format($item['subtotal'], 2); ?></td>
              </tr>
            <?php } ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="text-end fw-bold">Total</td>
              <td></td>
              <td class="text-end fw-bold text-success">&#8353; <?php echo number_format($total, 2); ?></td>
            </tr>
          </tfoot>
        </table>

        <!-- Link to return to the gallery -->
        <a href="index.php" class="btn btn-primary">Back to the gallery</a>
      <?php } ?>
    </div>
  </body>
</html>