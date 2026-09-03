# DOCUMENTACIÓN TÉCNICA - Tienda Virtual de Camisetas UNIX

Módulo: Almacenamiento de datos

---

## 1. Descripción general

Aplicación web que muestra un catálogo de 15 camisetas almacenadas en una base
de datos MySQL/MariaDB y un carrito de compras gestionado con sesiones de PHP.
El usuario visualiza los productos en una galería de tarjetas, puede ampliar
cualquier imagen con un clic (modal) y agregar productos al carrito, cuyo
contenido vive en `$_SESSION`.

| Capa      | Tecnología                        |
|-----------|-----------------------------------|
| Servidor web   | Apache (incluido en LAMPP)   |
| Lenguaje       | PHP 8 (extensión mysqli)     |
| Base de datos  | MariaDB (incluida en LAMPP)  |
| Frontend       | HTML5 + Bootstrap 5.3.8 + CSS propio |
| Interactividad | JavaScript vanilla + Modal de Bootstrap |

---

## 2. Flujo de datos de la aplicación

```
Navegador del usuario
        |
        | petición HTTP GET http://localhost/tienda_virtual/
        v
index.php  (presentación)
        |
        | include 'productos.php'
        v
productos.php  (lógica de consulta)
        |
        | include 'conexion.php'  ->  conexion.php hace require 'config.php'
        v
config.php  (credenciales usuario)  y  conexion.php  (abre la conexión mysqli)
        |
        | new mysqli()
        v
MariaDB  ->  base de datos "Tienda"  ->  tabla "Productos"
        |
        | resultado: arreglo $productos (15 filas)
        v
index.php recorre $productos con foreach y genera las tarjetas HTML
        |
        v
Navegador renderiza la galería; script.js activa el modal al hacer clic
```

Flujo del carrito (sesiones de PHP):

```
Galería (index.php) --POST codigo--> agregar.php
    |  valida (int) el código y consulta la BD
    v
$_SESSION['carrito']  (arreglo de códigos, ej. [1, 4, 4])
    |  -> carrito.php consulta la BD por cada código y suma el total
    |  -> vaciar.php   unset($_SESSION['carrito'])  (solo el carrito)
    v
cerrar.php  setcookie(expira) + session_destroy()  (sesión completa)
```

Separación de responsabilidades:
- config.php: SOLO las credenciales de la base de datos (host, usuario, contraseña, BD).
- conexion.php: SOLO abre (y valida) la conexión.
- productos.php: SOLO consulta y organiza los datos en el arreglo $productos.
- index.php: SOLO presentación (HTML). No conoce credenciales ni SQL.

---

## 3. Base de datos

Base: **Tienda** | Tablas: **Productos** y **Consultas**

### Tabla Productos

| Campo   | Tipo         | Restricción | Uso                                  |
|---------|--------------|-------------|--------------------------------------|
| codigo  | INT          | PRIMARY KEY | Identificador único del producto     |
| nombre  | VARCHAR(100) |             | Nombre comercial                     |
| detalle | TEXT         |             | Descripción larga                    |
| imagen  | VARCHAR(255) |             | URL de la foto (local o externa)     |
| precio  | DOUBLE       |             | Precio con decimales                 |

### Tabla Consultas

| Campo    | Tipo          | Restricción                  | Uso                                   |
|----------|---------------|------------------------------|---------------------------------------|
| id       | INT           | AUTO_INCREMENT, PRIMARY KEY  | Identificador de cada consulta        |
| nombre   | VARCHAR(100)  | NOT NULL                     | Nombre del cliente que consulta       |
| telefono | VARCHAR(20)   |                              | Teléfono de contacto (opcional)       |
| email    | VARCHAR(100)  | NOT NULL                     | Correo electrónico del cliente        |
| detalle  | TEXT          | NOT NULL                     | Descripción del asunto de la consulta |
| fecha    | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP    | Fecha y hora de registro (automática) |

El script `tienda.sql` es idempotente: puede ejecutarse varias veces sin error,
porque crea la BD solo si no existe (IF NOT EXISTS) y borra cada tabla antes de
crearla (DROP TABLE IF EXISTS).

