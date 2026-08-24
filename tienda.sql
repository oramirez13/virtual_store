-- =============================================================
-- TAREA 1 - Semana 2 - Desarrollo Web PHP Avanzado
-- Script SQL de la base de datos "Tienda"
-- Motor: MariaDB (incluido en LAMPP)
--
-- Crea la base de datos, la tabla "Productos" con los campos
-- exigidos e inserta 15 productos de prueba.
--
-- Política de precios (solo 2 valores):
--   ₡ 8,500.00 -> camisetas de tonos claros: Gris Claro, Grafito y Salmón
--   ₡12,500.00 -> camisetas de color (las más caras del catálogo)
--
-- Nota: todas las camisetas del catálogo son ESTAMPADAS y de
-- tela 100% algodón.
-- =============================================================

-- Crea la base de datos si todavía no existe
CREATE DATABASE IF NOT EXISTS Tienda;

-- Selecciona la base de datos para trabajar sobre ella
USE Tienda;

-- Elimina la tabla si existiera (permite re-ejecutar el script)
DROP TABLE IF EXISTS Productos;

-- Creación de la tabla "Productos" con los campos indicados en la tarea
CREATE TABLE Productos (
    codigo INT PRIMARY KEY,          -- Código único del producto (identificador)
    nombre VARCHAR(100),             -- Nombre comercial del producto
    detalle TEXT,                    -- Descripción larga del producto
    imagen VARCHAR(255),             -- URL de la imagen (local o externa)
    precio DOUBLE                    -- Precio del producto (admite decimales)
);

-- Inserción de los 15 productos de prueba;
-- el campo imagen apunta a las imágenes locales de la carpeta img/
INSERT INTO Productos (codigo, nombre, detalle, imagen, precio) VALUES
(1,  'Camiseta Mostaza',        'Camiseta estampada de algodón 100%, tono mostaza, corte clásico. Tallas S a XXL.',                                      'http://localhost/tienda_virtual/img/camiseta_01.jpg', 12500),
(2,  'Camiseta Azul Estampada', 'Camiseta estampada 100% algodón en color azul, con diseño exclusivo al frente y tela fresca.',                          'http://localhost/tienda_virtual/img/camiseta_02.jpg', 12500),
(3,  'Camiseta Negra Clásica',  'Camiseta estampada 100% algodón en color negro, diseño discreto de estilo clásico.',                                     'http://localhost/tienda_virtual/img/camiseta_03.jpg', 12500),
(4,  'Camiseta Gris Claro',     'Camiseta estampada 100% algodón en tono gris claro, tela ligera ideal para clima cálido.',                               'http://localhost/tienda_virtual/img/camiseta_04.jpg', 8500),
(5,  'Camiseta Grafito',        'Camiseta estampada 100% algodón en tono grafito, acabado mate y corte moderno.',                                         'http://localhost/tienda_virtual/img/camiseta_05.jpg', 8500),
(6,  'Camiseta Rosada',         'Camiseta estampada 100% algodón peinado en color rosado, tacto suave y colores estables al lavado.',                     'http://localhost/tienda_virtual/img/camiseta_06.jpg', 12500),
(7,  'Camiseta Verde Turquesa', 'Camiseta estampada 100% algodón en color verde turquesa, tono vibrante resistente a los lavados.',                       'http://localhost/tienda_virtual/img/camiseta_07.jpg', 12500),
(8,  'Camiseta Beige Estampada','Camiseta estampada 100% algodón en color beige, diseño artístico de edición limitada.',                                  'http://localhost/tienda_virtual/img/camiseta_08.jpg', 12500),
(9,  'Camiseta Roja Estampada', 'Camiseta estampada 100% algodón premium en color rojo, diseño al frente con doble costura.',                             'http://localhost/tienda_virtual/img/camiseta_09.jpg', 12500),
(10, 'Camiseta Salmón',         'Camiseta estampada 100% algodón en tono salmón, color suave que combina con todo, corte unisex.',                        'http://localhost/tienda_virtual/img/camiseta_10.jpg', 8500),
(11, 'Camiseta Roja Vintage',   'Camiseta estampada 100% algodón en color rojo, con diseño de estilo vintage y acabado desgastado, edición especial.',    'http://localhost/tienda_virtual/img/camiseta_11.jpg', 12500),
(12, 'Camiseta Durazno',        'Camiseta estampada 100% algodón en tono durazno, tela fresca y ligera para el día a día.',                                'http://localhost/tienda_virtual/img/camiseta_12.jpg', 12500),
(13, 'Camiseta Azul Marino',    'Camiseta estampada 100% algodón en color azul marino oscuro, diseño clásico y versátil para cualquier ocasión.',         'http://localhost/tienda_virtual/img/camiseta_13.jpg', 12500),
(14, 'Camiseta Café',           'Camiseta estampada 100% algodón grueso en color café chocolate, alta durabilidad.',                                      'http://localhost/tienda_virtual/img/camiseta_14.jpg', 12500),
(15, 'Camiseta Azul Petróleo',  'Camiseta estampada 100% algodón en tono azul petróleo profundo, diseño elegante de moda urbana.',                        'http://localhost/tienda_virtual/img/camiseta_15.jpg', 12500);

-- Verificación rápida de los datos insertados (opcional, para pruebas)
SELECT * FROM Productos;