<?php
// index.php: displays the product gallery and allows adding
// each product to the shopping cart. The cart is stored in the
// user session.

// session_start(): opens the session or resumes the existing one.
// It must run before any HTML output because it sends the HTTP
// header of the session cookie.
session_start();

// include 'products.php': queries the products and prepares
// the $products array to iterate in the gallery
include 'products.php';

// Reads the session cart; if it does not exist, it is treated
// as empty. isset() checks the key before using it.
$counts = [];
if(isset($_SESSION['cart'])){
    // array_count_values(): counts how many times each code
    // appears; with [4, 7, 4] it returns [4 => 2, 7 => 1]
    $counts = array_count_values($_SESSION['cart']);
}
// array_sum(): adds the quantities to get the total item count
$quantity = array_sum($counts);

// Flash message: if present in the session, it is copied to a
// local variable and removed from the session to show it once.
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
    <title>Virtual Store - Shopping Cart</title>

    <!-- Bootstrap CSS framework -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />

    <!-- Project own styles -->
    <link rel="stylesheet" href="css/style.css" />
  </head>

  <body>
    <!-- Top bar; "top" is the destination of the "back to top" button -->
    <nav id="top" class="navbar navbar-dark bg-dark mb-4">
      <!-- Bootstrap flex: brand on the left, links on the right -->
      <div class="container d-flex justify-content-between align-items-center">
        <span class="navbar-brand mb-0 h1">UNIX T-Shirts Virtual Store</span>
        <div>
        <?php if($quantity > 0){ ?>
          <!-- Cart link with the item counter -->
          <a href="cart.php" class="text-white text-decoration-none">
            <img src="img/icons8-shopping-cart-48.png" alt="Cart" style="width: 22px;" class="me-1">
            Cart (<?php echo $quantity; ?>)
          </a>
        <?php } else { ?>
          <!-- Cart link without counter when there are no items -->
          <a href="cart.php" class="text-white text-decoration-none">
            <img src="img/icons8-shopping-cart-48.png" alt="Cart" style="width: 22px;" class="me-1">
            Cart
          </a>
        <?php } ?>
          <!-- Ends the whole session: deletes the cookie and the
               data stored on the server -->
          <a href="inquiry.php" class="text-white text-decoration-none ms-3">Inquiries</a>
          <!-- Error handling examples by season: each button queries
               an inventory whose access fails and shows an error
               message to the user -->
          <a href="error_example.php?season=summer" class="text-white text-decoration-none ms-3">Summer</a>
          <a href="error_example.php?season=winter" class="text-white text-decoration-none ms-3">Winter</a>
          <a href="logout.php" class="text-white text-decoration-none ms-3">Log out</a>
        </div>
      </div>
    </nav>

    <?php if($flash != ""){ ?>
      <!-- Floating Bootstrap alert with the flash message.
           The color depends on $flashType (success/danger/warning) -->
      <div class="container">
        <div class="alert alert-<?php echo $flashType; ?> alert-dismissible fade show" role="alert">
          <?php echo $flash; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      </div>
    <?php } ?>

    <div class="container">
      <h1 class="mb-4">Product Gallery</h1>

      <div class="row">
        <?php
        // foreach(): iterates the products; on each pass $product
        // is one row
        foreach ($products as $product) {

            // htmlspecialchars(): escapes the data before printing
            // to prevent DB content from executing as HTML
            $code  = htmlspecialchars($product['code']);
            $name  = htmlspecialchars($product['name']);
            $detail = htmlspecialchars($product['detail']);
            $image  = htmlspecialchars($product['image']);

            // number_format(): formats the price with 2 decimals
            $price = number_format($product['price'], 2);
        ?>
          <!-- Card: full width on mobile and one third on desktop.
               The id (product-CODE) serves as an anchor: when a
               product is added, add_to_cart.php redirects here and
               the browser stays where the user clicked -->
          <div class="col-12 col-md-4 mb-4" id="product-<?php echo $code; ?>">
            <div class="card h-100 shadow-sm">
              <!-- Product photo; on click, script.js opens the modal -->
              <img src="<?php echo $image; ?>" class="card-img-top img-producto" alt="<?php echo $name; ?>">

              <div class="card-body">
                <?php if(isset($counts[$product['code']])){ ?>
                  <!-- Badge indicating the product is already in the
                       cart; uses the count read from the session -->
                  <span class="badge text-bg-success mb-2">
                    In your cart (x<?php echo $counts[$product['code']]; ?>)
                  </span>
                <?php } ?>
                <h5 class="card-title"><?php echo $name; ?></h5>
                <p class="card-text"><?php echo $detail; ?></p>
              </div>

              <div class="card-footer bg-white">
                <small class="text-muted">Code: <?php echo $code; ?></small>
                <!-- Bootstrap classes: bold green colored text -->
                <p class="mb-0 fw-bold text-success">&#8353; <?php echo $price; ?></p>

                <!-- Form that sends the product code to
                     add_to_cart.php. The hidden field travels via
                     POST without being shown on screen -->
                <form method="post" action="add_to_cart.php" class="mt-2">
                  <input type="hidden" name="code" value="<?php echo $code; ?>">
                  <button type="submit" class="btn btn-primary btn-sm w-100">
                    Add to cart
                  </button>
                </form>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>

    <!-- Hidden modal that shows the enlarged image when clicking a photo -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalTitle" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <!-- Modal title with the product name -->
            <h5 class="modal-title" id="imageModalTitle">Product</h5>
            <!-- Bootstrap native close X button -->
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-0">
            <!-- Enlarged image; its source is assigned by script.js -->
            <img id="enlargedImage" src="" alt="" class="img-fluid w-100">
          </div>
        </div>
      </div>
    </div>

    <!-- Floating back-to-top button (anchor to #top) -->
    <a href="#top" class="btn btn-dark btn-volver-arriba position-fixed bottom-0 end-0 m-4 shadow-sm"
       aria-label="Back to top">&#8593;</a>

    <!-- Bootstrap JavaScript (modal) -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
      crossorigin="anonymous"
    ></script>

    <!-- Modal own logic -->
    <script src="js/script.js" type="text/javascript"></script>
  </body>
</html>