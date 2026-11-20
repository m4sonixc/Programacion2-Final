# 🔧 Sistema de Gestión Web - Taller de Pachi

Guía completa de funcionamiento y defensa del Trabajo Práctico.

**Tecnologías utilizadas:**
- **Backend:** PHP 8+ con PDO y consultas preparadas.
- **Base de Datos:** MySQL (tablas relacionales con claves primarias y foráneas).
- **Frontend:** HTML5, Bootstrap 5 (por CDN) y JavaScript puro.
- **Servidor local:** XAMPP (Apache + MySQL).

---

## Parte 1: Funcionamiento del Sistema (Paso a Paso)

### Paso 1: Idea de negocio y objetivo del sistema
El sistema está pensado para administrar las ventas de repuestos e insumos del **Taller de Pachi** (aceites, filtros, frenos, etc.). Permite llevar el control de:
- Los **productos** que se venden y su **stock**, organizados por **categorías**.
- Los **clientes** que compran en el taller.
- Los **usuarios** (vendedores/administradores) que tienen acceso al sistema.
- Las **ventas** realizadas, guardando qué cliente compró, quién lo atendió, la fecha, el detalle de cada producto vendido y el total.

---

### Paso 2: Cómo está organizada la Base de Datos (`taller_pachi`)
La base de datos tiene **6 tablas** relacionadas entre sí:

1. **`usuarios`**: guarda los datos de acceso al sistema (`id_usuario`, `nombre`, `usuario`, `password`). El campo `usuario` es único (`UNIQUE`) para que no se repitan nombres de usuario.
2. **`categorias`**: agrupa los productos del taller (`id_categoria`, `nombre`, `descripcion`).
3. **`productos`**: guarda cada repuesto o insumo (`id_producto`, `nombre`, `descripcion`, `precio`, `stock`, `id_categoria`).
   - **Relación:** tiene la clave foránea `id_categoria` que apunta a `categorias(id_categoria)`. Cada producto pertenece a una categoría.
4. **`clientes`**: guarda los datos de los compradores (`id_cliente`, `nombre`, `apellido`, `telefono`, `email`, `direccion`).
5. **`ventas` (Cabecera)**: registra los datos generales de cada venta (`id_venta`, `id_cliente`, `id_usuario`, `fecha`, `total`).
   - **Relaciones:** `id_cliente` apunta a `clientes(id_cliente)` e `id_usuario` apunta a `usuarios(id_usuario)`.
6. **`detalle_ventas` (Detalle)**: registra cada renglón o producto incluido en una venta (`id_detalle`, `id_venta`, `id_producto`, `cantidad`, `precio_unitario`, `subtotal`).
   - **Relaciones:** `id_venta` apunta a `ventas(id_venta)` e `id_producto` apunta a `productos(id_producto)`.

> **¿Por qué se usa Cabecera-Detalle?**
> Porque una misma venta puede tener **uno o muchos productos**. Si guardáramos todo en una sola tabla, tendríamos que repetir los datos del cliente y la fecha por cada producto. Al separarlo en `ventas` (cabecera) y `detalle_ventas` (detalle), la cabecera se guarda una sola vez y el detalle guarda todos los productos asociados a ese `id_venta`.

---

### Paso 3: Inicio de sesión y control de acceso
1. Cuando alguien entra al sistema sin haberse logueado (por ejemplo intentando ir directo a `index.php`, `ventas.php` o cualquier ABM), el archivo `verificarsesion.php` detecta que no hay una sesión activa y **lo redirige automáticamente a `login.php`**.
2. En `login.php`, el usuario ingresa su **usuario** y **contraseña** (por ejemplo, `admin` / `1234` en el usuario inicial de prueba).
3. Si los datos son correctos, el sistema guarda en la sesión (`$_SESSION`) el ID, nombre y usuario, y lo deja entrar a `index.php`.
4. En la barra superior (`navbar.php`) se muestra el nombre del usuario conectado y el botón **Salir** (`logout.php`), que destruye la sesión y vuelve al login.

---

### Paso 4: Pantalla principal y navegación
Al ingresar, `index.php` muestra:
- Una barra de navegación superior (`navbar.php`) presente en todo el sistema con acceso a: **Inicio, Ventas, Clientes, Productos, Categorías, Usuarios** y **Salir**.
- Tarjetas de acceso rápido hacia cada uno de los módulos.

