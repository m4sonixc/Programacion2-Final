# 🏠 Panel de Control Principal / Dashboard (`index.php`)

Este archivo representa la **pantalla principal (Dashboard)** del sistema del *Taller de Pachi*. Es la vista a la que llega el usuario una vez que inicia sesión correctamente. 

Funciona como un centro de mando visual y modular que organiza el acceso a todas las áreas operativas del taller (ventas, stock, clientes, categorías y usuarios).

---

## 🎯 ¿Qué hace este archivo en resumen?

1. **Protege la pantalla:** Valida que el usuario tenga una sesión activa antes de mostrar cualquier dato.
2. **Carga la navegación común:** Inserta la barra de menú superior (`navbar.php`) de forma modular.
3. **Organiza los módulos del sistema:** Presenta un menú tipo mosaico o cuadrícula (*grid*) con tarjetas (*cards*) interactivas para acceder a cada sección del taller.
4. **Diseño adaptable (Responsive):** Se adapta automáticamente para verse correctamente en teléfonos móviles, tablets y computadoras de escritorio mediante Bootstrap 5.

---

---

## 🐘 1. Explicación del Código PHP (Backend y Modularidad)

### A. Control de Seguridad Obligatorio
```php
<?php
require_once "verificarsesion.php";
?>
```
* **¿Qué hace?** Es la primera instrucción del archivo. Llama al script `verificarsesion.php` para comprobar si existe la sesión del usuario.
* **¿Por qué es clave?** Si un usuario intenta entrar a `index.php` sin haberse logueado primero en `login.php`, este archivo lo intercepta de inmediato y lo expulsa al inicio de sesión, impidiendo que el resto del HTML se cargue o se envíe al navegador.
* **`require_once`**: Garantiza que el archivo se cargue una sola vez y detiene el script si el archivo no existe.

---

### B. Inclusión del Menú de Navegación
```php
<?php include "navbar.php"; ?>
```
* **Modularidad:** En lugar de reescribir el código HTML del menú de navegación en cada página del sistema, se escribe una sola vez en `navbar.php` y se incluye dinámicamente con `include`.
* Si en el futuro necesitas agregar un enlace al menú, solo modificas `navbar.php` y el cambio se refleja en todo el sitio web.

---

## 🎨 2. Explicación del Código HTML y Bootstrap 5 (Frontend)

### A. Configuración del Documento y Librerías
```html
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
...
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
```
* **`viewport`**: Ajusta la escala visual para que la página sea legible tanto en pantallas pequeñas de smartphones como en monitores grandes.
* **Bootstrap CSS y JS Bundle**: Importa la hoja de estilos y los scripts de JavaScript necesarios para componentes interactivos (menús desplegables, modales, etc.).

---

### B. Contenedor y Encabezado de Bienvenida
```html
<body class="bg-light">
    <div class="container">
        <div class="p-4 mb-4 bg-white rounded shadow-sm">
            <h2>Bienvenido al Sistema del Taller de Pachi</h2>
            <p class="text-muted mb-0">Seleccioná un módulo para comenzar a trabajar.</p>
        </div>
```
* **`bg-light`**: Aplica un fondo gris suave al cuerpo de la página para que resalten los componentes blancos.
* **`container`**: Centra el contenido en la pantalla y añade márgenes automáticos a los lados.
* **Banner de Bienvenida**: Un cuadro blanco (`bg-white`), con esquinas redondeadas (`rounded`), sombra sutil (`shadow-sm`) y espaciado interno (`p-4`), que sirve como saludo y guía inicial para el usuario.

---

### C. Cuadrícula de Módulos (Grid System de Bootstrap)
```html
<div class="row g-3">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">Ventas</h5>
                <p class="card-text">Registrar nuevas ventas, ver detalles, modificar o eliminar.</p>
                <a href="ventas.php" class="btn btn-primary">Ir a Ventas</a>
            </div>
        </div>
    </div>
    ...
</div>
```
* **`row g-3`**: Define una fila con un espacio (*gutter*) uniforme de nivel 3 entre todas las tarjetas.
* **`col-md-4`**: Divide la fila en un sistema de 12 columnas:
  * En pantallas medianas y grandes (`md` o superior), cada tarjeta ocupa 4 columnas (mostrando **3 tarjetas por fila**).
  * En dispositivos móviles (pantallas pequeñas), cada tarjeta ocupa automáticamente las 12 columnas (**1 tarjeta por fila**).
* **`card shadow-sm h-100`**:
  * Diseña una tarjeta limpia con bordes y sombra ligera.
  * **`h-100` (Height 100%)**: Hace que todas las tarjetas de la misma fila tengan exactamente la misma altura, sin importar que una tenga más texto que otra.
* **Jerarquía de Botones**:
  * **`btn-primary` (Azul)**: Destaca el botón de **Ventas**, indicando que es la acción más frecuente y principal del taller.
  * **`btn-secondary` (Gris)**: Utilizado para el resto de los módulos de gestión (Productos, Clientes, Categorías, Usuarios), manteniendo un orden visual claro.

---

## 🗂️ Módulos Disponibles en el Panel

| Módulo | Archivo Destino | Descripción | Botón |
| :--- | :--- | :--- | :--- |
| **Ventas** | `ventas.php` | Registro de operaciones comerciales, facturación y detalles. | Principal (`btn-primary`) |
| **Productos** | `productos.php` | Gestión del inventario (repuestos, aceites, stock y precios). | Secundario (`btn-secondary`) |
| **Clientes** | `clientes.php` | Base de datos de clientes frecuentes y sus datos de contacto. | Secundario (`btn-secondary`) |
| **Categorías** | `categorias.php` | Clasificación y orden de los artículos del taller. | Secundario (`btn-secondary`) |
| **Usuarios** | `usuarios.php` | Control de accesos y administración de cuentas de usuario. | Secundario (`btn-secondary`) |
