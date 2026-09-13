-- =============================================================
-- SQL script for the "Store" database
-- Engine: MariaDB (included in LAMPP)
--
-- Creates the database, the "Products" table with 15 sample
-- t-shirts and the "Inquiries" table for the inquiry form.
-- The image paths are relative, so they work regardless of
-- where the site is published.
-- =============================================================

-- Creates the database if it does not exist, for easy re-runs
CREATE DATABASE IF NOT EXISTS Store;

-- Selects the database to work on
USE Store;

-- Drops the table if it existed before recreating it
DROP TABLE IF EXISTS Products;

-- Creation of the "Products" table
CREATE TABLE Products (
    code INT PRIMARY KEY,          -- Unique identifier of each product
    name VARCHAR(100),             -- Commercial product name
    detail TEXT,                   -- Product description
    image VARCHAR(255),            -- Product image path
    price DOUBLE                   -- Product price (allows decimals)
);

-- Insertion of the 15 catalog products; each image field
-- points to the local images in the img/ folder
INSERT INTO Products (code, name, detail, image, price) VALUES
(1,  'Mustard T-Shirt',          'Printed 100% cotton t-shirt, mustard tone, classic cut. Sizes S to XXL.',                                                'img/tshirt_01.jpg', 12500),
(2,  'Blue Printed T-Shirt',     'Printed 100% cotton t-shirt in blue, with exclusive front design and fresh fabric.',                                        'img/tshirt_02.jpg', 12500),
(3,  'Classic Black T-Shirt',    'Printed 100% cotton t-shirt in black, discreet design in classic style.',                                                  'img/tshirt_03.jpg', 12500),
(4,  'Light Gray T-Shirt',       'Printed 100% cotton t-shirt in light gray tone, lightweight fabric ideal for warm weather.',                                'img/tshirt_04.jpg', 8500),
(5,  'Graphite T-Shirt',         'Printed 100% cotton t-shirt in graphite tone, matte finish and modern cut.',                                                'img/tshirt_05.jpg', 8500),
(6,  'Pink T-Shirt',             'Printed 100% combed cotton t-shirt in pink, soft touch and colorfast after washing.',                                        'img/tshirt_06.jpg', 12500),
(7,  'Turquoise Green T-Shirt',  'Printed 100% cotton t-shirt in turquoise green, vibrant tone resistant to washing.',                                         'img/tshirt_07.jpg', 12500),
(8,  'Beige Printed T-Shirt',    'Printed 100% cotton t-shirt in beige, artistic limited-edition design.',                                                     'img/tshirt_08.jpg', 12500),
(9,  'Red Printed T-Shirt',      'Printed 100% premium cotton t-shirt in red, front design with double stitching.',                                             'img/tshirt_09.jpg', 12500),
(10, 'Salmon T-Shirt',           'Printed 100% cotton t-shirt in salmon tone, soft color that matches everything, unisex cut.',                                'img/tshirt_10.jpg', 8500),
(11, 'Vintage Red T-Shirt',      'Printed 100% cotton t-shirt in red, vintage style design with worn finish, special edition.',                                 'img/tshirt_11.jpg', 12500),
(12, 'Peach T-Shirt',            'Printed 100% cotton t-shirt in peach tone, fresh and lightweight fabric for everyday use.',                                   'img/tshirt_12.jpg', 12500),
(13, 'Navy Blue T-Shirt',        'Printed 100% cotton t-shirt in dark navy blue, classic and versatile design for any occasion.',                                'img/tshirt_13.jpg', 12500),
(14, 'Coffee T-Shirt',           'Printed 100% cotton t-shirt in chocolate brown, high durability.',                                                           'img/tshirt_14.jpg', 12500),
(15, 'Petroleum Blue T-Shirt',   'Printed 100% cotton t-shirt in deep petroleum blue tone, elegant urban fashion design.',                                       'img/tshirt_15.jpg', 12500);

-- Optional query to verify the inserted data
SELECT * FROM Products;

-- =============================================================
-- "Inquiries" table: stores the messages sent by customers
-- through the inquiry form.
-- =============================================================

-- Drops the table if it existed before recreating it
DROP TABLE IF EXISTS Inquiries;

-- Creation of the "Inquiries" table
CREATE TABLE Inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- Unique identifier of each inquiry (autoincremental)
    name VARCHAR(100) NOT NULL,         -- Name of the customer asking
    phone VARCHAR(20),                  -- Contact phone (optional)
    email VARCHAR(100) NOT NULL,        -- Customer email
    detail TEXT NOT NULL,               -- Description of the inquiry subject
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Registration date and time (automatic)
);

-- =============================================================
-- Note: the "inventory_summer" and "inventory_winter" databases
-- are intentionally NOT created here. The error_example.php file
-- tries to connect to one of them (depending on the season),
-- fails because they do not exist, and demonstrates error handling.
-- =============================================================