<?php
require_once '../database/database.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;

session_start();

// Verificamos que el cliente esté logueado
if (!isset($_SESSION['cliente_email'])) {
    header("Location: ClienteLogin.php");
    exit();
  }
  
// Recibir productos
$productos = isset($_POST['productos_json']) ? json_decode($_POST['productos_json'], true) : [];

$total = 0;
foreach ($productos as $p) {
    $precio = isset($p['price']) ? floatval($p['price']) : 0;
    $cantidad = isset($p['quantity']) ? intval($p['quantity']) : 1;
    $total += $precio * $cantidad;
}

$conn = Database::conectar();

$nombre = $_SESSION['cliente_nombre'];
$gmail = $_SESSION['cliente_email'];
$cedula = $direccion = $telefono = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cedula'])) {
    $cedula = $_POST['cedula'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];

    // Codificar los productos en JSON para la base de datos
    $productosJson = json_encode($productos);

    $stmt = $conn->prepare("INSERT INTO ordenes (nombre_cliente, cedula, direccion, telefono, email, productos, total, estado) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente')");
    $stmt->execute([$nombre, $cedula, $direccion, $telefono, $gmail, $productosJson, $total]);
    $ordenId = $conn->lastInsertId();

    // Logo en base64
    $logoPath = realpath('../public/assets/images/LogoEmpresa.jpg');
    $logoBase64 = base64_encode(file_get_contents($logoPath));
    $logoSrc = 'data:image/jpeg;base64,' . $logoBase64;

    // QR en base64
    $qrPath = realpath('../public/assets/images/QR.jpeg');
    $qrBase64 = base64_encode(file_get_contents($qrPath));
    $qrSrc = 'data:image/jpeg;base64,' . $qrBase64;

    // HTML de la factura
    $html = '
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .logo { max-height: 80px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
    </style>
    <div class="header">
        <img src="' . $logoSrc . '" class="logo"><br>
        <h2>MotoRepuestos Melo</h2>
        <p>Factura de Compra</p>
    </div>';

    $html .= "<p><strong>Cliente:</strong> $nombre<br><strong>Cédula:</strong> $cedula<br><strong>Teléfono:</strong> $telefono<br><strong>Email:</strong> $gmail<br><strong>Dirección:</strong> $direccion</p>";
    $html .= "<table><tr><th>Producto</th><th>Cantidad</th><th>Precio</th></tr>";
    foreach ($productos as $p) {
        $nombreProd = htmlspecialchars($p['name']);
        $cantidad = isset($p['quantity']) ? intval($p['quantity']) : 1;
        $precio = isset($p['price']) ? floatval($p['price']) : 0;
        $html .= "<tr><td>$nombreProd</td><td>$cantidad</td><td>$" . number_format($precio, 0, ',', '.') . "</td></tr>";
    }
    $html .= "</table><br><h3>Total: $" . number_format($total, 0, ',', '.') . "</h3>";

    // Agregar el QR
    $html .= "<div style='margin-top: 30px; text-align: center;'>
                <p><strong>Escanea este QR y sube el comprobante de pago luego.</strong></p>
                <img src='$qrSrc' style='max-width: 150px;'>
              </div>";

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->render();

    $nombreFactura = "factura_{$ordenId}.pdf";
    $facturaPath = "../public/facturas/" . $nombreFactura;
    file_put_contents($facturaPath, $dompdf->output());

    // Guardar ruta de la factura en la base de datos
    $stmt = $conn->prepare("UPDATE ordenes SET factura_path = ? WHERE id = ?");
    $stmt->execute([$facturaPath, $ordenId]);

    $_SESSION['factura_generada'] = $nombreFactura;
    $_SESSION['descargar_factura'] = $nombreFactura;

    // Limpiar carrito del cliente actual
    unset($_SESSION['carrito']);
    echo "
<script>
    localStorage.removeItem('cart_' + '" . $_SESSION['cliente_email'] . "');
    window.location.href = 'subir_comprobante.php';
</script>
";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Compra</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 700px; margin-top: 50px; }
        .list-group-item img { width: 50px; margin-right: 10px; }
        header { background-color: #e0e0e0; padding: 10px 0; }
        .logo { height: 50px; }
    </style>
</head>
<body>

<header>
    <div class="container d-flex justify-content-between align-items-center">
        <img src="./assets/images/LogoEmpresa.jpg" class="logo" alt="Logo">
        <a href="IndexCliente.php" class="btn btn-dark">← Volver a la tienda</a>
    </div>
</header>

<div class="container bg-white p-5 shadow">
    <h2 class="mb-4 text-center">Finalizar Compra</h2>
    <form method="POST">
        <div class="form-group">
            <label for="nombre">Nombre Completo</label>
            <input type="text" class="form-control" id="nombre" value="<?= htmlspecialchars($_SESSION['cliente_nombre']) ?>" disabled>
        </div>
        <div class="form-group">
            <label for="cedula">Cédula</label>
            <input type="text" class="form-control" id="cedula" name="cedula" required>
        </div>
        <div class="form-group">
            <label for="direccion">Dirección de Entrega</label>
            <input type="text" class="form-control" id="direccion" name="direccion" required>
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono de Contacto</label>
            <input type="text" class="form-control" id="telefono" name="telefono" required>
        </div>
        <div class="form-group">
            <label for="gmail">Correo Electrónico</label>
            <input type="email" class="form-control" id="gmail" value="<?= htmlspecialchars($_SESSION['cliente_email']) ?>" disabled>
        </div>

        <h5 class="mt-4">Productos Adquiridos</h5>
        <ul class="list-group mb-3">
            <?php foreach ($productos as $p): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="<?= htmlspecialchars($p['imagen']) ?>" alt="img">
                        <?= htmlspecialchars($p['name']) ?> x <?= isset($p['quantity']) ? $p['quantity'] : 1 ?>
                    </div>
                    <span>$<?= number_format(floatval($p['price']), 0, ',', '.') ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <h5 class="text-right">Total a Pagar: <strong>$<?= number_format($total, 0, ',', '.') ?></strong></h5>

        <input type="hidden" name="productos_json" value='<?= json_encode($productos) ?>'>
        <input type="hidden" name="total" value='<?= $total ?>'>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success">Generar Factura</button>
        </div>
    </form>
</div>
</body>
</html>
