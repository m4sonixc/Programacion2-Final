# 🛡️ Control de Sesión y Protección de Rutas (`auth.php` / Verificación de Acceso)

Este fragmento de código funciona como un **"guardián" o filtro de seguridad**. Su función principal es proteger las páginas privadas del sistema (como un panel de administración, perfil o lista de clientes), asegurando que **solo los usuarios que hayan iniciado sesión puedan ver el contenido**.

Si una persona intenta entrar directamente escribiendo la URL en el navegador sin autenticarse, el sistema la detecta y la expulsa automáticamente a la pantalla de inicio de sesión (`login.php`).

---

## 🎯 ¿Qué hace este código en resumen?

1. **Abre o reanuda la sesión** del usuario en el servidor.
2. **Pregunta si el usuario está autenticado:** Revisa si existe la variable de sesión que identifica al usuario (`id_usuario`).
3. **Redirecciona si no ha iniciado sesión:** Si no la encuentra, envía una orden al navegador para que redirija a `login.php`.
4. **Corta la ejecución de inmediato:** Detiene el código restante para evitar que se cargue contenido privado en segundo plano.

---

## 🧠 Explicación Paso a Paso (Línea por Línea)

### 1. Iniciar el motor de sesiones
```php
session_start();
```
* **¿Qué hace?** Le dice a PHP: *"busca si este usuario ya tiene una sesión abierta en el navegador mediante cookies/servidor; si existe, cárgala; si no, prepárame una nueva"*.
* **Importante:** Debe colocarse siempre al principio del archivo, antes de enviar cualquier texto o código HTML al navegador.

---

### 2. Comprobar si el usuario está autenticado
```php
if (!isset($_SESSION['id_usuario'])) {
```
* **`$_SESSION`**: Es una variable especial (superglobal) que guarda información del usuario mientras navega entre páginas del sitio. Por ejemplo, al iniciar sesión con éxito en el formulario de login, se suele guardar: `$_SESSION['id_usuario'] = 5;`.
* **`isset(...)`**: Función que verifica si una variable existe y no es nula.
* **El signo `!` (NO / Negación)**: Invierte la condición. Por lo tanto, la línea se lee en lenguaje natural como:
  > *"Si **NO** está definida la variable `'id_usuario'` dentro de la sesión..."*

---

### 3. Redirigir al inicio de sesión
```php
    header("Location: login.php");
```
* **¿Qué hace?** Envía una cabecera HTTP de redirección al navegador del visitante.
* El navegador recibe la respuesta y automáticamente carga la página de inicio de sesión (`login.php`).

---

### 4. Detener la ejecución del script
```php
    exit();
```
* **¿Por qué es indispensable?** Aunque el navegador reciba la orden de cambiar de página con `header()`, PHP seguiría ejecutando el resto de las líneas que estén escritas abajo si no ponemos `exit()`.
* Si un usuario malintencionado bloquea las redirecciones en su navegador, podría llegar a ver o procesar el código protegido. Con `exit()` (o `die()`), garantizamos que **el servidor deje de procesar la página en ese mismo instante**.

---
