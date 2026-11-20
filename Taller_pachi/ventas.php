<?php
require_once "verificarsesion.php";
require_once "config/conexion.php";

$mensaje = "";
$tipo_alerta = "";
if (isset($_GET["eliminar"])) {
    $id_venta = intval($_GET["eliminar"]);
    $conexion->beginTransaction();
    try {
        $stmt_det = $conexion->prepare("SELECT id_producto, cantidad FROM detalle_ventas WHERE id_venta = ?");
        $stmt_det->execute([$id_venta]);
        $items = $stmt_det->fetchAll();
        $stmt_stock = $conexion->prepare("UPDATE productos SET stock = stock + ? WHERE id_producto = ?");
        foreach ($items as $item) {
            $stmt_stock->execute([$item["cantidad"], $item["id_producto"]]);
        }
        $stmt_del_det = $conexion->prepare("DELETE FROM detalle_ventas WHERE id_venta = ?");
        $stmt_del_det->execute([$id_venta]);
        $stmt_del_venta = $conexion->prepare("DELETE FROM ventas WHERE id_venta = ?");
        $stmt_del_venta->execute([$id_venta]);
        $conexion->commit();
        $mensaje = "Venta #$id_venta eliminada correctamente y stock restaurado.";
        $tipo_alerta = "success";
    } catch (Exception $e) {
        $conexion->rollBack();
        $mensaje = "Error al eliminar la venta.";
        $tipo_alerta = "danger";
    }
}
$sql_ventas = "SELECT v.id_venta, v.fecha, v.total, 
                      c.nombre AS cli_nombre, c.apellido AS cli_apellido,
                      u.nombre AS vendedor
               FROM ventas v
               INNER JOIN clientes c ON v.id_cliente = c.id_cliente
               INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
               ORDER BY v.id_venta DESC";
$ventas = $conexion->query($sql_ventas)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - Taller de Pachi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include "navbar.php"; ?>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Gestión de Ventas</h3>
            <a href="nueva_venta.php" class="btn btn-primary">+ Nueva Venta</a>
        </div>
        <?php if ($mensaje != ""): ?>
            <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show">
                <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="card shadow-sm">
            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>N° Venta</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Total</th>
                            <th style="width: 240px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventas as $v): ?>
                            <tr>
                                <td>#<?php echo $v["id_venta"]; ?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($v["fecha"])); ?></td>
                                <td><?php echo htmlspecialchars($v["cli_nombre"] . " " . $v["cli_apellido"]); ?></td>
                                <td><?php echo htmlspecialchars($v["vendedor"]); ?></td>
                                <td><strong>$<?php echo number_format($v["total"], 2, ",", "."); ?></strong></td>
                                <td>
                                    <a href="ver_venta.php?id=<?php echo $v["id_venta"]; ?>" class="btn btn-info btn-sm text-white">Ver Detalle</a>
                                    <a href="editar_venta.php?id=<?php echo $v["id_venta"]; ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="ventas.php?eliminar=<?php echo $v["id_venta"]; ?>" class="btn btn-danger btn-sm"
                                       onclick="return confirm('¿Seguro que querés eliminar esta venta?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>