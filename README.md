# Tienda Virtual de Camisetas UNIX

Galería web de productos que muestra camisetas almacenadas en una base de datos MySQL/MariaDB, desarrollada con PHP y Bootstrap.

## Descripción

Cada producto está conformado por: **código**, **nombre**, **detalle**, **imagen** (URL) y **precio**. El sitio consulta los productos desde la tabla `Productos` de la base `Tienda` y los presenta al usuario en una cuadrícula de tarjetas.

## Tecnologías

- PHP 8 (extensión mysqli)
- MySQL/MariaDB (servidor LAMPP)
- HTML5 + Bootstrap 5.3.8 (plantilla base skeletor.html)
- JavaScript (modal de imagen ampliada)

## Estructura del proyecto

```
tienda_virtual/
├── conexion.php        # Conexión a la base de datos MySQL/MariaDB
├── productos.php       # Lógica de consulta: obtiene los productos en el arreglo $productos
├── index.php           # Galería de productos (HTML/PHP) + modal de imagen ampliada
├── tienda.sql          # Script SQL: creación de tabla + 15 productos de prueba
├── README.md           # Este archivo
├── DOCUMENTACION.md    # Documentación técnica (arquitectura, flujo de datos, seguridad)
├── STEP-BY-STEP.md     # Guía paso a paso de instalación y entrega
├── css/
│   └── style.css       # Estilos propios complementarios a Bootstrap
├── js/
│   └── script.js       # JavaScript: apertura del modal al hacer clic en una imagen
├── img/                # Imágenes locales de los productos
│   ├── camiseta_01.jpg
│   ├── ...
│   └── camiseta_15.jpg
└── screenshots/        # Capturas de pantalla del sitio y de phpMyAdmin
    ├── galeria_01.png
    ├── galeria_02.png
    ├── galeria_03.png
    └── galeria_04.png
```

## Requisitos previos

- LAMPP/XAMPP instalado en `/opt/lampp`
- Apache y MariaDB funcionando

## Instalación y ejecución

```bash
# 1. Iniciar los servicios de LAMPP
sudo /opt/lampp/lampp start

# 2. Importar el script de base de datos (crea BD Tienda, tabla Productos y datos;
#    --default-character-set conserva las tildes del contenido)
/opt/lampp/bin/mysql -u root --default-character-set=utf8mb4 < tienda.sql

# 3. Publicar el proyecto en la raíz web de LAMPP (enlace simbólico)
sudo ln -s $PWD /opt/lampp/htdocs/tienda_virtual
```

Abrir en el navegador: <http://localhost/tienda_virtual/>

## Credenciales de base de datos (LAMPP por defecto)

| Parámetro | Valor    |
|-----------|----------|
| Host      | localhost|
| Usuario   | root     |
| Contraseña | (vacía)  |
| Base      | Tienda   |

## Funcionalidades

- Galería responsiva de 15 camisetas leídas desde MariaDB.
- Clic sobre cualquier imagen: modal de Bootstrap con la foto ampliada, nombre del producto como título y botón X de cerrado (también cierra con Esc o clic fuera).
- Botón flotante "volver arriba" con desplazamiento suave (esquina inferior derecha).
- Formato monetario del precio en colones.

## Capturas de pantalla

**Tienda Virtual funcionando en el navegador:**

![Tienda Virtual en el navegador](screenshots/galeria_01.png)

**Producto seleccionado con su imagen ampliada (modal):**

![Imagen ampliada de un producto](screenshots/galeria_02.png)

**phpMyAdmin mostrando la lista de productos de la tabla `Productos`:**

![phpMyAdmin - lista de productos](screenshots/galeria_03.png)

**phpMyAdmin mostrando el resultado de la consulta `SELECT * FROM Productos`:**

![phpMyAdmin - SELECT * FROM Productos](screenshots/galeria_04.png)

## Notas de seguridad aplicadas

- `htmlspecialchars()` al imprimir datos de la BD (prevención de XSS).
- Verificación de errores de conexión (`connect_error`) y de consulta (`error`).
- La conexión se cierra con `$conexion->close()` al terminar su uso.
- Estilo orientado a objetos mysqli.
