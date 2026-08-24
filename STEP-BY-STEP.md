# STEP-BY-STEP - Tarea 1: Galería de Productos

Guía paso a paso para llevar el proyecto de cero hasta la entrega del ZIP.
Proyecto: tienda_virtual (Tienda Virtual de Camisetas UNIX) | Motor: LAMPP | BD: Tienda

---

## Paso 0: Preparar el entorno LAMPP

```bash
# Iniciar Apache y MariaDB
sudo /opt/lampp/lampp start

# Verificar que están corriendo
sudo /opt/lampp/lampp status
```

## Paso 1: Crear la base de datos y la tabla

Opción A (recomendada): importar el script ya preparado
(--default-character-set conserva las tildes del contenido):

```bash
/opt/lampp/bin/mysql -u root --default-character-set=utf8mb4 < /opt/lampp/htdocs/tienda_virtual/tienda.sql
```

Opción B (manual, comando por comando):

```bash
/opt/lampp/bin/mysql -u root
```

```sql
CREATE DATABASE Tienda;
USE Tienda;
CREATE TABLE Productos (
    codigo INT PRIMARY KEY,
    nombre VARCHAR(100),
    detalle TEXT,
    imagen VARCHAR(255),
    precio DOUBLE
);
```

Verificar que la tabla quedó bien:

```sql
USE Tienda;
DESCRIBE Productos;   -- Muestra los 5 campos y sus tipos
SELECT * FROM Productos;  -- Debe mostrar 15 filas tras importar
```

## Paso 2: Publicar el proyecto en LAMPP

El proyecto vive en la carpeta de la universidad; se publica con un enlace
simbólico hacia la raíz web de LAMPP:

```bash
sudo ln -s /home/orami/u_fidelitas/desarrollo_web/php_avanzado/semana_2/tienda_virtual /opt/lampp/htdocs/tienda_virtual
```

Alternativa (copia directa):

```bash
sudo cp -r /home/orami/u_fidelitas/desarrollo_web/php_avanzado/semana_2/tienda_virtual /opt/lampp/htdocs/
```

## Paso 3: Probar en el navegador

Abrir: http://localhost/tienda_virtual/

Lista de verificación visual:
- [ ] Se muestran las 15 camisetas (códigos 1 al 15; la tarea pide mínimo 10)
- [ ] Cada tarjeta muestra imagen, nombre, detalle, código y precio
- [ ] Solo existen 2 precios: ₡8,500 (tonos claros) y ₡12,500 (de color)
- [ ] Todas las imágenes cargan (ningún ícono roto)
- [ ] La cuadrícula responde: 3 tarjetas por fila en PC, 1 por fila en móvil
- [ ] Al pasar el mouse la tarjeta se levanta (efecto hover)

## Paso 4: Prueba de fallo controlado

Verificar que el sitio no se rompe si la base de datos no responde:

```bash
sudo /opt/lampp/lampp stopmysql
# Recargar la página: debe verse un mensaje claro de error de conexión
sudo /opt/lampp/lampp startmysql
```

## Paso 5: Armar el ZIP de entrega

El PDF exige exactamente estos 3 artefactos:

| # | Artefacto exigido                    | Archivo(s) del proyecto        |
|---|--------------------------------------|--------------------------------|
| 1 | Código de creación de la tabla       | tienda.sql                     |
| 2 | PHP de conexión y consulta           | conexion.php + productos.php   |
| 3 | HTML/PHP de visualización            | index.php                      |

```bash
# Crear una carpeta temporal con solo los entregables
mkdir -p /tmp/opencode/Tarea1_Orami
cp tienda.sql conexion.php productos.php index.php /tmp/opencode/Tarea1_Orami/
cd /tmp/opencode && zip -r Tarea1_Orami.zip Tarea1_Orami
```

Nota: img/ no va en el ZIP porque las imágenes se referencian por URL
y el PDF solo exige los 3 códigos.

## Paso 6: Revisión final antes de entregar

- [ ] tienda.sql recrea todo desde cero (BD + tabla + 15 inserciones)
- [ ] Los archivos PHP no exponen credenciales en mensajes de error
- [ ] htmlspecialchars() aplicado a todos los datos impresos
- [ ] El ZIP contiene los 3 artefactos correctos
- [ ] Probar el ZIP en otra máquina/carpeta limpia si es posible
