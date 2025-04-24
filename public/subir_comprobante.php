<?php
require_once '../database/database.php';
session_start();

// Validar sesión
if (!isset($_SESSION['cliente_email'])) {
    die("Acceso denegado. Debes iniciar sesión.");
}

$conn = Database::conectar();
$message = '';

// Evitar caché del navegador
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Descargar factura
$facturaParaDescargar = null;
if (isset($_SESSION['descargar_factura'])) {
    $facturaNombre = basename($_SESSION['descargar_factura']);
    $ruta = "../public/facturas/" . $facturaNombre;
    if (file_exists($ruta)) {
        $facturaParaDescargar = $ruta;
        unset($_SESSION['descargar_factura']);
    }
}

// Manejo de comprobante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['orden_id'])) {
    $ordenId = $_POST['orden_id'];
    $file = $_FILES['comprobante'];

    if ($file && $file['error'] === 0) {
        $uploadDir = '../public/comprobantes/';
        $filename = basename($file['name']);
        $targetPath = $uploadDir . time() . "_" . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $stmt = $conn->prepare("UPDATE ordenes SET comprobante_path = ?, estado = 'en revisión' WHERE id = ?");
            $stmt->execute([$targetPath, $ordenId]);
            $message = '✅ Comprobante subido correctamente. Tu compra está en revisión.';
        } else {
            $message = '❌ Error al subir el comprobante.';
        }
    } else {
        $message = '❌ Archivo no válido o vacío.';
    }
}

// Recargar órdenes directamente desde la base
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 900px; margin-top: 40px; }
        .card { margin-bottom: 20px; }
        header { background-color: #e0e0e0; padding: 10px 0; margin-bottom: 20px; }
        .logo { height: 50px; }
    </style>
</head>
<body>

<?php if ($facturaParaDescargar): ?>
<script>
    window.onload = function() {
        const link = document.createElement('a');
        link.href = '<?= $facturaParaDescargar ?>';
        link.download = '<?= basename($facturaParaDescargar) ?>';
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };
</script>
<?php endif; ?>

<header>
    <div class="container d-flex justify-content-between align-items-center">
        <img src="./assets/images/LogoEmpresa.jpg" class="logo" alt="Logo">
        <a href="IndexCliente.php" class="btn btn-dark">← Volver a la tienda</a>
    </div>
</header>

<div class="container bg-white p-4 shadow">
    <h2 class="mb-4 text-center">Mis Compras</h2>

    <?php if ($message): ?>
        <div class="alert alert-info text-center font-weight-bold"> <?= $message ?> </div>
    <?php endif; ?>

    <?php if (empty($ordenes)): ?>
        <p class="text-center text-muted">Aún no tienes compras registradas.</p>
    <?php endif; ?>

    <?php foreach ($ordenes as $orden): ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Orden #<?= $orden['id'] ?> - <strong><?= number_format($orden['total'], 0, ',', '.') ?> COP</strong></h5>
                <p><strong>Fecha:</strong> <?= $orden['fecha'] ?></p>
                <p><strong>Estado:</strong>
                    <?php if ($orden['estado'] === 'aprobado'): ?>
                        <span class="badge badge-success">Aprobado</span>
                    <?php elseif ($orden['estado'] === 'rechazado'): ?>
                        <span class="badge badge-danger">Rechazado</span>
                    <?php elseif ($orden['estado'] === 'en revisión'): ?>
                        <span class="badge badge-warning">En Revisión</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Pendiente</span>
                    <?php endif; ?>
                </p>

                <?php
                    $facturaFile = "../public/facturas/factura_{$orden['id']}.pdf";
                    if (file_exists($facturaFile)):
                        $facturaLink = $facturaFile;
                ?>
                    <p><strong>Factura:</strong> <a href="<?= $facturaLink ?>" class="btn btn-outline-info btn-sm" download>📄 Descargar Factura</a></p>
                <?php endif; ?>

                <?php if (empty($orden['comprobante_path'])): ?>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="orden_id" value="<?= $orden['id'] ?>">
                        <div class="form-group">
                            <label>Subir comprobante de pago (PDF o Imagen)</label>
                            <input type="file" name="comprobante" accept=".pdf,image/*" class="form-control-file" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar Comprobante</button>
                    </form>
                <?php else: ?>
                    <p><strong>Comprobante:</strong> <a href="<?= $orden['comprobante_path'] ?>" target="_blank">Ver archivo</a></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
