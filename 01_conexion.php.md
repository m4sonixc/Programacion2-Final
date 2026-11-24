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
Esta sección establece el enlace entre la aplicación PHP y el servidor MySQL de forma controlada:
* **`PDO` (PHP Data Objects):** Extensión nativa de PHP utilizada para conectarse y operar con bases de datos de forma segura, permitiendo el uso de consultas preparadas para prevenir inyecciones SQL.

* **`try { ... }`:** Inicia un bloque de manejo de excepciones. Intenta ejecutar la conexión y, si ocurre algún fallo (como credenciales erróneas o el servidor caído), permite capturar el error en un bloque `catch` sin exponer datos sensibles en pantalla.
* **`$conexion = new PDO(...)`:** Crea e instancia un nuevo objeto de conexión usando la extensión **PDO** y lo guarda en la variable `$conexion` para realizar consultas posteriormente.
* **Cadena de conexión (DSN):**
  * **`mysql:`** Indica el controlador de base de datos a utilizar (MySQL / MariaDB).
  * **`host=$servidor`:** Define la dirección del servidor donde se aloja la base de datos (por ejemplo, `localhost`).
  * **`dbname=$nombre_bd`:** Especifica el nombre exacto de la base de datos a la que se conectará el sistema.
  * **`charset=utf8mb4`:** Configura la codificación de caracteres para soportar correctamente tildes, la letra `ñ` y caracteres especiales sin errores de formato.
* **`$usuario_bd` y `$password_bd`:** Credenciales de acceso del usuario autorizado para operar sobre esa base de datos.
---

### 3. Ajustes de Seguridad y Comodidad
```php
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
```
### (`setAttribute`)
El método `setAttribute` define el comportamiento global de la conexión PDO con la base de datos mediante dos reglas principales:
* **Manejo de errores (`PDO::ATTR_ERRMODE`, `PDO::ERRMODE_EXCEPTION`):** Configura a PDO para que lance excepciones (`PDOException`) cada vez que ocurra un fallo en la conexión o en una consulta SQL. Esto permite capturar los errores de forma controlada usando bloques `try-catch` en lugar de mostrar advertencias genéricas en pantalla.
* **Modo de lectura por defecto (`PDO::ATTR_DEFAULT_FETCH_MODE`, `PDO::FETCH_ASSOC`):** Establece que todas las consultas devuelvan los resultados automáticamente como **arrays asociativos** (indexados por el nombre de cada columna), evitando tener que especificar `PDO::FETCH_ASSOC` manualmente cada vez que se ejecuta un `fetch()` o `fetchAll()`.
* **`PDO::FETCH_ASSOC`:** Modo de extracción que le indica a PDO que devuelva los resultados de la consulta como un **array asociativo**, utilizando los nombres de las columnas de la tabla como claves.
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
