<?php
require_once '../database/database.php';
session_start();

// Validar sesión
if (!isset($_SESSION['cliente_email'])) {
    die("Acceso denegado. Debes iniciar sesión.");
}

$conn = Database::conectar();

// Notificación de acción
$notificacion = '';
if (isset($_SESSION['notificacion'])) {
    $notificacion = $_SESSION['notificacion'];
    unset($_SESSION['notificacion']);
}

// Consultar órdenes del cliente
$emailCliente = $_SESSION['cliente_email'];
$stmt = $conn->prepare("SELECT * FROM ordenes WHERE email = ? ORDER BY fecha DESC");
$stmt->execute([$emailCliente]);
$ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Compras</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- No caché -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 900px; margin-top: 40px; }
        .card { margin-bottom: 20px; }
        header { background-color: #e0e0e0; padding: 10px 0; margin-bottom: 20px; }
        .logo { height: 50px; }
        .producto { display: flex; align-items: center; margin-bottom: 10px; }
        .producto img { width: 50px; height: 50px; object-fit: cover; margin-right: 10px; border-radius: 5px; }
    </style>
</head>
<body>

<header>
    <div class="container d-flex justify-content-between align-items-center">
        <img src="./assets/images/LogoEmpresa.jpg" class="logo" alt="Logo">
        <a href="IndexCliente.php" class="btn btn-dark">← Volver a la tienda</a>
    </div>
</header>

<div class="container bg-white p-4 shadow">
    <h2 class="mb-4 text-center">Historial de Compras</h2>

    <?php if (!empty($notificacion)): ?>
        <div class="alert alert-info text-center font-weight-bold">
            <?= htmlspecialchars($notificacion) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($ordenes)): ?>
        <p class="text-center text-muted">Aún no tienes compras registradas.</p>
    <?php endif; ?>

    <?php foreach ($ordenes as $orden): ?>
        <?php $productos = json_decode($orden['productos'], true); ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Orden #<?= $orden['id'] ?> - <strong><?= number_format($orden['total'], 0, ',', '.') ?> COP</strong></h5>
                <p><strong>Fecha:</strong> <?= $orden['fecha'] ?></p>
                <p><strong>Estado:</strong>
                    <?php if ($orden['estado'] === 'aprobado'): ?>
                        <span class="badge badge-success">✅ Aprobado</span>
                    <?php elseif ($orden['estado'] === 'rechazado'): ?>
                        <span class="badge badge-danger">❌ Rechazado</span>
                    <?php elseif ($orden['estado'] === 'en revisión'): ?>
                        <span class="badge badge-warning">⌛ En Revisión</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">⏳ Pendiente</span>
                    <?php endif; ?>
                </p>

                <?php if (!empty($productos) && is_array($productos)): ?>
                    <p><strong>Productos comprados:</strong></p>
                    <?php foreach ($productos as $producto): ?>
                        <div class="producto">
                            <img src="<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['name']) ?>">
                            <span><?= htmlspecialchars($producto['name']) ?> x <?= $producto['quantity'] ?? 1 ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No se pudieron cargar los productos.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
