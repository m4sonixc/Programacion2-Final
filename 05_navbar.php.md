# 🧭 Barra de Navegación del Sistema (`navbar.php`)

Este componente representa la **barra de navegación superior (Navbar)** del sistema del *Taller de Pachi*. Está diseñado como un módulo reutilizable que se incluye en la cabecera de todas las vistas protegidas del sistema.

Permite a los usuarios moverse fácilmente entre las distintas secciones del taller, ver qué usuario tiene la sesión activa y cerrar sesión de manera segura.

---

## 🎯 ¿Qué hace este archivo en resumen?

1. **Navegación general:** Ofrece enlaces directos a todos los módulos: Inicio, Ventas, Clientes, Productos, Categorías y Usuarios.
2. **Diseño responsivo:** En pantallas grandes muestra los enlaces horizontales; en teléfonos móviles se transforma automáticamente en un **menú hamburguesa** desplegable.
3. **Personalización dinámica (PHP):** Lee la sesión activa del usuario e imprime su nombre de bienvenida en pantalla.
4. **Protección de seguridad:** Aplica un filtro contra ataques **XSS (Cross-Site Scripting)** al mostrar el nombre del usuario.
5. **Cierre de sesión:** Incluye un botón destacado para salir del sistema (`logout.php`).

---

---

## 🎨 1. Explicación de la Estructura en HTML y Bootstrap 5

### A. Contenedor Principal de la Barra
```html
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
```
* **`navbar`**: Clase base de Bootstrap para crear barras de navegación.
* **`navbar-expand-lg`**: Define el punto de quiebre (*breakpoint*). En pantallas grandes (`lg` o superiores) el menú se ve extendido; en pantallas medianas o celulares, se colapsa.
* **`navbar-dark bg-dark`**: Aplica un tema visual oscuro (fondo negro/gris oscuro con texto claro), dándole un aspecto moderno y profesional.
* **`mb-4` (Margin Bottom)**: Añade un espacio de separación hacia abajo para que el contenido de la página no quede pegado a la barra.
* **`container`**: Centra el contenido de la barra y respeta los márgenes estándar del resto del sitio web.

---

### B. Logo y Botón para Celulares (Menú Hamburguesa)
```html
<a class="navbar-brand" href="index.php">Taller de Pachi</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
    <span class="navbar-toggler-icon"></span>
</button>
```
* **`navbar-brand`**: El título o logo del sistema. Al hacer clic, siempre regresa al panel principal (`index.php`).
* **`navbar-toggler`**: El botón que aparece únicamente en celulares.
  * **`data-bs-toggle="collapse"`** y **`data-bs-target="#menuPrincipal"`**: Atributos de JavaScript de Bootstrap que indican qué contenedor debe abrirse o cerrarse al tocar el botón (el que tenga el identificador `menuPrincipal`).
  * **`navbar-toggler-icon`**: Dibuja el clásico icono de tres líneas horizontales (hamburguesa).

---

### C. Enlaces de Navegación del Menú
```html
<div class="collapse navbar-collapse" id="menuPrincipal">
    <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="ventas.php">Ventas</a></li>
        ...
    </ul>
```
* **`collapse navbar-collapse`**: Agrupa los enlaces para que puedan ocultarse o mostrarse en móviles.
* **`navbar-nav me-auto`**: Lista ordenada de enlaces. La clase **`me-auto`** (*margin-end: auto*) empuja todo lo que venga después (el saludo y el botón de salir) hacia el extremo derecho de la barra.
* **`nav-item` y `nav-link`**: Estilizan cada enlace de forma limpia, añadiendo efectos visuales cuando se pasa el cursor por encima (*hover*).

---

### D. Botón de Cierre de Sesión
```html
<a href="logout.php" class="btn btn-outline-danger btn-sm">Salir</a>
```
* **`btn btn-outline-danger`**: Botón con borde rojo y fondo transparente que se ilumina de rojo al pasar el mouse por encima, indicando visualmente una acción de salida o desconexión.
* **`btn-sm`**: Tamaño compacto para que encaje de manera armónica en la barra.
* **`href="logout.php"`**: Enlace al archivo que destruye la sesión y redirige al login.

---

## 🐘 2. Explicación del Código PHP (Seguridad y Dinamismo)

```php
<span class="navbar-text text-light me-3">
    Hola, <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>
</span>
```

### ¿Qué hace exactamente esta línea?
1. **Acceso a la sesión (`$_SESSION['nombre_usuario']`)**: Recupera el nombre real del usuario que fue guardado en el servidor al momento de hacer login. De este modo, la barra dice de forma personalizada: *"Hola, Juan"* o *"Hola, Administrador"*.
2. **Seguridad contra XSS (`htmlspecialchars`)**:
   * **¿Por qué es fundamental?** Si el nombre de un usuario en la base de datos contiene caracteres especiales como `<script>` o comillas, un atacante podría intentar inyectar código malicioso en el navegador de quien esté viendo la página (**Cross-Site Scripting**).
   * **¿Cómo lo soluciona?** Convierte caracteres peligrosos en entidades HTML seguras (por ejemplo, transforma `<` en `&lt;` y `>` en `&gt;`), logrando que el navegador lo interprete puramente como texto visible y nunca como código ejecutable.

---

## 💡 Cómo incluirlo en tus páginas

Al ser un componente modular, se inserta fácilmente en cualquier vista privada (`index.php`, `ventas.php`, `productos.php`, etc.) simplemente agregando:

```php
<?php include "navbar.php"; ?>
```
