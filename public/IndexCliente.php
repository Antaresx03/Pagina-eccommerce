<?php
require_once '../database/database.php';
session_start(); // 👈 Necesario para acceder al nombre y correo del cliente
if (!isset($_SESSION['cliente_email'])) {
  header("Location: ClienteLogin.php");
  exit();
}


$conn = Database::conectar();

// Paginación y filtros
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 16;
$offset = ($page - 1) * $perPage;

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$where = "WHERE 1=1";
$params = [];

if (!empty($search)) {
    $where .= " AND nombre LIKE ?";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $where .= " AND categoria = ?";
    $params[] = $category;
}

$stmt = $conn->prepare("SELECT COUNT(*) FROM productos $where");
$stmt->execute($params);
$total = $stmt->fetchColumn();
$totalPages = ceil($total / $perPage);

$stmt = $conn->prepare("SELECT * FROM productos $where ORDER BY id DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<style>
  .product-badge {
    position: absolute;
    top: 10px;
    left: -10px;
    background-color: #dc3545;
    color: white;
    font-size: 12px;
    padding: 5px 10px;
    transform: rotate(-15deg);
    font-weight: bold;
    z-index: 10;
    box-shadow: 0 0 5px rgba(0,0,0,0.2);
  }
  .product-img-wrapper {
    position: relative;
  }
  
  .border-danger {
    box-shadow: 0 0 10px rgba(255, 0, 0, 0.4);
  }


</style>


  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cliente | MotoRepuestos Melo</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="./assets/css/ecommerce.css">
</head>
<body>

<!-- ✅ CABECERA ACTUALIZADA -->
<header class="bg-light py-3">
  <div class="container d-flex justify-content-between align-items-center">
    <img src="./assets/images/LogoEmpresa.jpg" alt="Logo" class="logo">
    <input type="text" id="busqueda" placeholder="Buscar por nombre" class="form-control search-box">
    <div class="ml-auto d-flex">
      <a class="nav-link" href="#" id="cart">
        <img src="./assets/images/cart.png" alt="Cart" width="30px">
        <span class="badge badge-pill badge-danger" id="cart-count">0</span>
      </a>
      <a class="nav-link" href="#" data-toggle="modal" data-target="#perfilModal">
        <img src="./assets/images/personas.png" alt="Perfil" width="30px">
      </a>
    </div>
  </div>
</header>

<nav class="navbar navbar-expand-md bg-dark navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="#">MotoRepuestos Melo</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="./IndexCliente.php">Inicio</a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Categorías
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown" id="categoryFilter">
              <a class="dropdown-item" href="#" onclick="filtrarCategoria('todos')">Todos</a>
              <a class="dropdown-item" href="#" onclick="filtrarCategoria('llantas y rines')">Llantas y rines</a>
              <a class="dropdown-item" href="#" onclick="filtrarCategoria('repuestos')">Repuestos</a>
              <a class="dropdown-item" href="#" onclick="filtrarCategoria('accesorios')">Accesorios</a>
              <a class="dropdown-item" href="#" onclick="filtrarCategoria('aceites')">Aceites</a>
              <a class="dropdown-item" href="#" onclick="filtrarCategoria('componentes eléctricos')">Componentes eléctricos</a>
            </div>
        </li>
      </ul>
    </div>
  </div>
</nav>

<section class="featured-products py-5">
  <div class="container">
    <h2 class="text-center mb-4">PRODUCTOS EN VENTA</h2>

    <div class="row" id="product-list">
    <?php foreach ($productos as $producto): ?>
  <div class="col-md-4 col-lg-3 mb-4 product-item" data-nombre="<?= strtolower($producto['nombre']) ?>" data-categoria="<?= strtolower($producto['categoria']) ?>">
    <div class="card h-100 text-center">
      <div class="product-img-wrapper">
        <img src="<?= htmlspecialchars($producto['imagen']) ?>" class="card-img-top" alt="<?= htmlspecialchars($producto['nombre']) ?>">
        <?php if ($producto['cantidad'] < 10): ?>
          <div class="product-badge">¡Pocas unidades!</div>
        <?php endif; ?>
      </div>
      <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($producto['nombre']) ?></h5>
        <p class="card-text">$<?= number_format($producto['precio_venta'], 0, ',', '.') ?></p>
        <button class="btn btn-warning">Añadir al carrito</button>
      </div>
    </div>
  </div>
<?php endforeach; ?>


    </div>
  </div>
</section>

<!-- Paginación -->
<nav class="mt-4">
  <ul class="pagination justify-content-center">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>"> <?= $i ?> </a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>

<!-- Modal del carrito -->
<div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cartModalLabel">Carrito de Compras</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul id="cart-items" class="list-group">
          <!-- Se llena dinámicamente desde carrito.js -->
        </ul>
        <p class="mt-3 font-weight-bold">Total: $<span id="cart-total">0</span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <form action="finalizar_compra.php" method="POST" id="goToCheckout">
          <input type="hidden" name="productos_json" id="productos_json">
          <input type="hidden" name="total" id="total_compra">
          <button type="submit" class="btn btn-success">Proceder al Pago</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ✅ MODAL DE PERFIL DEL CLIENTE -->
<div class="modal fade" id="perfilModal" tabindex="-1" role="dialog" aria-labelledby="perfilModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content shadow">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="perfilModalLabel">Mi Perfil</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <p><strong>👤 Nombre:</strong><br><?= $_SESSION['cliente_nombre'] ?? 'Desconocido' ?></p>
        <p><strong>📧 Correo:</strong><br><?= $_SESSION['cliente_email'] ?? 'No disponible' ?></p>
        <hr>
        <a href="ver_mis_compras.php" class="btn btn-outline-info btn-sm btn-block">🧾 Ver mis compras</a>
        <a href="logout_cliente.php" class="btn btn-outline-danger btn-sm btn-block mt-2">Cerrar sesión</a>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white py-4">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <h5>Quick Links</h5>
        <ul class="list-unstyled">
          <li><a href="./IndexCliente.php" class="text-white">Inicio</a></li>
        </ul>
      </div>
      <div class="col-md-6">
        <h5>Contáctanos</h5>
        <p>Teléfono: +57 3187459821</p>
        <p>Email: motoRepuestosmelo@gmail.com</p>
      </div>
    </div>
  </div>
</footer>

<!-- Scripts -->
<script>
function filtrarCategoria(cat) {
  const items = document.querySelectorAll('.product-item');
  items.forEach(item => {
    const categoria = item.dataset.categoria;
    item.style.display = (cat === 'todos' || categoria === cat.toLowerCase()) ? 'block' : 'none';
  });
}

document.getElementById('busqueda').addEventListener('input', function () {
  const valor = this.value.toLowerCase();
  document.querySelectorAll('.product-item').forEach(item => {
    const nombre = item.dataset.nombre;
    item.style.display = nombre.includes(valor) ? 'block' : 'none';
  });
});

</script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="./assets/js/carrito.js"></script>
<script src="./assets/js/cliente.js"></script>
<script>
    // Guardar el email del cliente en localStorage (solo si está en sesión)
    localStorage.setItem('cliente_email', '<?= $_SESSION['cliente_email'] ?? 'invitado' ?>');
</script>

</body>
</html>
