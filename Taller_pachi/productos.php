<?php
require_once "verificarsesion.php";
require_once "config/conexion.php";

$mensaje = "";
$tipo_alerta = "";
$id_editar = "";
$nombre_editar = "";
$descripcion_editar = "";
$precio_editar = "";
$stock_editar = "";
$id_categoria_editar = "";
if (isset($_GET["eliminar"])) {
    $id = intval($_GET["eliminar"]);
    try {
        $stmt = $conexion->prepare("DELETE FROM productos WHERE id_producto = ?");
        $stmt->execute([$id]);
        $mensaje = "Producto eliminado correctamente.";
        $tipo_alerta = "success";
    } catch (PDOException $e) {
        $mensaje = "No se puede eliminar el producto porque ya está incluido en una venta.";
        $tipo_alerta = "danger";
    }
}
if (isset($_GET["editar"])) {
    $id_editar = intval($_GET["editar"]);
    $stmt = $conexion->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $stmt->execute([$id_editar]);
    if ($fila = $stmt->fetch()) {
        $nombre_editar = $fila["nombre"];
        $descripcion_editar = $fila["descripcion"];
        $precio_editar = $fila["precio"];
        $stock_editar = $fila["stock"];
        $id_categoria_editar = $fila["id_categoria"];
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto = $_POST["id_producto"] ?? "";
    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $precio = floatval($_POST["precio"] ?? -1);
    $stock = intval($_POST["stock"] ?? -1);
    $id_categoria = intval($_POST["id_categoria"] ?? 0);
    if ($nombre == "" || $id_categoria <= 0 || $precio < 0 || $stock < 0) {
        $mensaje = "Completá todos los campos obligatorios con valores válidos.";
        $tipo_alerta = "danger";
    } else {
        if ($id_producto == "") {
            $stmt = $conexion->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, id_categoria) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $descripcion, $precio, $stock, $id_categoria]);
            $mensaje = "Producto agregado correctamente.";
        } else {
            $stmt = $conexion->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, id_categoria = ? WHERE id_producto = ?");
            $stmt->execute([$nombre, $descripcion, $precio, $stock, $id_categoria, $id_producto]);
            $mensaje = "Producto actualizado correctamente.";
        }
        $tipo_alerta = "success";
    }
}
$categorias = $conexion->query("SELECT id_categoria, nombre FROM categorias ORDER BY nombre")->fetchAll();
$sql_lista = "SELECT p.*, c.nombre AS nombre_categoria 
              FROM productos p 
              INNER JOIN categorias c ON p.id_categoria = c.id_categoria 
              ORDER BY p.id_producto DESC";
$productos = $conexion->query($sql_lista)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Taller de Pachi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include "navbar.php"; ?>
    <div class="container">
        <h3 class="mb-4">Gestión de Productos</h3>
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
                        <?php echo ($id_editar != "") ? "Editar Producto" : "Nuevo Producto"; ?>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="productos.php">
                            <input type="hidden" name="id_producto" value="<?php echo $id_editar; ?>">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" required
                                       value="<?php echo htmlspecialchars($nombre_editar); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Categoría</label>
                                <select name="id_categoria" class="form-select" required>
                                    <option value="">-- Seleccionar --</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?php echo $cat["id_categoria"]; ?>"
                                            <?php echo ($cat["id_categoria"] == $id_categoria_editar) ? "selected" : ""; ?>>
                                            <?php echo htmlspecialchars($cat["nombre"]); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label">Precio ($)</label>
                                    <input type="number" step="0.01" min="0" name="precio" class="form-control" required
                                           value="<?php echo $precio_editar; ?>">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Stock</label>
                                    <input type="number" min="0" name="stock" class="form-control" required
                                           value="<?php echo $stock_editar; ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="2"><?php echo htmlspecialchars($descripcion_editar); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <?php echo ($id_editar != "") ? "Guardar Cambios" : "Agregar Producto"; ?>
                            </button>
                            <?php if ($id_editar != ""): ?>
                                <a href="productos.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">Listado de Productos</div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $prod): ?>
                                    <tr>
                                        <td><?php echo $prod["id_producto"]; ?></td>
                                        <td><?php echo htmlspecialchars($prod["nombre"]); ?></td>
                                        <td><?php echo htmlspecialchars($prod["nombre_categoria"]); ?></td>
                                        <td>$<?php echo number_format($prod["precio"], 2, ",", "."); ?></td>
                                        <td><?php echo $prod["stock"]; ?></td>
                                        <td>
                                            <a href="productos.php?editar=<?php echo $prod["id_producto"]; ?>" class="btn btn-warning btn-sm">Editar</a>
                                            <a href="productos.php?eliminar=<?php echo $prod["id_producto"]; ?>" class="btn btn-danger btn-sm"
                                               onclick="return confirm('¿Seguro que querés eliminar este producto?');">Eliminar</a>
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