<?php
session_start();
require_once '../database/database.php';
$conn = Database::conectar();
$ordenes = $conn->query("SELECT * FROM ordenes ORDER BY fecha DESC")->fetchAll(PDO::FETCH_ASSOC);

// Capturar y eliminar el mensaje de éxito si existe
$mensaje_estado = '';
if (isset($_SESSION['mensaje_estado'])) {
    $mensaje_estado = $_SESSION['mensaje_estado'];
    unset($_SESSION['mensaje_estado']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial Ventas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Prevenir caché para asegurar datos actualizados -->
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />

  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../public/assets/css/Vistas.css">
</head>
<body>
<div class="d-flex">
  <div class="sidebar">
    <div class="menu-item">
      <a href="Vistainventarioadmin.php"><img src="../public/assets/images/invenEmple.png" alt="Inventario"></a>
      <span>Inventario</span>
    </div>
    <div class="menu-item">
      <img src="../public/assets/images/ventasEmple.png" alt="Ventas">
      <span>Ventas</span>
    </div>
    <?php if ($_SESSION['usuario']['rol'] === 'admin'): ?>
    <div class="menu-item">
        <a href="empleados.php">
            <img src="assets/images/colabEmple.png" alt="Colaboradores">
        </a>
        <span>Colaboradores</span>
    </div>
<?php endif; ?>

    <div class="menu-item">
      <a href="perfilEmple.php"><img src="../public/assets/images/personas.png" alt="Perfil"></a>
      <span>Perfil</span>
    </div>
    <div class="menu-item" id="cerrarSesion">
      <a href="logout.php"><img src="../public/assets/images/exitEmple.png" alt="Cerrar sesión"></a>
      <span>Cerrar sesión</span>
    </div>
  </div>

  <div class="content container-fluid">
    <?php if (!empty($mensaje_estado)): ?>
      <div id="estado-alert" class="alert alert-success text-center font-weight-bold">
        <?= $mensaje_estado ?>
      </div>
    <?php endif; ?>

    <h1>Historial Ventas</h1>

    <div class="table-responsive mt-4">
      <table class="table table-bordered text-center">
        <thead class="thead-dark">
          <tr>
            <th>Fecha</th>
            <th>Cantidad productos</th>
            <th>Número venta</th>
            <th>Precio total</th>
            <th>Factura</th>
            <th>Comprobante</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($ordenes as $orden): 
          $productos = !empty($orden['productos']) ? json_decode($orden['productos'], true) : [];
          $cantidad = is_array($productos) ? count($productos) : 0;
          $numVenta = str_pad($orden['id'], 6, "0", STR_PAD_LEFT);
        ?>
        <tr>
          <td><?= $orden['fecha'] ?></td>
          <td><?= $cantidad ?></td>
          <td><?= $numVenta ?></td>
          <td>$<?= number_format($orden['total'], 0, ',', '.') ?></td>
          <td>
            <?php if (!empty($orden['factura_path']) && file_exists($orden['factura_path'])): ?>
              <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalFactura<?= $orden['id'] ?>">Ver Factura</button>
            <?php else: ?>
              <span class="text-muted">Sin factura</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if (!empty($orden['comprobante_path']) && file_exists($orden['comprobante_path']) && $orden['estado'] === 'en revisión'): ?>
              <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modalComprobante<?= $orden['id'] ?>">Ver Comprobante</button>
            <?php elseif ($orden['estado'] === 'aprobado'): ?>
              <span class="badge badge-success">Aprobado</span>
            <?php elseif ($orden['estado'] === 'rechazado'): ?>
              <span class="badge badge-danger">Rechazado</span>
            <?php else: ?>
              <span class="text-muted">No subido</span>
            <?php endif; ?>
          </td>
        </tr>

        <!-- Modal Factura -->
        <div class="modal fade" id="modalFactura<?= $orden['id'] ?>" tabindex="-1" role="dialog">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Factura No. <?= $numVenta ?></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <iframe src="../public/facturas/<?= basename($orden['factura_path']) ?>" width="100%" height="500px"></iframe>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Comprobante -->
        <div class="modal fade" id="modalComprobante<?= $orden['id'] ?>" tabindex="-1" role="dialog">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Comprobante de Pago</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body text-center">
                <?php
                $ext = strtolower(pathinfo($orden['comprobante_path'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png'])):
                  echo "<img src='{$orden['comprobante_path']}' class='img-fluid mb-3' style='max-height: 400px;'>";
                elseif ($ext === 'pdf'):
                  echo "<iframe src='{$orden['comprobante_path']}' width='100%' height='500px'></iframe>";
                else:
                  echo "<p class='text-danger'>❌ No se puede mostrar este tipo de archivo.</p>";
                endif;
                ?>

                <form method="POST" action="procesar_comprobante.php">
                  <input type="hidden" name="orden_id" value="<?= $orden['id'] ?>">
                  <button name="accion" value="aprobar" class="btn btn-success mr-2">Aprobar</button>
                  <button name="accion" value="rechazar" class="btn btn-danger">Rechazar</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Ocultar alert de éxito después de 3 segundos
  setTimeout(() => {
    const alerta = document.getElementById('estado-alert');
    if (alerta) alerta.remove();
  }, 3000);
</script>
</body>
</html>
