-- =============================================================
-- Script SQL de la base de datos "Tienda"
-- Motor: MariaDB (incluido en LAMPP)
--
-- Crea la base de datos, la tabla "Productos" con 15 productos de
-- prueba y la tabla "Consultas" para el formulario de consultas.
-- Las rutas de imagen son relativas, por lo que funcionan sin
-- importar la carpeta donde se publique el sitio.
-- =============================================================

-- Crea la base si no existe, para permitir volver a ejecutar el script
CREATE DATABASE IF NOT EXISTS Tienda;

-- Selecciona la base de datos sobre la que se trabajará
USE Tienda;

-- Elimina la tabla si existiera antes de recrearla
DROP TABLE IF EXISTS Productos;

-- Creación de la tabla "Productos"
CREATE TABLE Productos (
    codigo INT PRIMARY KEY,          -- Identificador único de cada producto
    nombre VARCHAR(100),             -- Nombre comercial del producto
    detalle TEXT,                    -- Descripción del producto
    imagen VARCHAR(255),             -- Ruta de la imagen del producto
    precio DOUBLE                    -- Precio del producto (admite decimales)
);

-- Inserción de los 15 productos del catálogo; el campo imagen
-- apunta a las imágenes locales de la carpeta img/
INSERT INTO Productos (codigo, nombre, detalle, imagen, precio) VALUES
(1,  'Camiseta Mostaza',        'Camiseta estampada de algodón 100%, tono mostaza, corte clásico. Tallas S a XXL.',                                      'img/camiseta_01.jpg', 12500),
(2,  'Camiseta Azul Estampada', 'Camiseta estampada 100% algodón en color azul, con diseño exclusivo al frente y tela fresca.',                          'img/camiseta_02.jpg', 12500),
(3,  'Camiseta Negra Clásica',  'Camiseta estampada 100% algodón en color negro, diseño discreto de estilo clásico.',                                    'img/camiseta_03.jpg', 12500),
(4,  'Camiseta Gris Claro',     'Camiseta estampada 100% algodón en tono gris claro, tela ligera ideal para clima cálido.',                              'img/camiseta_04.jpg', 8500),
(5,  'Camiseta Grafito',        'Camiseta estampada 100% algodón en tono grafito, acabado mate y corte moderno.',                                        'img/camiseta_05.jpg', 8500),
(6,  'Camiseta Rosada',         'Camiseta estampada 100% algodón peinado en color rosado, tacto suave y colores estables al lavado.',                    'img/camiseta_06.jpg', 12500),
(7,  'Camiseta Verde Turquesa', 'Camiseta estampada 100% algodón en color verde turquesa, tono vibrante resistente a los lavados.',                      'img/camiseta_07.jpg', 12500),
(8,  'Camiseta Beige Estampada','Camiseta estampada 100% algodón en color beige, diseño artístico de edición limitada.',                                 'img/camiseta_08.jpg', 12500),
(9,  'Camiseta Roja Estampada', 'Camiseta estampada 100% algodón premium en color rojo, diseño al frente con doble costura.',                            'img/camiseta_09.jpg', 12500),
(10, 'Camiseta Salmón',         'Camiseta estampada 100% algodón en tono salmón, color suave que combina con todo, corte unisex.',                       'img/camiseta_10.jpg', 8500),
(11, 'Camiseta Roja Vintage',   'Camiseta estampada 100% algodón en color rojo, con diseño de estilo vintage y acabado desgastado, edición especial.',   'img/camiseta_11.jpg', 12500),
(12, 'Camiseta Durazno',        'Camiseta estampada 100% algodón en tono durazno, tela fresca y ligera para el día a día.',                              'img/camiseta_12.jpg', 12500),
(13, 'Camiseta Azul Marino',    'Camiseta estampada 100% algodón en color azul marino oscuro, diseño clásico y versátil para cualquier ocasión.',        'img/camiseta_13.jpg', 12500),
(14, 'Camiseta Café',           'Camiseta estampada 100% algodón grueso en color café chocolate, alta durabilidad.',                                     'img/camiseta_14.jpg', 12500),
(15, 'Camiseta Azul Petróleo',  'Camiseta estampada 100% algodón en tono azul petróleo profundo, diseño elegante de moda urbana.',                       'img/camiseta_15.jpg', 12500);

-- Consulta opcional para comprobar los datos insertados
SELECT * FROM Productos;

-- =============================================================
-- Tabla "Consultas": almacena los mensajes enviados por los
-- clientes desde el formulario de consultas.
-- =============================================================

-- Elimina la tabla si existiera antes de recrearla
DROP TABLE IF EXISTS Consultas;

-- Creación de la tabla "Consultas"
CREATE TABLE Consultas (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- Identificador único de cada consulta (autoincremental)
    nombre VARCHAR(100) NOT NULL,       -- Nombre del cliente que consulta
    telefono VARCHAR(20),               -- Teléfono de contacto (opcional)
    email VARCHAR(100) NOT NULL,        -- Correo electrónico del cliente
    detalle TEXT NOT NULL,              -- Descripción del asunto de la consulta
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Fecha y hora de registro (automática)
);

-- =============================================================
-- Nota: las bases de datos "inventario_verano" e "inventario_invierno"
-- NO se crean aquí a propósito. El archivo ejemplo_errores.php intenta
-- conectarse a una de ellas (según la estación), falla porque no
-- existen, y demuestra el manejo de errores.
-- =============================================================