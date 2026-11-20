<?php
require_once "verificarsesion.php";
require_once "config/conexion.php";

$mensaje = "";
$tipo_alerta = "";
$id_editar = "";
$nombre_editar = "";
$usuario_editar = "";
if (isset($_GET["eliminar"])) {
    $id = intval($_GET["eliminar"]);
    if ($id == $_SESSION["id_usuario"]) {
        $mensaje = "No podés eliminar tu propio usuario mientras estás conectado.";
        $tipo_alerta = "warning";
    } else {
        try {
            $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
            $stmt->execute([$id]);
            $mensaje = "Usuario eliminado correctamente.";
            $tipo_alerta = "success";
        } catch (PDOException $e) {
            $mensaje = "No se puede eliminar el usuario porque tiene ventas registradas.";
            $tipo_alerta = "danger";
        }
    }
}
if (isset($_GET["editar"])) {
    $id_editar = intval($_GET["editar"]);
    $stmt = $conexion->prepare("SELECT id_usuario, nombre, usuario FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id_editar]);
    if ($fila = $stmt->fetch()) {
        $nombre_editar = $fila["nombre"];
        $usuario_editar = $fila["usuario"];
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_POST["id_usuario"] ?? "";
    $nombre = trim($_POST["nombre"] ?? "");
    $usuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";
    if ($nombre == "" || $usuario == "" || ($id_usuario == "" && $password == "")) {
        $mensaje = "Completá todos los campos obligatorios.";
        $tipo_alerta = "danger";
    } else {
        try {
            if ($id_usuario == "") {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, usuario, password) VALUES (?, ?, ?)");
                $stmt->execute([$nombre, $usuario, $password_hash]);
                $mensaje = "Usuario agregado correctamente.";
            } else {
                if ($password != "") {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, usuario = ?, password = ? WHERE id_usuario = ?");
                    $stmt->execute([$nombre, $usuario, $password_hash, $id_usuario]);
                } else {
                    $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, usuario = ? WHERE id_usuario = ?");
                    $stmt->execute([$nombre, $usuario, $id_usuario]);
                }
                $mensaje = "Usuario actualizado correctamente.";
            }
            $tipo_alerta = "success";
        } catch (PDOException $e) {
            $mensaje = "Error: ese nombre de usuario ya existe.";
            $tipo_alerta = "danger";
        }
    }
}
$usuarios = $conexion->query("SELECT id_usuario, nombre, usuario FROM usuarios ORDER BY id_usuario DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Taller de Pachi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include "navbar.php"; ?>
    <div class="container">
        <h3 class="mb-4">Gestión de Usuarios</h3>
        <?php if ($mensaje != ""): ?>
            <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show">
                <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <?php echo ($id_editar != "") ? "Editar Usuario" : "Nuevo Usuario"; ?>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="usuarios.php">
                            <input type="hidden" name="id_usuario" value="<?php echo $id_editar; ?>">
                            <div class="mb-3">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" name="nombre" class="form-control" required
                                       value="<?php echo htmlspecialchars($nombre_editar); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Usuario (para login)</label>
                                <input type="text" name="usuario" class="form-control" required
                                       value="<?php echo htmlspecialchars($usuario_editar); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="password" class="form-control"
                                       <?php echo ($id_editar == "") ? "required" : ""; ?>>
                                <?php if ($id_editar != ""): ?>
                                    <div class="form-text">Dejala vacía si no querés cambiarla.</div>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <?php echo ($id_editar != "") ? "Guardar Cambios" : "Agregar Usuario"; ?>
                            </button>
                            <?php if ($id_editar != ""): ?>
                                <a href="usuarios.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">Listado de Usuarios</div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Usuario</th>
                                    <th style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usu): ?>
                                    <tr>
                                        <td><?php echo $usu["id_usuario"]; ?></td>
                                        <td><?php echo htmlspecialchars($usu["nombre"]); ?></td>
                                        <td><?php echo htmlspecialchars($usu["usuario"]); ?></td>
                                        <td>
                                            <a href="usuarios.php?editar=<?php echo $usu["id_usuario"]; ?>" class="btn btn-warning btn-sm">Editar</a>
                                            <a href="usuarios.php?eliminar=<?php echo $usu["id_usuario"]; ?>" class="btn btn-danger btn-sm"
                                               onclick="return confirm('¿Seguro que querés eliminar este usuario?');">Eliminar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>