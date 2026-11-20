<?php
session_start();
if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}
require_once "config/conexion.php";
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";
    if ($usuario == "" || $password == "") {
        $error = "Completá usuario y contraseña.";
    } else {
        $stmt = $conexion->prepare("SELECT id_usuario, nombre, usuario, password FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
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
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Taller de Pachi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow" style="width: 100%; max-width: 400px;">
        <div class="card-body p-4">
            <h3 class="text-center mb-2">Taller de Pachi</h3>
            <p class="text-center text-muted mb-4">Iniciar Sesión</p>
            <?php if ($error != ""): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
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
        </div>
    </div>
</body>
</html>