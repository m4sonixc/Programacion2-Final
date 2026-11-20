<?php
require_once "verificarsesion.php";
require_once "config/conexion.php";

$mensaje = "";
$tipo_alerta = "";
$id_editar = "";
$nombre_editar = "";
$apellido_editar = "";
$telefono_editar = "";
$email_editar = "";
$direccion_editar = "";
if (isset($_GET["eliminar"])) {
    $id = intval($_GET["eliminar"]);
    try {
        $stmt = $conexion->prepare("DELETE FROM clientes WHERE id_cliente = ?");
        $stmt->execute([$id]);

        $mensaje = "Cliente eliminado correctamente.";
        $tipo_alerta = "success";
    } catch (PDOException $e) {
        $mensaje = "No se puede eliminar el cliente porque tiene ventas registradas.";
        $tipo_alerta = "danger";
    }
}
if (isset($_GET["editar"])) {
    $id_editar = intval($_GET["editar"]);
    $stmt = $conexion->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id_editar]);
    if ($fila = $stmt->fetch()) {
        $nombre_editar = $fila["nombre"];
        $apellido_editar = $fila["apellido"];
        $telefono_editar = $fila["telefono"];
        $email_editar = $fila["email"];
        $direccion_editar = $fila["direccion"];
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_cliente = $_POST["id_cliente"] ?? "";
    $nombre = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $direccion = trim($_POST["direccion"] ?? "");
    if ($nombre == "" || $apellido == "") {
        $mensaje = "Nombre y apellido son obligatorios.";
        $tipo_alerta = "danger";
    } else {
        if ($id_cliente == "") {
            $stmt = $conexion->prepare("INSERT INTO clientes (nombre, apellido, telefono, email, direccion) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $apellido, $telefono, $email, $direccion]);
            $mensaje = "Cliente agregado correctamente.";
        } else {
            $stmt = $conexion->prepare("UPDATE clientes SET nombre = ?, apellido = ?, telefono = ?, email = ?, direccion = ? WHERE id_cliente = ?");
            $stmt->execute([$nombre, $apellido, $telefono, $email, $direccion, $id_cliente]);
            $mensaje = "Cliente actualizado correctamente.";
        }
        $tipo_alerta = "success";
    }
}
$clientes = $conexion->query("SELECT * FROM clientes ORDER BY id_cliente DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Taller de Pachi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include "navbar.php"; ?>
    <div class="container">
        <h3 class="mb-4">Gestión de Clientes</h3>
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
                        <?php echo ($id_editar != "") ? "Editar Cliente" : "Nuevo Cliente"; ?>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="clientes.php">
                            <input type="hidden" name="id_cliente" value="<?php echo $id_editar; ?>">
                            <div class="mb-2">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" required
                                       value="<?php echo htmlspecialchars($nombre_editar); ?>">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido" class="form-control" required
                                       value="<?php echo htmlspecialchars($apellido_editar); ?>">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control"
                                       value="<?php echo htmlspecialchars($telefono_editar); ?>">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                       value="<?php echo htmlspecialchars($email_editar); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="direccion" class="form-control"
                                       value="<?php echo htmlspecialchars($direccion_editar); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <?php echo ($id_editar != "") ? "Guardar Cambios" : "Agregar Cliente"; ?>
                            </button>
                            <?php if ($id_editar != ""): ?>
                                <a href="clientes.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">Listado de Clientes</div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre y Apellido</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientes as $cli): ?>
                                    <tr>
                                        <td><?php echo $cli["id_cliente"]; ?></td>
                                        <td><?php echo htmlspecialchars($cli["nombre"] . " " . $cli["apellido"]); ?></td>
                                        <td><?php echo htmlspecialchars($cli["telefono"]); ?></td>
                                        <td><?php echo htmlspecialchars($cli["email"]); ?></td>
                                        <td>
                                            <a href="clientes.php?editar=<?php echo $cli["id_cliente"]; ?>" class="btn btn-warning btn-sm">Editar</a>
                                            <a href="clientes.php?eliminar=<?php echo $cli["id_cliente"]; ?>" class="btn btn-danger btn-sm"
                                               onclick="return confirm('¿Seguro que querés eliminar este cliente?');">Eliminar</a>
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