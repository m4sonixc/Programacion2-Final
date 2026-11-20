<?php
require_once "verificarsesion.php";
require_once "config/conexion.php";

$id_venta = intval($_GET["id"] ?? 0);
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_venta = intval($_POST["id_venta"]);
    $id_cliente = intval($_POST["id_cliente"] ?? 0);
    $productos_ids = $_POST["id_producto"] ?? [];
    $cantidades = $_POST["cantidad"] ?? [];
    $precios = $_POST["precio"] ?? [];
    if ($id_cliente <= 0 || count($productos_ids) == 0) {
        $error = "Debés seleccionar un cliente y dejar al menos un producto.";
    } else {
        $total_venta = 0;
        for ($i = 0; $i < count($productos_ids); $i++) {
            $total_venta += intval($cantidades[$i]) * floatval($precios[$i]);
        }
        $conexion->beginTransaction();
        try {
            $stmt_old = $conexion->prepare("SELECT id_producto, cantidad FROM detalle_ventas WHERE id_venta = ?");
            $stmt_old->execute([$id_venta]);
            $items_old = $stmt_old->fetchAll();
            $stmt_st = $conexion->prepare("UPDATE productos SET stock = stock + ? WHERE id_producto = ?");
            foreach ($items_old as $item) {
                $stmt_st->execute([$item["cantidad"], $item["id_producto"]]);
            }
            $stmt_del = $conexion->prepare("DELETE FROM detalle_ventas WHERE id_venta = ?");
            $stmt_del->execute([$id_venta]);
            $stmt_upd = $conexion->prepare("UPDATE ventas SET id_cliente = ?, total = ? WHERE id_venta = ?");
            $stmt_upd->execute([$id_cliente, $total_venta, $id_venta]);
            $stmt_det = $conexion->prepare("INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmt_stock = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");
        
            for ($i = 0; $i < count($productos_ids); $i++) {
                $id_prod = intval($productos_ids[$i]);
                $cant = intval($cantidades[$i]);
                $prec = floatval($precios[$i]);
                $sub = $cant * $prec;

                $stmt_det->execute([$id_venta, $id_prod, $cant, $prec, $sub]);
                $stmt_stock->execute([$cant, $id_prod]);
            }
            $conexion->commit();
            header("Location: ver_venta.php?id=" . $id_venta);
            exit();
        } catch (Exception $e) {
            $conexion->rollBack();
            $error = "Ocurrió un error al actualizar la venta.";
        }
    }
}
$stmt_v = $conexion->prepare("SELECT * FROM ventas WHERE id_venta = ?");
$stmt_v->execute([$id_venta]);
$venta = $stmt_v->fetch();
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
$clientes = $conexion->query("SELECT id_cliente, nombre, apellido FROM clientes ORDER BY nombre")->fetchAll();
$productos = $conexion->query("SELECT id_producto, nombre, precio, stock FROM productos ORDER BY nombre")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Venta #<?php echo $id_venta; ?> - Taller de Pachi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include "navbar.php"; ?>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Editar Venta #<?php echo $id_venta; ?></h3>
            <a href="ventas.php" class="btn btn-secondary">Volver a Ventas</a>
        </div>
        <?php if ($error != ""): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="editar_venta.php?id=<?php echo $id_venta; ?>">
            <input type="hidden" name="id_venta" value="<?php echo $id_venta; ?>">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">Datos de la Venta (Cabecera)</div>
                <div class="card-body">
                    <label class="form-label">Cliente</label>
                    <select name="id_cliente" class="form-select" required>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?php echo $c["id_cliente"]; ?>"
                                <?php echo ($c["id_cliente"] == $venta["id_cliente"]) ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($c["nombre"] . " " . $c["apellido"]); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning">Productos de la Venta (Detalle)</div>
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Producto</label>
                            <select id="selectProducto" class="form-select" onchange="cargarPrecio()">
                                <option value="" data-precio="0">-- Seleccionar Producto --</option>
                                <?php foreach ($productos as $p): ?>
                                    <option value="<?php echo $p["id_producto"]; ?>" data-precio="<?php echo $p["precio"]; ?>">
                                        <?php echo htmlspecialchars($p["nombre"]) . " (Stock: " . $p["stock"] . ")"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Cantidad</label>
                            <input type="number" id="inputCantidad" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Precio Unitario ($)</label>
                            <input type="number" id="inputPrecio" class="form-control" step="0.01" min="0" value="0">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-success w-100" onclick="agregarProducto()">+ Agregar</button>
                        </div>
                    </div>
                    <table class="table table-bordered mt-4">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Subtotal</th>
                                <th style="width: 90px;">Quitar</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDetalle">
                            <?php foreach ($detalles as $d): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($d["producto_nombre"]); ?>
                                        <input type="hidden" name="id_producto[]" value="<?php echo $d["id_producto"]; ?>">
                                    </td>
                                    <td>
                                        <?php echo $d["cantidad"]; ?>
                                        <input type="hidden" name="cantidad[]" value="<?php echo $d["cantidad"]; ?>">
                                    </td>
                                    <td>
                                        $<?php echo number_format($d["precio_unitario"], 2, ".", ""); ?>
                                        <input type="hidden" name="precio[]" value="<?php echo $d["precio_unitario"]; ?>">
                                    </td>
                                    <td class="subtotal-celda" data-subtotal="<?php echo $d["subtotal"]; ?>">
                                        $<?php echo number_format($d["subtotal"], 2, ".", ""); ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">Quitar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="text-end fs-4">
                        Total: <strong>$<span id="totalTexto"><?php echo number_format($venta["total"], 2, ".", ""); ?></span></strong>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-warning btn-lg w-100 mb-5">Guardar Cambios</button>
        </form>
    </div>
    <script>
        function cargarPrecio() {
            let select = document.getElementById("selectProducto");
            let opcion = select.options[select.selectedIndex];
            document.getElementById("inputPrecio").value = opcion.getAttribute("data-precio");
        }
        function agregarProducto() {
            let select = document.getElementById("selectProducto");
            let idProducto = select.value;
            let nombreProducto = select.options[select.selectedIndex].text;
            let cantidad = parseInt(document.getElementById("inputCantidad").value);
            let precio = parseFloat(document.getElementById("inputPrecio").value);
            if (idProducto === "" || cantidad <= 0 || isNaN(precio)) {
                alert("Seleccioná un producto, cantidad válida y precio.");
                return;
            }
            let subtotal = cantidad * precio;
            let tbody = document.getElementById("tablaDetalle");
            let fila = document.createElement("tr");
            fila.innerHTML = `
                <td>${nombreProducto}<input type="hidden" name="id_producto[]" value="${idProducto}"></td>
                <td>${cantidad}<input type="hidden" name="cantidad[]" value="${cantidad}"></td>
                <td>$${precio.toFixed(2)}<input type="hidden" name="precio[]" value="${precio}"></td>
                <td class="subtotal-celda" data-subtotal="${subtotal}">$${subtotal.toFixed(2)}</td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">Quitar</button></td>
            `;
            tbody.appendChild(fila);
            calcularTotal();
            select.value = "";
            document.getElementById("inputCantidad").value = 1;
            document.getElementById("inputPrecio").value = 0;
        }
        function eliminarFila(boton) {
            boton.closest("tr").remove();
            calcularTotal();
        }
        function calcularTotal() {
            let celdas = document.querySelectorAll(".subtotal-celda");
            let total = 0;
            celdas.forEach(function(celda) {
                total += parseFloat(celda.getAttribute("data-subtotal"));
            });
            document.getElementById("totalTexto").innerText = total.toFixed(2);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>