Política de precios del catálogo (solo 2 valores):
- ₡8,500.00: camisetas de tonos claros o blancos (Gris Claro, Grafito y Salmón).
- ₡12,500.00: camisetas de color (los productos más caros).

Regla que cumple: una camiseta blanca o de tono claro cuesta menos que una
de color; el catálogo maneja únicamente esos dos precios.

Característica común del catálogo: todas las camisetas son estampadas y de
tela 100% algodón; las descripciones lo reflejan.

---

## 4. Descripción archivo por archivo

El acceso a datos sigue el estilo orientado a objetos de la extensión mysqli:
instancia de `mysqli`, validación
con `connect_error`, consultas con `query()` y lectura con `fetch_assoc()`.

### config.php
Archivo de configuración separado con las 4 credenciales ($host, $usuario,
$contrasena, $basedatos). No contiene lógica: su único propósito es que la
información de conexión no quede mezclada con el código. Se carga desde
conexion.php con `require`.

### conexion.php
Carga las credenciales con `require 'config.php'` y crea la conexión con
`new mysqli(host, usuario, contrasena, BD)` dentro de un `try`. Habilita
`mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)` para que cualquier
fallo se lance como excepción. En el `catch (mysqli_sql_exception)` registra el
detalle con `error_log()` y muestra un mensaje amigable al usuario.

### productos.php
Ejecuta `SELECT * FROM Productos` con `$conexion->query()` dentro de un `try`;
si la consulta falla se captura la excepción y se registra con `error_log()`.
Recorre el resultado con el patrón estándar: `$resultado->fetch_assoc()` dentro
de un `while` que termina cuando retorna null, acumulando cada fila en
`$productos`. Cierra con `$conexion->close()`.

### index.php
Presentación. Incluye `productos.php` para obtener `$productos` y dibuja una
tarjeta Bootstrap (`col-12 col-md-4`) por producto dentro de un `foreach`.
Cada tarjeta contiene: imagen (`card-img-top img-producto`), nombre, detalle,
código interno y precio formateado con `number_format(valor, 2)` más el símbolo
de colones. Al final del body incluye el HTML del modal `#modalImagen`
(oculto) y carga Bootstrap bundle + js/script.js.

### index.php (sesiones)
`session_start()` como primera instrucción, antes de cualquier salida.
Lee `$_SESSION['carrito']` con `isset()` y `array_count_values()` cuenta cuántas
veces aparece cada código para la insignia "En tu carrito (xN)" en las tarjetas
ya agregadas; `array_sum()` obtiene el total de ítems para el contador
"Carrito (N)" de la barra. Incluye el enlace a `cerrar.php`.

### agregar.php
Página receptora del formulario. Valida `isset($_POST['codigo'])`, fuerza entero
con `(int)` (un dato malicioso quedaría en 0 y se rechaza) y verifica en la BD que
el producto exista usando un **prepared statement** (`prepare()` + `bind_param("i",
$codigo)` + `execute()`). Así, el valor viaja por separado de la instrucción SQL
y no puede inyectarse código. Este patrón es obligatorio cuando una consulta
recibe datos provenientes del usuario. La operación va dentro de un `try-catch`
que registra cualquier error en `error_log()`. Solo entonces agrega el código al
arreglo y lo guarda en `$_SESSION['carrito']`. Muestra una confirmación con el
nombre agregado y el total acumulado.

### carrito.php
Reconstruye los ítems consultando la BD por cada código guardado con el mismo
patrón de **prepared statement** (los códigos provienen de la sesión) y acumula el
precio en `$total`. Las consultas van dentro de un `try-catch` que registra
cualquier error en `error_log()`. Presenta la tabla con miniaturas, el total en
`tfoot` y los botones "Finalizar compra" (POST a `finalizar_compra.php`) y
"Vaciar carrito" (POST a `vaciar.php`). Si no hay ítems muestra un aviso.

### finalizar_compra.php
Reconstruye los ítems del carrito con el mismo patrón de prepared statement y
muestra un resumen de la compra: tabla con los artículos y el monto total. Al
confirmar, vacía el carrito con `unset($_SESSION['carrito'])` (no destruye la
sesión). Las consultas se envuelven en `try-catch` con registro en `error_log()`.

