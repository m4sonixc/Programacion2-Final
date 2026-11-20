<?php
require_once "verificarsesion.php";
require_once "config/conexion.php";

$id_venta = intval($_GET["id"] ?? 0);
$sql_cab = "SELECT v.*, c.nombre AS cli_nombre, c.apellido AS cli_apellido, c.telefono,
                   u.nombre AS vendedor
            FROM ventas v
            INNER JOIN clientes c ON v.id_cliente = c.id_cliente
            INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
            WHERE v.id_venta = ?";
$stmt_cab = $conexion->prepare($sql_cab);
$stmt_cab->execute([$id_venta]);
$venta = $stmt_cab->fetch();
if (!$venta) {
    header("Location: ventas.php");
    exit();
}
$sql_det = "SELECT d.*, p.nombre AS producto_nombre
            FROM detalle_ventas d
            INNER JOIN productos p ON d.id_producto = p.id_producto
            WHERE d.id_venta = ?";
$stmt_det = $conexion->prepare($sql_det);
$stmt_det->execute([$id_venta]);
$detalles = $stmt_det->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Venta #<?php echo $id_venta; ?> - Taller de Pachi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include "navbar.php"; ?>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Detalle de Venta #<?php echo $venta["id_venta"]; ?></h3>
            <div>
                <a href="editar_venta.php?id=<?php echo $id_venta; ?>" class="btn btn-warning">Editar</a>
                <a href="ventas.php" class="btn btn-secondary">Volver</a>
            </div>
        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">Datos de la Venta</div>
            <div class="card-body row">
                <div class="col-md-4">
                    <strong>Cliente:</strong><br>
                    <?php echo htmlspecialchars($venta["cli_nombre"] . " " . $venta["cli_apellido"]); ?><br>
                    <small class="text-muted">Tel: <?php echo htmlspecialchars($venta["telefono"]); ?></small>
                </div>
                <div class="col-md-4">
                    <strong>Fecha:</strong><br>
                    <?php echo date("d/m/Y H:i", strtotime($venta["fecha"])); ?>
                </div>
                <div class="col-md-4">
                    <strong>Atendido por:</strong><br>
                    <?php echo htmlspecialchars($venta["vendedor"]); ?>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header">Productos Vendidos</div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $d): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($d["producto_nombre"]); ?></td>
                                <td><?php echo $d["cantidad"]; ?></td>
                                <td>$<?php echo number_format($d["precio_unitario"], 2, ",", "."); ?></td>
                                <td>$<?php echo number_format($d["subtotal"], 2, ",", "."); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-secondary">
                            <th colspan="3" class="text-end">TOTAL DE LA VENTA:</th>
                            <th>$<?php echo number_format($venta["total"], 2, ",", "."); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>