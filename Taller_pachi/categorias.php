<?php
require_once "verificarsesion.php";
require_once "config/conexion.php";

$mensaje = "";
$tipo_alerta = "";
$id_editar = "";
$nombre_editar = "";
$descripcion_editar = "";
if (isset($_GET["eliminar"])) {
    $id = intval($_GET["eliminar"]);
    try {
        $stmt = $conexion->prepare("DELETE FROM categorias WHERE id_categoria = ?");
        $stmt->execute([$id]);
        $mensaje = "Categoría eliminada correctamente.";
        $tipo_alerta = "success";
    } catch (PDOException $e) {
        $mensaje = "No se puede eliminar la categoría porque tiene productos asociados.";
        $tipo_alerta = "danger";
    }
}
if (isset($_GET["editar"])) {
    $id_editar = intval($_GET["editar"]);
    $stmt = $conexion->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
    $stmt->execute([$id_editar]);
    if ($fila = $stmt->fetch()) {
        $nombre_editar = $fila["nombre"];
        $descripcion_editar = $fila["descripcion"];
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_categoria = $_POST["id_categoria"] ?? "";
    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    if ($nombre == "") {
        $mensaje = "El nombre de la categoría es obligatorio.";
        $tipo_alerta = "danger";
    } else {
        if ($id_categoria == "") {
            $stmt = $conexion->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
            $stmt->execute([$nombre, $descripcion]);
            $mensaje = "Categoría agregada correctamente.";
        } else {
            $stmt = $conexion->prepare("UPDATE categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?");
            $stmt->execute([$nombre, $descripcion, $id_categoria]);
            $mensaje = "Categoría actualizada correctamente.";
        }
        $tipo_alerta = "success";
    }
}
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY id_categoria DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - Taller de Pachi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include "navbar.php"; ?>
    <div class="container">
        <h3 class="mb-4">Gestión de Categorías</h3>
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
                        <?php echo ($id_editar != "") ? "Editar Categoría" : "Nueva Categoría"; ?>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="categorias.php">
                            <input type="hidden" name="id_categoria" value="<?php echo $id_editar; ?>">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" required
                                       value="<?php echo htmlspecialchars($nombre_editar); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($descripcion_editar); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <?php echo ($id_editar != "") ? "Guardar Cambios" : "Agregar Categoría"; ?>
                            </button>
                            <?php if ($id_editar != ""): ?>
                                <a href="categorias.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">Listado de Categorías</div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th style="width: 160px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categorias as $cat): ?>
                                    <tr>
                                        <td><?php echo $cat["id_categoria"]; ?></td>
                                        <td><?php echo htmlspecialchars($cat["nombre"]); ?></td>
                                        <td><?php echo htmlspecialchars($cat["descripcion"]); ?></td>
                                        <td>
                                            <a href="categorias.php?editar=<?php echo $cat["id_categoria"]; ?>" class="btn btn-warning btn-sm">Editar</a>
                                            <a href="categorias.php?eliminar=<?php echo $cat["id_categoria"]; ?>" class="btn btn-danger btn-sm"
                                               onclick="return confirm('¿Seguro que querés eliminar esta categoría?');">Eliminar</a>
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