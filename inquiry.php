<?php
// inquiry.php: shows the form for the customer to submit
// an inquiry. This file only presents the form; the
// processing is done by save_inquiry.php when it receives
// the POST.
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inquiry form - Virtual Store</title>

    <!-- Bootstrap CSS framework -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />
  </head>

  <body>
    <div class="container mt-5" style="max-width: 560px;">
      <div class="card shadow-sm">
        <div class="card-body">
          <h1 class="h4 mb-4">Inquiry form</h1>

          <!-- The form sends the data via POST to save_inquiry.php.
               The require/required attributes activate browser validation -->
          <form method="post" action="save_inquiry.php">

            <!-- Customer name field -->
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control" id="name" name="name"
                     required placeholder="Enter your full name">
            </div>

            <!-- Phone field (optional) -->
            <div class="mb-3">
              <label for="phone" class="form-label">Phone</label>
              <input type="tel" class="form-control" id="phone" name="phone"
                     placeholder="Contact number">
            </div>

            <!-- Email field, validated by the browser -->
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email"
                     required placeholder="user@example.com">
            </div>

            <!-- Inquiry detail field -->
            <div class="mb-3">
              <label for="detail" class="form-label">Inquiry detail</label>
              <textarea class="form-control" id="detail" name="detail" rows="4"
                        required placeholder="Describe your inquiry"></textarea>
            </div>

            <!-- Submit button -->
            <button type="submit" class="btn btn-primary w-100">Send inquiry</button>
          </form>

          <!-- Link to return to the gallery -->
          <div class="text-center mt-3">
            <a href="index.php" class="btn btn-outline-secondary btn-sm">&lt;= Back to the gallery</a>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>