### consulta.php
Presenta el formulario de consultas del cliente con los campos nombre, teléfono
(opcional), correo y detalle. Incluye validación HTML5 (required y type="email").
No procesa datos; los envía por POST a `guardar_consulta.php`.

### guardar_consulta.php
Página receptora del formulario de consultas. Valida que lleguen nombre, correo
y detalle (con `isset()` y `trim()`), comprueba el formato del correo con
`filter_var($email, FILTER_VALIDATE_EMAIL)` y almacena los datos en la tabla
`Consultas` usando un **prepared statement** (`prepare()` + `bind_param("ssss", ...)`).
La inserción va en un `try-catch` que registra el error en `error_log()`. Muestra
un mensaje de éxito o de error según el caso.

### ejemplo_errores.php
Página didáctica de manejo de errores. Simula que un usuario consulta el
inventario de invierno, pero se conecta a una base de datos inexistente
(`inventario_invierno`). Con `mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)`
y un `try-catch (mysqli_sql_exception)` captura el error, lo registra con
`error_log()` y muestra un mensaje amigable; el bloque `finally` se ejecuta
siempre para indicar el fin del procesamiento.

### vaciar.php
Borra solo una llave: `unset($_SESSION['carrito'])`. La sesión como tal
permanece viva, porque solo se descarta el carrito.

### cerrar.php
Cierra la sesión completa: `session_name()` obtiene el nombre de la cookie,
`session_get_cookie_params()` sus atributos, `setcookie()` con fecha 1 y el mismo
path fuerza su eliminación en el navegador, y `session_destroy()` borra los datos
del archivo en el servidor.

### css/style.css
Complementa Bootstrap (no lo duplica). Solo define:
- `.img-producto`: altura fija de 260px, recorte centrado con object-fit y cursor pointer.
- `.card:hover`: efecto de levantamiento con sombra animada.
- `html { scroll-behavior: smooth }`: anima el desplazamiento del botón "volver arriba".
- `.btn-volver-arriba`: forma circular del botón flotante (la posición fija la dan las utilidades de Bootstrap).

### js/script.js
Interactividad del modal:
1. `querySelectorAll('.img-producto')` obtiene todas las fotos de la galería.
2. A cada una le agrega un evento `click`.
3. Al hacer clic copia `src` y `alt` de la foto hacia el modal, coloca el nombre
   del producto como título y muestra la ventana con
   `new bootstrap.Modal(...).show()`.
4. El cierre funciona por tres vías nativas de Bootstrap: botón X
   (`.btn-close`), tecla Esc y clic fuera del diálogo.

---

## 5. Seguridad aplicada

| Medida | Dónde | Riesgo que mitiga |
|--------|-------|-------------------|
| `htmlspecialchars()` en todo dato impreso | index.php | XSS (inyección de HTML/JS desde datos de la BD) |
| Credenciales en config.php | conexion.php | Credenciales expuestas en el código fuente |
| Prepared statements (prepare + bind_param) | agregar.php, carrito.php, finalizar_compra.php, guardar_consulta.php | Inyección SQL en consultas con datos del usuario/sesión |
| Manejo de errores con try-catch + error_log | conexion/productos/agregar/carrito/finalizar/guardar | Errores silenciosos, fuga de información técnica |
| Validación del formato de correo | guardar_consulta.php | Datos incorrectos en la base de datos |
| Cierre explícito de la conexión | productos.php, etc. | Agotamiento de recursos del servidor |
| Cast `(int)` del código recibido por POST | agregar.php | Inyección SQL / datos maliciosos en la sesión |
| `isset()` defensivo antes de leer `$_SESSION` | index/agregar/carrito | Accidentes por llaves inexistentes |
| Mensajes de error claros, sin detalles técnicos | todos | Exposición de información sensible |

---

## 6. Ejecución rápida

```bash
sudo /opt/lampp/lampp start                                   # iniciar servicios
/opt/lampp/bin/mysql -u root --default-character-set=utf8mb4 < tienda.sql   # crear BD y datos
sudo ln -s <ruta_del_proyecto> /opt/lampp/htdocs/tienda_virtual   # publicar
# abrir http://localhost/tienda_virtual/
```

Detalle completo en README.md y guía de entrega en STEP-BY-STEP.md.
