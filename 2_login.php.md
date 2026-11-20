# 🔐 Módulo de Inicio de Sesión (`login.php`)

Este archivo gestiona la **autenticación de usuarios** en el sistema del *Taller de Pachi*. Combina la lógica del servidor en **PHP** (para validar credenciales de forma segura con la base de datos) con una interfaz visual en **HTML5 y Bootstrap 5** (para mostrar un formulario limpio y moderno).

---

## 🎯 ¿Qué hace este archivo en resumen?

1. **Evita accesos innecesarios:** Si el usuario ya había iniciado sesión, lo manda directamente al panel principal (`index.php`).
2. **Muestra el formulario:** Presenta una tarjeta visual centrada en pantalla para ingresar usuario y contraseña.
3. **Procesa y valida los datos:** Cuando el usuario presiona "Ingresar", verifica que los campos no estén vacíos y consulta la base de datos de manera protegida contra inyecciones SQL.
4. **Verifica la clave y crea la sesión:** Si la contraseña es correcta, almacena los datos del usuario en la sesión del servidor y le da acceso; de lo contrario, muestra una alerta de error en pantalla.

---

## 🔄 Flujo del Sistema



---

## 🐘 1. Explicación de la Lógica en PHP (Backend)

### A. Verificación previa de sesión
```php
session_start();
if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}
```
* Abre la sesión para comprobar si el usuario ya está conectado (`isset($_SESSION['id_usuario'])`).
* Si ya inició sesión, no tiene sentido volver a mostrarle el login: lo redirige de inmediato a `index.php` y detiene la página con `exit()`.

---

### B. Conexión e inicio de variables
```php
require_once "config/conexion.php";
$error = "";
```
* **`require_once "config/conexion.php"`**: Importa el archivo que crea la conexión PDO a la base de datos.
* **`$error = ""`**: Variable que servirá como contenedor de cualquier mensaje de error que ocurra durante el proceso.

---

### C. Captura y limpieza de datos
```php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";
```
* Comprueba si el usuario envió el formulario mediante el método **POST**.
* **`trim(...)`**: Quita espacios en blanco accidentales al inicio y al final del usuario.
* **`?? ""` (Operador de fusión por null)**: Si no se envió el dato, asigna un texto vacío en lugar de generar una advertencia de PHP.

---

### D. Consulta Segura (Prevención de Inyecciones SQL)
```php
$stmt = $conexion->prepare("SELECT id_usuario, nombre, usuario, password FROM usuarios WHERE usuario = ?");
$stmt->execute([$usuario]);
$fila = $stmt->fetch(PDO::FETCH_ASSOC);
```
* **Consultas Preparadas (`prepare` y `execute`)**: En lugar de concatenar el texto del usuario directamente en la consulta, se utiliza un marcador `?`. Esto **bloquea cualquier intento de inyección SQL (SQL Injection)**, ya que el motor de la base de datos trata el dato estrictamente como texto y no como código ejecutable.
* **`fetch(PDO::FETCH_ASSOC)`**: Obtiene la fila encontrada del usuario como un arreglo asociativo con los nombres de las columnas.

---

### E. Comprobación de Contraseña y Creación de Sesión
```php
if (is_array($fila)) {
    if (password_verify($password, $fila["password"]) || $password === $fila["password"]) {
        $_SESSION["id_usuario"] = $fila["id_usuario"];
        $_SESSION["nombre_usuario"] = $fila["nombre"];
        $_SESSION["usuario"] = $fila["usuario"];
        header("Location: index.php");
        exit();
    } else {
        $error = "Contraseña incorrecta.";
    }
} else {
    $error = "El usuario no existe.";
}
```
* **`password_verify($password, $fila["password"])`**: Verifica si la contraseña escrita coincide con la contraseña encriptada (hash `bcrypt`/`argon2`) en la base de datos.
* **`|| $password === $fila["password"]`**: Condición de compatibilidad hacia atrás que permite ingresar si la clave fue guardada en texto plano (útil durante pruebas iniciales).
* Si todo es correcto, se guardan en la memoria del servidor (`$_SESSION`) los datos esenciales del usuario para usarlos en el resto del sistema, y se le envía a `index.php`.
* Si falla, se asigna el mensaje correspondiente a `$error`.

---

## 🎨 2. Explicación de la Interfaz en HTML y Bootstrap (Frontend)

### A. Encabezado y Recursos Externos
```html
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
```
* Asegura que la pantalla sea **responsive** (se adapte correctamente a celulares, tablets y computadoras).
* Conecta la librería de estilos **Bootstrap 5** mediante CDN, permitiendo maquetar sin necesidad de escribir CSS manual.

---

### B. Centrado en Pantalla con Flexbox
```html
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
```
* **`bg-light`**: Fondo gris claro, suave a la vista.
* **`d-flex align-items-center justify-content-center`**: Utiliza Flexbox para centrar la tarjeta de login tanto vertical como horizontalmente.
* **`vh-100`**: Hace que el cuerpo ocupe exactamente el 100% de la altura visible de la ventana del navegador.

---

### C. La Tarjeta de Login (Card)
```html
<div class="card shadow" style="width: 100%; max-width: 400px;">
```
* Crea un contenedor tipo tarjeta con borde redondeado y sombra elegante (`shadow`), con un ancho máximo de 400 píxeles para que se vea bien en cualquier dispositivo.

---

### D. Renderizado Dinámico de Errores (PHP embebido)
```php
<?php if ($error != ""): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $error; ?>
    </div>
<?php endif; ?>
```
* Si la variable `$error` contiene algún mensaje (por ejemplo: *"Contraseña incorrecta."*), PHP inserta en el código HTML un cuadro de alerta rojo de Bootstrap (`alert alert-danger`). Si no hay errores, este bloque no se muestra.

---

### E. Formulario de Captura
```html
<form method="POST" action="login.php">
    <div class="mb-3">
        <label for="usuario" class="form-label">Usuario</label>
        <input type="text" class="form-control" id="usuario" name="usuario" required autofocus>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
</form>
```
* **`method="POST"`**: Envía las credenciales en el cuerpo de la petición HTTP, de forma oculta y segura (no a través de la barra de direcciones como ocurriría con `GET`).
* **`autofocus`**: Coloca el cursor en el campo de usuario automáticamente apenas carga la página.
* **`type="password"`**: Oculta los caracteres de la contraseña con puntos o asteriscos en la pantalla.
* **`required`**: Validación básica del navegador que impide enviar el formulario si algún campo está en blanco.
* **`btn-primary w-100`**: Botón de acción destacado que ocupa el 100% del ancho del formulario.

---

## 💡 Recomendaciones de Seguridad para Producción

> [!NOTE]
> * **Migrar a Hashing Seguro:** La comparación `$password === $fila["password"]` (texto plano) es práctica para desarrollo, pero en producción se recomienda almacenar siempre las contraseñas con `password_hash($password, PASSWORD_DEFAULT)` y validar únicamente con `password_verify()`.
> * **Protección contra fuerza bruta:** En proyectos grandes, es aconsejable implementar un contador de intentos fallidos o un captcha para evitar ataques automatizados.
