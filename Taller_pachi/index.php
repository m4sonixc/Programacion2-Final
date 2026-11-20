<?php
require_once "verificarsesion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Taller de Pachi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include "navbar.php"; ?>
    <div class="container">
        <div class="p-4 mb-4 bg-white rounded shadow-sm">
            <h2>Bienvenido al Sistema del Taller de Pachi</h2>
            <p class="text-muted mb-0">Seleccioná un módulo para comenzar a trabajar.</p>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Ventas</h5>
                        <p class="card-text">Registrar nuevas ventas, ver detalles, modificar o eliminar.</p>
                        <a href="ventas.php" class="btn btn-primary">Ir a Ventas</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Productos</h5>
                        <p class="card-text">Administrar repuestos, aceites, precios y stock del taller.</p>
                        <a href="productos.php" class="btn btn-secondary">Ir a Productos</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Clientes</h5>
                        <p class="card-text">Gestionar el listado de clientes del taller.</p>
                        <a href="clientes.php" class="btn btn-secondary">Ir a Clientes</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Categorías</h5>
                        <p class="card-text">Organizar los productos por categoría.</p>
                        <a href="categorias.php" class="btn btn-secondary">Ir a Categorías</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Usuarios</h5>
                        <p class="card-text">Administrar los usuarios con acceso al sistema.</p>
                        <a href="usuarios.php" class="btn btn-secondary">Ir a Usuarios</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>