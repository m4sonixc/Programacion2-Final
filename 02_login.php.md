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
Este bloque captura y prepara los datos enviados desde el formulario HTML antes de consultarlos en la base de datos:

1. **Verificación del método (`$_SERVER["REQUEST_METHOD"] == "POST"`):** Comprueba que la petición haya sido enviada mediante el método `POST` (es decir, que el usuario haya presionado el botón de enviar en el formulario) y no por una carga normal de página (`GET`).
2. **Captura y limpieza del usuario (`trim($_POST["usuario"] ?? "")`):** Obtiene el valor ingresado en el campo `usuario` y aplica `trim()` para eliminar espacios en blanco accidentales al inicio o al final. El operador de fusión de null (`?? ""`) asigna una cadena vacía si el campo no fue enviado, evitando errores de índice indefinido.
3. **Captura de la contraseña (`$_POST["password"] ?? ""`):** Recupera la contraseña tal cual fue escrita, asignando una cadena vacía por defecto si no existe. No se aplica `trim()` para respetar si el usuario incluyó espacios intencionalmente en su clave.

---

### D. Consulta Segura (Prevención de Inyecciones SQL)
```php
$stmt = $conexion->prepare("SELECT id_usuario, nombre, usuario, password FROM usuarios WHERE usuario = ?");
$stmt->execute([$usuario]);
$fila = $stmt->fetch(PDO::FETCH_ASSOC);
```
* **`PDO` (PHP Data Objects):** Extensión nativa de PHP utilizada para conectarse y operar con bases de datos de forma segura, permitiendo el uso de consultas preparadas para prevenir inyecciones SQL.
* **`$stmt` (Statement / Sentencia):** Variable que almacena el objeto de la consulta SQL preparada (`PDOStatement`). Actúa como intermediario entre PHP y la base de datos: primero guarda la estructura de la consulta, luego la ejecuta con los datos reales y finalmente permite extraer los resultados.
* **`PDO::FETCH_ASSOC`:** Modo de extracción que le indica a PDO que devuelva el resultado como un **array asociativo**, utilizando los nombres de las columnas de la tabla (`id_usuario`, `nombre`, `usuario`, `password`) como claves.
* **`$fila`:** Variable que guarda el registro extraído de la base de datos. Si el usuario existe, contiene un arreglo con sus datos; si no hay coincidencias, devuelve `false`.

---

### Consulta Preparada y Obtención de Datos

Antes de validar las credenciales, el sistema busca al usuario en la base de datos en tres pasos:

1. **Preparación (`$conexion->prepare(...)`):** Crea la plantilla de la consulta `SELECT` y la guarda en `$stmt`. El signo de interrogación (`?`) funciona como un **marcador de posición (placeholder)** que reserva el lugar donde irá el dato del usuario, evitando que cualquier texto malicioso altere la consulta SQL.
2. **Ejecución (`$stmt->execute([$usuario])`):** Envía el valor real de `$usuario` para reemplazar al signo `?` y ejecuta la consulta de forma segura en el motor de base de datos.
3. **Extracción (`$stmt->fetch(PDO::FETCH_ASSOC)`):** Recupera la primera fila coincidente desde `$stmt` en formato de array asociativo y la almacena en `$fila`.
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
### Explicación de la Lógica de Validación de Login

* **`if (is_array($fila))`:** Verifica que la consulta a la base de datos haya encontrado al usuario y devuelto sus datos en forma de arreglo (array). Si devuelve `false`, significa que no hubo coincidencias y salta directamente al bloque `else` final para asignar `"El usuario no existe."`.
* **`if (password_verify($password, $fila["password"]) || $password === $fila["password"])`:** Valida la contraseña ingresada contra la almacenada en la base de datos:
  * `password_verify(...)`: Comprueba si la contraseña coincide con un hash encriptado (método seguro).
  * `|| $password === $fila["password"]`: Condición alternativa (`O`) que verifica coincidencia exacta en texto plano, útil para compatibilidad con registros antiguos sin encriptar.
* **`$_SESSION["id_usuario"] = $fila["id_usuario"];`:** Guarda el ID único del usuario en la sesión del servidor para identificarlo en todo el sistema.
* **`$_SESSION["nombre_usuario"] = $fila["nombre"];`:** Almacena el nombre real del usuario en la sesión para mostrarlo en la interfaz (por ejemplo, en mensajes de bienvenida).
* **`$_SESSION["usuario"] = $fila["usuario"];`:** Guarda el nombre de usuario (login) en la sesión activa.
* **`header("Location: index.php");`:** Envía una cabecera HTTP al navegador para redirigir automáticamente al usuario hacia la página principal (`index.php`).
* **`exit();`:** Detiene de inmediato la ejecución del script PHP para asegurar que no se procese ningún código posterior tras la redirección.
* **`else { $error = "Contraseña incorrecta."; }`:** Se ejecuta si el usuario existe, pero la contraseña no pasó ninguna de las dos validaciones.
* **`else { $error = "El usuario no existe."; }`:** Se ejecuta cuando `$fila` no es un arreglo, indicando que el usuario ingresado no está registrado en la base de datos.

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
