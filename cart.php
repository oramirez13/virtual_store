<?php
// cart.php: page where all the products stored in the
// session cart are displayed.

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
// file is essential for the cart to work.
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

// Flash message: if present in the session, it is copied to a
// local variable and removed from the session to show it only once.
$flash = "";
$flashType = "success";
if(isset($_SESSION['flash'])){
    $flash = $_SESSION['flash']['message'];
    $flashType = $_SESSION['flash']['type'];
    // unset(): removes the already consumed flash message
    unset($_SESSION['flash']);
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart - Virtual Store</title>

    <!-- Bootstrap CSS framework -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />
  </head>

  <body>
    <div class="container mt-4">
      <h1 class="mb-4">
        <!-- Cart icon next to the title, same style as the bar -->
        <img src="img/icons8-shopping-cart-48.png" alt="Cart" style="width: 28px;" class="me-2">
        Shopping Cart
      </h1>

      <?php if($flash != ""){ ?>
        <!-- Bootstrap alert with the flash message (e.g. "cart emptied").
             The color depends on $flashType (success/warning/danger) -->
        <div class="alert alert-<?php echo $flashType; ?> alert-dismissible fade show" role="alert">
          <?php echo $flash; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php } ?>

      <?php if($error != ""){ ?>
        <!-- Friendly error message when the query fails -->
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php } ?>

      <?php if(count($items) == 0){ ?>
        <!-- Notice when there are no products in the cart -->
        <div class="alert alert-info">
          Your cart is empty. <a href="index.php">Go to the gallery</a>
        </div>
      <?php } else { ?>
        <!-- Product table: one row per product, grouped by quantity
             when it repeats several times in the cart -->
        <table class="table table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th></th>
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
                <td style="width: 80px;">
                  <!-- Product image thumbnail -->
                  <img src="<?php echo htmlspecialchars($item['image']); ?>"
                       alt="<?php echo htmlspecialchars($item['name']); ?>"
                       class="img-thumbnail">
                </td>
                <td><?php echo htmlspecialchars($item['code']); ?></td>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td>&#8353; <?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>&#8353; <?php echo number_format($item['subtotal'], 2); ?></td>
              </tr>
            <?php } ?>
          </tbody>
          <!-- Final row with the total of all subtotals -->
          <tfoot>
            <tr>
              <td colspan="4" class="text-end fw-bold">Total</td>
              <td></td>
              <td class="fw-bold text-success">&#8353; <?php echo number_format($total, 2); ?></td>
            </tr>
          </tfoot>
        </table>

        <!-- Cart action buttons.
             "Checkout" sends POST to checkout.php to view the
             summary; "Empty cart" sends POST to clear_cart.php.
             Both use POST because they modify the cart state -->
        <form method="post" action="checkout.php" class="d-inline">
          <button type="submit" class="btn btn-success">Checkout</button>
        </form>
        <form method="post" action="clear_cart.php" class="d-inline">
          <button type="submit" class="btn btn-danger">Empty cart</button>
        </form>
        <a href="index.php" class="btn btn-outline-secondary">&lt;= Continue shopping</a>
      <?php } ?>
    </div>
  </body>
</html>