---

### Paso 5: Funcionamiento de los Módulos ABM
Todos los ABMs (**Altas, Bajas y Modificaciones**) comparten una misma lógica clara: a la izquierda está el formulario y a la derecha la tabla con el listado.

1. **Categorías (`categorias.php`):**
   - Permite crear categorías nuevas, editarlas (al tocar *Editar*, el formulario se llena con los datos existentes y cambia el botón a *Guardar Cambios*) y eliminarlas.
   - **Integridad:** si se intenta eliminar una categoría que ya tiene productos asociados, MySQL lo impide por la clave foránea y el sistema muestra un mensaje de aviso en lugar de romperse.
2. **Productos (`productos.php`):**
   - Al cargar un producto se elige su categoría desde un desplegable (`<select>`) cargado dinámicamente desde la tabla `categorias`.
   - Se indica nombre, precio, stock y descripción.
   - El listado muestra el nombre de la categoría (usando `INNER JOIN`) en lugar de mostrar solo el número de ID.
   - Si un producto ya fue vendido en alguna venta, el sistema no permite eliminarlo para no perder el historial.
3. **Clientes (`clientes.php`):**
   - Permite cargar, editar, listar y eliminar clientes (nombre, apellido, teléfono, email y dirección).
   - No permite eliminar un cliente si ya tiene ventas registradas.
4. **Usuarios (`usuarios.php`):**
   - Permite dar de alta nuevos usuarios guardando la contraseña **encriptada** (`password_hash`).
   - Al editar un usuario, si se deja el campo contraseña vacío se mantiene la contraseña actual; si se escribe una nueva, se vuelve a encriptar.
   - Impide que el usuario conectado se elimine a sí mismo y también protege usuarios que ya registraron ventas.

---

### Paso 6: Funcionamiento del Módulo de Ventas (Transacción Cabecera-Detalle)
Es el módulo principal del sistema y funciona en 4 pantallas:

1. **Listado de Ventas (`ventas.php`):**
   - Muestra todas las ventas realizadas (N° de venta, fecha, cliente, vendedor y total).
   - Tiene botones para **+ Nueva Venta**, **Ver Detalle**, **Editar** y **Eliminar**.
2. **Registrar Nueva Venta (`nueva_venta.php`):**
   - **Cabecera:** se selecciona el **Cliente** y se muestra el **Vendedor** logueado.
   - **Detalle dinámico (JavaScript):** se selecciona un **Producto** (el sistema autocompleta automáticamente su precio, aunque el vendedor puede modificarlo), se indica la **Cantidad** y se presiona **+ Agregar**.
   - Se pueden agregar tantos productos como se necesiten. En cada fila agregada, JavaScript calcula el **Subtotal** (`cantidad × precio`) y actualiza el **Total general** en tiempo real, permitiendo también quitar filas antes de guardar.
   - Al presionar **Guardar Venta**: PHP valida que haya cliente y al menos un producto, inicia una **transacción en MySQL**, inserta la cabecera en `ventas`, inserta cada producto en `detalle_ventas`, **descuenta el stock** de cada producto en la tabla `productos`, y confirma todo con `commit()`. Si ocurre algún error en el medio, `rollBack()` deshace todo para no dejar ventas a medias ni stock mal descontado.
3. **Consultar Detalle (`ver_venta.php`):**
   - Al guardar una venta (o al tocar *Ver Detalle* en el listado), muestra los datos de la cabecera (cliente, teléfono, fecha, vendedor) y la tabla con todos los productos vendidos, sus cantidades, precios unitarios, subtotales y el total final.
4. **Editar Venta (`editar_venta.php`):**
   - Carga el cliente actual y los productos que tenía esa venta en la tabla dinámica.
   - Permite cambiar el cliente, quitar productos existentes o agregar nuevos.
   - Al guardar dentro de una transacción: primero **devuelve el stock** de los productos anteriores, borra los detalles viejos, actualiza la cabecera, inserta los nuevos detalles y **vuelve a descontar el stock** actualizado.
5. **Eliminar Venta (desde `ventas.php`):**
   - Dentro de una transacción: primero **devuelve el stock** de los productos vendidos, luego elimina los registros de `detalle_ventas` y finalmente elimina la cabecera en `ventas`.
