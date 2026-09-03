# Guía paso a paso: levantar la Tienda Virtual desde cero

Instrucciones para desplegar el proyecto en un equipo Linux con LAMPP.
Proyecto: Tienda Virtual de Camisetas UNIX | Motor: MariaDB | PHP 8+

---

## Paso 0: Obtener el código

```bash
# Clona el repositorio en la carpeta que prefieras
git clone https://github.com/oramirez13/tienda_virtual_camisetas_unix.git tienda_virtual

# Entra a la carpeta del proyecto
cd tienda_virtual
```

## Paso 1: Iniciar los servicios

```bash
# Arranca Apache y MariaDB
sudo /opt/lampp/lampp start

# Verifica que ambos estén corriendo
sudo /opt/lampp/lampp status
```

## Paso 2: Crear la base de datos

Opción A (recomendada): importar el script incluido.
El parámetro --default-character-set conserva las tildes del contenido:

```bash
/opt/lampp/bin/mysql -u root --default-character-set=utf8mb4 < tienda.sql
```

Opción B (manual): revisar el contenido del script comando por comando.

```bash
/opt/lampp/bin/mysql -u root
```

```sql
USE Tienda;
DESCRIBE Productos;       -- Muestra los 5 campos y sus tipos
DESCRIBE Consultas;       -- Muestra los 6 campos de la tabla de consultas
SELECT * FROM Productos;  -- Debe mostrar las 15 camisetas del catálogo
```

> **Nota sobre las credenciales:** los datos de conexión (host, usuario,
> contraseña y base) viven en `config.php`, separados del código de conexión.
> Si tu LAMPP usa una contraseña de root distinta,
> edítala en ese archivo antes de continuar.

## Paso 3: Publicar el proyecto en LAMPP

La raíz web de LAMPP es /opt/lampp/htdocs. Se publica con un enlace
simbólico (ajusta la ruta a donde hayas clonado el proyecto):

```bash
sudo ln -s $PWD /opt/lampp/htdocs/tienda_virtual
```

Alternativa (copia directa):

```bash
sudo cp -r . /opt/lampp/htdocs/tienda_virtual/
```

## Paso 4: Probar en el navegador

Abrir: http://localhost/tienda_virtual/

Lista de verificación visual:
- [ ] Se muestran las 15 camisetas (códigos 1 al 15)
- [ ] Cada tarjeta muestra imagen, nombre, detalle, código y precio
- [ ] Solo existen 2 precios: ₡8,500 (tonos claros) y ₡12,500 (de color)
- [ ] Todas las imágenes cargan (ningún ícono roto)
- [ ] La cuadrícula responde: 3 tarjetas por fila en PC, 1 por fila en móvil
- [ ] Al hacer clic en una imagen se abre ampliada en una ventana modal
- [ ] Al pulsar "Agregar al carrito" sale la confirmación y el contador sube
- [ ] La tarjeta agregada muestra la insignia "En tu carrito (xN)"
- [ ] "Carrito" lista todos los ítems con miniatura y el total sumado
- [ ] "Vaciar carrito" borra el listado; el carrito queda vacío
- [ ] "Cerrar sesión" muestra "Sesión destruida" y el carrito vuelve a estar vacío

### Funcionalidades nuevas (Proyecto Final)
- [ ] "Consultas" abre el formulario; al enviarlo se guarda en la tabla Consultas
- [ ] En el formulario de consultas, un correo inválido se rechaza
- [ ] "Finalizar compra" muestra el resumen de artículos y el monto total
- [ ] Tras finalizar la compra, el carrito queda vacío

## Paso 5: Prueba de fallo controlado

El sitio debe mostrar un mensaje claro si la base de datos no responde:

```bash
sudo /opt/lampp/lampp stopmysql
# Recargar la página: debe verse un mensaje legible de error de conexión,
# nunca una pantalla en blanco ni credenciales expuestas
sudo /opt/lampp/lampp startmysql
```

### Ejemplo de manejo de errores (inventarios de verano e invierno)

El archivo `ejemplo_errores.php` demuestra el manejo de errores consultando una
base de datos que no existe. Se accede desde los botones "Verano" e "Invierno"
del menú, que envían el parámetro `estacion` por GET:

- `ejemplo_errores.php?estacion=verano` intenta conectar a `inventario_verano`.
- `ejemplo_errores.php?estacion=invierno` intenta conectar a `inventario_invierno`.

En ambos casos:
- La conexión a la BD inexistente falla.
- El error se registra en el log de Apache con `error_log()`.
- Se muestra un mensaje amigable al usuario, sin detalle técnico.
- El bloque `finally` indica que el procesamiento terminó.

Para verlo: http://localhost/tienda_virtual/ejemplo_errores.php?estacion=verano

Para comprobar el registro en el log:

```bash
sudo tail -f /opt/lampp/logs/php_error_log
# Abrir un botón (Verano o Invierno) y ver la línea de error registrada
```

## Paso 6: Personalizar el catálogo

Los productos viven en tienda.sql. Para cambiarlos:

1. Edita los INSERT de tienda.sql (o agrega filas nuevas).
2. Reimporta para reconstruir todo desde cero:
   ```bash
   /opt/lampp/bin/mysql -u root --default-character-set=utf8mb4 < tienda.sql
   ```
3. Recarga el navegador; la galería siempre refleja lo que hay en la BD.

Para reiniciar la base de datos a su estado original basta repetir el
mismo comando de importación: el script borra y recrea la tabla cada vez.
