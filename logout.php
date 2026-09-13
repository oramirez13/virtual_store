<?php
// logout.php: completely ends the user session, both the
// browser cookie and the data stored on the server.

// session_start(): opens the session before modifying its cookie
session_start();

// session_name(): name of the session cookie (default PHPSESSID)
$sessionName = session_name();

// session_get_cookie_params(): real attributes of the cookie (path, etc.)
$sessionParams = session_get_cookie_params();

// setcookie(): sends a cookie with an expired date (1) and the same
// path, so the browser deletes the original session cookie
setcookie($sessionName, '', 1, $sessionParams["path"]);

// session_destroy(): removes the session data on the server.
// Unlike clear_cart.php, here the complete session is ended.
session_destroy();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign in - Virtual Store</title>

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
      <!-- Session close confirmation -->
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h1 class="h4 mb-3">The session has ended</h1>

          <!-- Navigation option to sign in again -->
          <a href="index.php" class="btn btn-primary btn-sm">Sign in</a>
        </div>
      </div>
    </div>
  </body>
</html>