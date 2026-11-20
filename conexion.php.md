# 🔌 Módulo de Conexión a la Base de Datos (`conexion.php`)

Este archivo es el encargado de establecer la comunicación entre la aplicación web en **PHP** y el motor de base de datos **MySQL / MariaDB**. 

Funciona como el "puente" o "llave de acceso" que permite al sistema consultar, guardar, actualizar o eliminar información de forma segura y eficiente.

---

## 🎯 ¿Qué hace este código?

1. **Define las credenciales de acceso:** Indica dónde está la base de datos, qué base de datos usar y con qué usuario/contraseña ingresar.
2. **Crea la conexión mediante PDO:** Utiliza la interfaz moderna y segura de PHP (`PDO`) para abrir el canal de datos.
3. **Configura el comportamiento:** Establece que ante cualquier error se lance una alerta inmediata y que los datos se entreguen organizados por nombres de columna.
4. **Maneja fallos:** Si los datos son incorrectos o el servidor está apagado, captura el error de forma controlada y muestra un mensaje en pantalla sin romper la aplicación inesperadamente.

---

## 🧠 Explicación Paso a Paso (Línea por Línea)

### 1. Parámetros de Configuración
```php
$servidor = "localhost";
$usuario_bd = "root";
$password_bd = "";
$nombre_bd = "taller_pachi";
```
* **`$servidor`**: La dirección donde corre la base de datos (`localhost` indica que está en la misma computadora o servidor local, típico de XAMPP, WampServer o Laragon).
* **`$usuario_bd`**: El nombre del usuario con permisos en la base de datos (por defecto `root` en entornos de desarrollo).
* **`$password_bd`**: La contraseña del usuario (en blanco por defecto en instalaciones locales).
* **`$nombre_bd`**: El nombre exacto de la base de datos a la que nos conectaremos (`taller_pachi`).

---

### 2. Intento de Conexión Segura (`try`)
```php
try {
    $conexion = new PDO("mysql:host=$servidor;dbname=$nombre_bd;charset=utf8mb4", $usuario_bd, $password_bd);
```
* **`try { ... }`**: Significa *"intenta ejecutar este bloque"*. Si ocurre algún problema en el camino, no se detendrá todo de golpe, sino que pasará a la sección de captura (`catch`).
* **`new PDO(...)`**: Crea un nuevo objeto **PDO** (*PHP Data Objects*), el estándar recomendado en PHP para conectarse a bases de datos.
  * **`charset=utf8mb4`**: Garantiza que el sistema admita tildes, letras como la `ñ` y caracteres especiales o emojis sin que se corrompan los textos.

---

### 3. Ajustes de Seguridad y Comodidad
```php
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
```
* **`ATTR_ERRMODE => ERRMODE_EXCEPTION`**: Le indica a PDO que si una consulta SQL falla (por ejemplo, por un error de sintaxis o una tabla inexistente), lance una **excepción** en lugar de fallar en silencio. Esto facilita mucho encontrar y corregir errores durante el desarrollo.
* **`ATTR_DEFAULT_FETCH_MODE => FETCH_ASSOC`**: Configura que al consultar datos, estos se devuelvan siempre como un arreglo asociativo: `["nombre" => "Juan", "telefono" => "123456"]`, en lugar de duplicar los datos con índices numéricos (`[0 => "Juan", "nombre" => "Juan"]`). Ahorra memoria y hace el código más limpio.

---

### 4. Control de Errores (`catch`)
```php
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
```
* **`catch (PDOException $e)`**: Si dentro del bloque `try` algo sale mal (servidor MySQL apagado, base de datos inexistente o credenciales equivocadas), se atrapa el error dentro de la variable `$e`.
* **`die(...)`**: Detiene de inmediato la ejecución del script y muestra el mensaje descriptivo del fallo (`$e->getMessage()`).

---
