# UNIX T-Shirts Virtual Store

**UNIX T-Shirts Virtual Store** is an academic project developed in **PHP 8** with **MySQL/MariaDB**, using **Bootstrap 5.3.8** and **JavaScript** on the frontend.

The application relies on two main pillars:

- **Dynamic catalog**: the products (15 t-shirts) are loaded from the MariaDB database and displayed in a card gallery.
- **Shopping cart**: persisted through PHP sessions (`$_SESSION`), allowing the user to add products, view the cart, empty it and check out.

Basic security practices are applied, such as output sanitization (escaping data with `htmlspecialchars()` against XSS), prepared statements on queries that receive user data, and input validation (casts and checks before using external data). The database credentials live in a separate configuration file (`config.php`).

---

## Table of contents

- [1. Overview](#1-overview)
- [2. Features](#2-features)
- [3. Technologies](#3-technologies)
- [4. Project structure](#4-project-structure)
- [5. Application data flow](#5-application-data-flow)
- [6. Database](#6-database)
- [7. File-by-file description](#7-file-by-file-description)
- [8. Requirements](#8-requirements)
- [9. Installation and execution](#9-installation-and-execution)
- [10. Database credentials (LAMPP default)](#10-database-credentials-lampp-default)
- [11. Security practices](#11-security-practices)
- [12. Screenshots](#12-screenshots)

---

## 1. Overview

The web application shows a catalog of 15 t-shirts stored in a MySQL/MariaDB database and manages a shopping cart through PHP sessions. The user views the products in a card gallery, can enlarge any image with a click (modal) and add products to the cart, whose contents are kept in `$_SESSION`.

It also includes the customer **inquiry form**, the **checkout** feature and **error handling**.

| Layer          | Technology                                  |
| -------------- | ------------------------------------------- |
| Web server     | Apache (included in LAMPP)                  |
| Language       | PHP 8 (mysqli extension and native sessions)|
| Database       | MariaDB (included in LAMPP)                 |
| Frontend       | HTML5 + Bootstrap 5.3.8 + custom CSS        |
| Interactivity  | Vanilla JavaScript + Bootstrap Modal        |

## 2. Features

- Responsive gallery of 15 t-shirts loaded from MariaDB.
- Click on any image: Bootstrap modal with the enlarged photo, product name as title and X close button (also closes with Esc or outside-click).
- Floating "back to top" button with smooth scrolling (bottom-right corner).
- Price formatting in Costa Rican colones.
- "Add to cart" button per card: validates the product in the database and stores it as an array of codes in `$_SESSION['cart']`.
- "In your cart (xN)" badge on cards whose products were already added.
- "Cart (N)" counter in the top bar reflecting the accumulated items.
- Cart page: queries each code in the database, shows thumbnails, **unit quantity** and subtotal per product, plus the accumulated total.
- **Quantity grouping**: if a product is added multiple times, the cart and the summary show it in **a single row with its quantity** (for example, "T-Shirt X  x3") instead of repeating identical rows. The reusable logic lives in `cart_functions.php` (`buildGroupedCartItems()`), which uses `array_count_values()` to count the units.
- "Empty cart" button: clears only the cart with `unset($_SESSION['cart'])` and automatically redirects to the empty cart.
- "Log out" link: deletes the session cookie (with its real path) and runs `session_destroy()`.
- **Inquiry form**: the customer sends name, phone, email and detail; the data is stored in the `Inquiries` table using prepared statements.
- **Checkout**: shows the summary of the purchased items and the total amount, then empties the cart.
- **Error handling**: database operations use `try-catch`, log the detail to the Apache log with `error_log()` and show a friendly message to the user.
- **Error example**: "Summer" and "Winter" buttons that query non-existent inventories (`inventory_summer` / `inventory_winter`) and demonstrate exception handling with user-facing error messages.

## 3. Technologies

- PHP 8 (mysqli extension and native sessions)
- MySQL/MariaDB (LAMPP server)
- HTML5 + Bootstrap 5.3.8
- JavaScript (enlarged image modal)

---

## 4. Project structure

```
tienda_virtual/
├── connection.php       # Opens and validates the MySQL/MariaDB connection using config.php
├── config.php           # Credentials (read with getenv and example values as fallback)
├── cart_functions.php   # Helper: groups repeated products (quantity and subtotal)
├── products.php         # Query logic: gets the products into the $products array
├── index.php            # Gallery + Add forms + badges and cart counter
├── add_to_cart.php      # POST receiver: validates the code and saves it to the session
├── cart.php             # Displays all cart items and the total
├── checkout.php         # Shows the summary and total amount when checking out
├── inquiry.php          # Customer inquiry form
├── save_inquiry.php     # Processes and stores the inquiry in the Inquiries table
├── error_example.php    # Educational error handling example (summer/winter inventory)
├── clear_cart.php       # Clears only the cart (unset)
├── logout.php           # Ends the complete session (cookie + session_destroy)
├── store.sql            # SQL script: table creation + 15 sample products
├── README.md            # General documentation (this file)
├── css/
│   └── style.css        # Custom styles complementing Bootstrap
├── js/
│   └── script.js        # JavaScript: opens the modal when clicking an image
├── img/                 # Local product images
│   ├── tshirt_01.jpg
│   ├── ...
│   ├── tshirt_15.jpg
│   └── icons8-shopping-cart-48.png  # Cart icon in the top bar
└── screenshots/         # Site and phpMyAdmin screenshots
    ├── galeria_01.png            # Main store page
    ├── galeria_02.png            # Gallery with 5 products in the cart
    ├── galeria_03.png            # Modal with the enlarged product image
    ├── sesion_finalizada_01.png  # Notice shown when logging out
    ├── base_de_datos_01.png      # phpMyAdmin: SELECT query on the Products table
    ├── base_de_datos_02.png      # phpMyAdmin: Products table rows
    ├── consulta_01.png           # Inquiry form
    ├── finalizar_compra_01.png   # Cart contents with the total to pay
    ├── finalizar_compra_02.png   # Completed purchase summary with the total
    ├── manejo_de_errores_01.png  # Error handling example
    └── php_error_log_01.png      # PHP error log viewed with tail -f
```

---

## 5. Application data flow

```
User browser
        |
        | HTTP GET request http://localhost/tienda_virtual/
        v
index.php  (presentation)
        |
        | include 'products.php'
        v
products.php  (query logic)
        |
        | include 'connection.php'  ->  connection.php requires 'config.php'
        v
config.php  (credentials)  and  connection.php  (opens the mysqli connection)
        |
        | new mysqli()
        v
MariaDB  ->  "Store" database  ->  "Products" table
        |
        | result: $products array (15 rows)
        v
index.php iterates $products with foreach and generates the HTML cards
        |
        v
Browser renders the gallery; script.js activates the modal on click
```

Cart flow (PHP sessions):

```
Gallery (index.php) --POST code--> add_to_cart.php
    |  validates (int) the code and queries the DB
    v
$_SESSION['cart']  (array of codes, e.g. [1, 4, 4])
    |  -> cart_functions.php  (builds the grouped items: quantity and subtotal)
    |  -> cart.php queries the DB for each unique code and sums the total
    |  -> clear_cart.php   unset($_SESSION['cart'])  (cart only)
    |  -> checkout.php  rebuilds the items, shows the summary and empties the cart
    v
logout.php  setcookie(expired) + session_destroy()  (complete session)
```

Separation of responsibilities:

- `config.php`: only the database credentials (host, user, password, DB), read with `getenv()` and example values as fallback.
- `connection.php`: only opens (and validates) the connection.
- `products.php`: only queries and organizes the data into the `$products` array.
- `cart_functions.php`: only the reusable cart logic (groups repeated codes with quantity and subtotal).
- `index.php`: only presentation (HTML). It knows neither credentials nor SQL.

---

## 6. Database

Database: **Store** | Tables: **Products** and **Inquiries**

### Products table

| Field  | Type         | Constraint  | Use                                 |
| ------ | ------------ | ----------- | ----------------------------------- |
| code   | INT          | PRIMARY KEY | Unique product identifier           |
| name   | VARCHAR(100) |             | Commercial name                     |
| detail | TEXT         |             | Long description                    |
| image  | VARCHAR(255) |             | Photo URL (local or external)       |
| price  | DOUBLE       |             | Price with decimals                 |

### Inquiries table

| Field  | Type         | Constraint                 | Use                                  |
| ------ | ------------ | -------------------------- | ------------------------------------ |
| id     | INT          | AUTO_INCREMENT, PRIMARY KEY | Identifier of each inquiry           |
| name   | VARCHAR(100) | NOT NULL                   | Name of the inquiring customer       |
| phone  | VARCHAR(20)  |                            | Contact phone (optional)             |
| email  | VARCHAR(100) | NOT NULL                   | Customer email                       |
| detail | TEXT         | NOT NULL                   | Description of the inquiry subject   |
| date   | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP  | Registration date and time (automatic) |

The `store.sql` script is idempotent: it can be run several times without errors because it creates the database only if it does not exist (`IF NOT EXISTS`) and drops each table before recreating it (`DROP TABLE IF EXISTS`).

Catalog pricing policy (only two values):

- 8,500.00: t-shirts in light tones (Light Gray, Graphite and Salmon).
- 12,500.00: colored t-shirts (the most expensive products).

Rule met: a light-toned t-shirt costs less than a colored one; the catalog handles only those two prices.

Common catalog feature: all t-shirts are printed and made of 100% cotton; the descriptions reflect it.

> **Note**: the `inventory_summer` and `inventory_winter` databases are intentionally not created. The `error_example.php` file tries to connect to one of them (depending on the season), fails because they do not exist, and demonstrates error handling.

---

## 7. File-by-file description

The data access follows the object-oriented style of the mysqli extension: a `mysqli` instance with `mysqli_report()` (failures are thrown as exceptions), queries with `query()` or prepared statements, and row reading with `fetch_assoc()`.

### config.php

Separate configuration file with the four credentials (`$host`, `$user`, `$password`, `$database`). It contains no logic: its only purpose is to keep the connection information out of the code. It is loaded from `connection.php` with `require`.

Each credential is read with `getenv()` from the system **environment variables**; if the variable does not exist, a **safe example value** is applied as a fallback:

```php
$host      = getenv('DB_HOST')        ?: 'localhost';
$database  = getenv('DB_NAME')        ?: 'Store';

$user = getenv('DB_USER');
if ($user === false) {
    $user = 'user';
}

$password = getenv('DB_PASSWORD');
if ($password === false) {
    $password = 'password';
}
```

Important detail: **user and password do not use the `?:` operator** because in LAMPP the default password is empty (`""`). The `?:` operator treats an empty string as falsy and would apply the fallback (`password`), breaking the connection. `getenv()` is compared against `false` (what it returns when the variable is not defined), correctly distinguishing "not defined" from "defined but empty".

This way the repository does not contain real credentials (the example values expose no data), and in production the credentials can be injected externally by defining the `DB_HOST`, `DB_USER`, `DB_PASSWORD` and `DB_NAME` variables, without modifying this file.

### connection.php

Loads the credentials with `require 'config.php'` and creates the connection with `new mysqli(host, user, password, database)` inside a `try`. Enables `mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)` so any failure is thrown as an exception. In the `catch (mysqli_sql_exception)` it logs the detail with `error_log()` and shows a friendly message to the user.

### products.php

Executes `SELECT * FROM Products` with `$connection->query()` inside a `try`; if the query fails, the exception is caught and logged with `error_log()`. Iterates the result with the standard pattern: `$result->fetch_assoc()` inside a `while` that stops when it returns `null`, accumulating each row into `$products`. Closes with `$connection->close()`.

### index.php

Presentation. Includes `products.php` to obtain `$products` and draws a Bootstrap card (`col-12 col-md-4`) per product inside a `foreach`. Each card contains image (`card-img-top img-producto`), name, detail, internal code and price formatted with `number_format(value, 2)` plus the colones symbol. At the end of the body it includes the modal HTML `#imageModal` (hidden) and loads the Bootstrap bundle + `js/script.js`.

Its first instruction is `session_start()`, before any output. It reads `$_SESSION['cart']` with `isset()` and `array_count_values()` counts how many times each code appears for the "In your cart (xN)" badge on already-added cards; `array_sum()` gets the total item count for the "Cart (N)" counter in the bar. It includes the link to `logout.php`.

### add_to_cart.php

Form processing page. Validates `isset($_POST['code'])`, forces an integer with `(int)` (malicious data would become 0 and be rejected) and verifies in the database that the product exists using a **prepared statement** (`prepare()` + `bind_param("i", $code)` + `execute()`). This way the value travels separately from the SQL statement and cannot inject code. This pattern is mandatory when a query receives user-provided data. The operation runs inside a `try-catch` that logs any error with `error_log()`. Only then the code is added to the array and stored in `$_SESSION['cart']`. When done, it stores a **flash message** in the session and automatically redirects to `index.php` with `header("Location: ...")` (Post/Redirect/Get pattern). The redirect includes an **anchor** (`#product-CODE`) that positions the gallery at the card of the just-added product, so the page does not scroll to the top and the alert shows only once in that place.

### cart_functions.php

"Helper" file with reusable cart functions, so that `cart.php` and `checkout.php` do not duplicate the same logic. It contains a single public function:

- `buildGroupedCartItems($connection, $cart)`: receives the open connection and the code array from `$_SESSION['cart']`, and returns an associative array with three keys: `items` (queried products), `total` (sum of subtotals) and `error` (message if the query fails).

How it groups: `array_count_values($cart)` counts how many times each code appears, e.g. `[1, 4, 4]` becomes `[1 => 1, 4 => 2]`. Then it iterates each **distinct** code with a `foreach` and, using the same **prepared statement** pattern as the other pages, queries the product **only once**. To each row it adds two computed keys:

- `quantity`: the units of the product in the cart (previously repeated in multiple rows).
- `subtotal`: `price * quantity`, and accumulates that subtotal into the `total`.

Thus, if the user adds the same product 3 times, **one row** with quantity 3 and its subtotal is shown instead of 3 identical rows. The `try-catch` block logs errors with `error_log()` and returns the message in the `error` key.

### cart.php

Loads `cart_functions.php` with `require` and calls `buildGroupedCartItems($connection, $cart)` to rebuild the items by querying the database through the helper (which handles the `try-catch` and logs errors with `error_log()`). Renders the table with thumbnails, the **Quantity** and **Subtotal** columns per product, and the total in the `tfoot`. If there are no items it shows a notice. The "Checkout" (POST to `checkout.php`) and "Empty cart" (POST to `clear_cart.php`) buttons close the view.

### checkout.php

Loads `cart_functions.php` with `require` and calls `buildGroupedCartItems($connection, $cart)` to prepare the same grouped summary (quantity and subtotal per product). Renders the purchase detail in a table with the total amount and, on confirmation, empties the cart with `unset($_SESSION['cart'])` (it does not destroy the session). Error handling stays inside the helper, which logs with `error_log()` and returns the error message to display to the user.

### inquiry.php

Renders the customer inquiry form with the fields name, phone (optional), email and detail. Includes HTML5 validation (`required` and `type="email"`). It processes no data; it sends it via POST to `save_inquiry.php`.

### save_inquiry.php

Receiver page of the inquiry form. Validates that name, email and detail arrived (with `isset()` and `trim()`), checks the email format with `filter_var($email, FILTER_VALIDATE_EMAIL)` and stores the data in the `Inquiries` table using a **prepared statement** (`prepare()` + `bind_param("ssss", ...)`). The insertion runs inside a `try-catch` that logs the error with `error_log()`. It shows a success or error message depending on the case.

### error_example.php

Educational error handling page. Reads the `season` parameter via GET ("summer" or "winter", with "winter" as default) and selects the corresponding database (`inventory_summer` or `inventory_winter`), both non-existent. Loads the credentials from `config.php` (does not repeat them in the code) and, with `mysqli_report()` and a `try-catch (mysqli_sql_exception)`, catches the connection error, logs it with `error_log()` (customized with the season) and shows a friendly message; the `finally` block always runs to mark the end of processing.

### clear_cart.php

Deletes only one key: `unset($_SESSION['cart'])`. The session itself stays alive because only the cart is discarded. Automatically redirects to `cart.php` with `header("Location: ...")` (Post/Redirect/Get pattern); the "Your cart is empty" notice is generated by the cart page itself when it checks that no items remain.

### logout.php

Ends the complete session: `session_name()` gets the cookie name, `session_get_cookie_params()` its attributes, `setcookie()` with date 1 and the same path forces its removal in the browser, and `session_destroy()` deletes the data file on the server.

### css/style.css

Complements Bootstrap (does not duplicate it). It defines:

- `.img-producto`: fixed 260px height, centered crop with `object-fit` and pointer cursor.
- `.card:hover`: lift effect with animated shadow.
- `html { scroll-behavior: smooth }`: animates the "back to top" button scrolling.
- `.btn-volver-arriba`: circular shape of the floating button (the fixed position comes from Bootstrap utilities).

### js/script.js

Modal interactivity:

1. `querySelectorAll('.img-producto')` gets all the gallery photos.
2. A `click` event is added to each one.
3. On click it copies `src` and `alt` from the photo to the modal, places the product name as title and shows the window with `new bootstrap.Modal(...).show()`.
4. It closes through three native Bootstrap ways: X button (`.btn-close`), Esc key and outside-click.

---

## 8. Requirements

- LAMPP/XAMPP installed at `/opt/lampp`.
- Apache and MariaDB running.

## 9. Installation and execution

### Step 0: Get the code

```bash
# Clones the repository into the desired folder
git clone https://github.com/oramirez13/tienda_virtual.git tienda_virtual

# Access the project folder
cd tienda_virtual
```

> The database credentials are not part of the code: `config.php` reads them from the **environment variables** with `getenv()` and uses safe example values as a fallback (see the [Database credentials](#10-database-credentials-lampp-default) section). This way the project runs on any machine without configuration, and the real credentials can be defined with `export DB_HOST=... DB_USER=... DB_PASSWORD=... DB_NAME=...` without modifying repository files.

### Step 1: Start the services

```bash
# Starts Apache and MariaDB
sudo /opt/lampp/lampp start

# Verification that both services are active
sudo /opt/lampp/lampp status
```

### Step 2: Create the database

Option A (recommended): import the included script. The `--default-character-set` parameter preserves the accents in the content:

```bash
/opt/lampp/bin/mysql -u root --default-character-set=utf8mb4 < store.sql
```

Option B (manual): review the script content command by command.

```bash
/opt/lampp/bin/mysql -u root
```

```sql
USE Store;
DESCRIBE Products;       -- Shows the 5 fields and their types
DESCRIBE Inquiries;      -- Shows the 6 fields of the inquiries table
SELECT * FROM Products;  -- Should show the 15 catalog t-shirts
```

> **Note on credentials:** the connection data (host, user, password and database) live in `config.php`, which reads them from the environment with `getenv()`. If LAMPP uses a different root password, define the corresponding environment variables (`DB_USER`, `DB_PASSWORD`) before starting Apache, or adjust the fallbacks in `config.php` itself.

### Step 3: Publish the project in LAMPP

LAMPP's web root is `/opt/lampp/htdocs`. The project is published with a symbolic link (adjust the path to where it was cloned):

```bash
sudo ln -s $PWD /opt/lampp/htdocs/tienda_virtual
```

Alternative (direct copy):

```bash
sudo cp -r . /opt/lampp/htdocs/tienda_virtual/
```

### Step 4: Test in the browser

Browser access: <http://localhost/tienda_virtual/>

Visual checklist:

- [ ] The 15 t-shirts are shown (codes 1 to 15).
- [ ] Each card shows image, name, detail, code and price.
- [ ] There are only 2 prices: 8,500 (light tones) and 12,500 (colored).
- [ ] All images load (no broken icons).
- [ ] The grid is responsive: 3 cards per row on desktop, 1 per row on mobile.
- [ ] Clicking an image opens it enlarged in a modal window.
- [ ] Pressing "Add to cart" shows the confirmation and the counter goes up.
- [ ] The added card shows the "In your cart (xN)" badge.
- [ ] "Cart" lists all items with thumbnail, unit quantity and subtotal per product, and the summed total.
- [ ] If the same product is added several times, the cart shows it grouped in a single row with its quantity (no repeated rows).
- [ ] "Empty cart" clears the listing; the cart is left empty.
- [ ] "Log out" shows the "The session has ended" notice with the "Sign in" button and leaves the cart empty.

Additional features:

- [ ] "Inquiries" opens the form; submitting it stores the data in the `Inquiries` table.
- [ ] In the inquiry form, an invalid email is rejected.
- [ ] "Checkout" shows the item summary and the total amount.
- [ ] After checking out, the cart is empty.

### Step 5: Controlled failure test

The site must show a clear message if the database does not respond:

```bash
sudo /opt/lampp/lampp stopmysql
# Reload the page: a readable connection error message must appear,
# never a blank screen or exposed credentials
sudo /opt/lampp/lampp startmysql
```

### Error handling example (summer and winter inventories)

The `error_example.php` file demonstrates error handling by querying a database that does not exist. It is accessed from the "Summer" and "Winter" buttons in the menu, which send the `season` parameter via GET:

- `error_example.php?season=summer` tries to connect to `inventory_summer`.
- `error_example.php?season=winter` tries to connect to `inventory_winter`.

In both cases:

- The connection to the non-existent database fails.
- The error is logged to the Apache log with `error_log()`.
- A friendly message is shown to the user, without technical detail.
- The `finally` block marks that processing finished.

To view it: <http://localhost/tienda_virtual/error_example.php?season=summer>

To check the log entry:

```bash
sudo tail -f /opt/lampp/logs/php_error_log
# Open a button (Summer or Winter) and observe the logged error line
```

### Step 6: Customize the catalog

The products live in `store.sql`. To change them:

1. Edit the `INSERT` statements in `store.sql` (or add new rows).
2. Re-import to rebuild everything from scratch:
   ```bash
   /opt/lampp/bin/mysql -u root --default-character-set=utf8mb4 < store.sql
   ```
3. Reload the browser; the gallery always reflects what is in the database.

To reset the database to its original state, repeat the same import command: the script drops and recreates the table every time.

---

## 10. Database credentials (LAMPP default)

The credentials are configured in `config.php`, which reads them from the system **environment** with `getenv()`. The default LAMPP/XAMPP values the user usually connects with are:

| Parameter | Value     | Environment variable |
| --------- | --------- | -------------------- |
| Host      | localhost | `DB_HOST`            |
| User      | root      | `DB_USER`            |
| Password  | (empty)   | `DB_PASSWORD`        |
| Database  | Store     | `DB_NAME`            |

### How to define the environment variables locally

The recommended way for this project is a **`.htaccess`** file in the root (allowed because LAMPP has `AllowOverride All` in htdocs). It holds the variables with the `SetEnv` directive and is **not versioned** (it is in `.gitignore`), because that is where the real credentials of each machine live:

```
# Content of the project .htaccess file (not uploaded to git)
SetEnv DB_HOST "localhost"
SetEnv DB_USER "root"
SetEnv DB_PASSWORD ""
SetEnv DB_NAME "Store"
```

Apache reads the `.htaccess` on every request, so **it is not necessary to restart the server** when creating or modifying it.

Equivalent terminal alternative (useful for environments without Apache or CLI tests):

```bash
export DB_HOST="localhost"
export DB_USER="root"
export DB_PASSWORD=""
export DB_NAME="Store"
```

`config.php` keeps its safe example values as a fallback —which are not real credentials (user `user`, password `password`)—, so the repository exposes no sensitive information, and the real connection is resolved only with the server environment variables, without editing any project file.

---

## 11. Security practices

| Measure                                                      | Where                                                           | Risk mitigated                                            |
| ------------------------------------------------------------ | --------------------------------------------------------------- | --------------------------------------------------------- |
| `htmlspecialchars()` on all printed data                     | index.php                                                       | XSS (HTML/JS injection from DB data)                      |
| Credentials outside the code (getenv + example values)       | config.php                                                      | Credentials exposed in the repository or source code      |
| Prepared statements (prepare + bind_param)                   | add_to_cart.php, cart_functions.php, save_inquiry.php           | SQL injection in queries with user/session data           |
| Error handling with try-catch + error_log                    | connection/products/add/cart/checkout/save                      | Silent errors, technical information leakage              |
| Email format validation                                      | save_inquiry.php                                                | Wrong data in the database                                |
| Explicit connection closing                                  | products.php, etc.                                               | Server resource exhaustion                                |
| Cast `(int)` of the code received via POST                   | add_to_cart.php                                                 | SQL injection / malicious data in the session             |
| Defensive `isset()` before reading `$_SESSION`               | index/add_to_cart/cart                                          | Accidents from missing keys                               |
| Clear error messages without technical details               | all                                                             | Sensitive information exposure                            |

Other applied practices:

- Actions that modify data (add, empty, checkout, save inquiry) are sent via POST.
- The connection is closed with `$connection->close()` after use.
- The data access style is object-oriented with the mysqli extension.

---

## 12. Screenshots

**Main page of the UNIX t-shirts virtual store.**

![galeria_01](screenshots/galeria_01.png)

**Shows the same main page, but with 5 products added to the shopping cart.**

![galeria_02](screenshots/galeria_02.png)

**Shows the notice presented to the user when logging out, with a button to sign in again.**

![sesion_finalizada_01](screenshots/sesion_finalizada_01.png)

**Shows the modal with the enlarged image of a product.**

![galeria_03](screenshots/galeria_03.png)

**Shows in phpMyAdmin the SELECT query on the Products table.**

![base_de_datos_01](screenshots/base_de_datos_01.png)

**Shows in phpMyAdmin the rows (products) of the Products table.**

![base_de_datos_02](screenshots/base_de_datos_02.png)

**Screenshot of the inquiry form, which collects the customer's name, phone, email and inquiry detail.**

![consulta_01](screenshots/consulta_01.png)

**Screenshot of the shopping cart content summary, detailing the items to be paid.**

![finalizar_compra_01](screenshots/finalizar_compra_01.png)

**Screenshot of the checkout process, showing the total amount.**

![finalizar_compra_02](screenshots/finalizar_compra_02.png)

**Screenshot of the error handling example, with the message shown to the user when an inventory query fails.**

![manejo_de_errores_01](screenshots/manejo_de_errores_01.png)

**Screenshot of the PHP error log, viewed with the `tail -f` command.**

![php_error_log_01](screenshots/php_error_log_01.png)