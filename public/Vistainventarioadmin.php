<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Inventario</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/assets/css/Vistas.css">
</head>

<body>
    <div class="d-flex">
        <div class="sidebar">
            <div class="menu-item">
                <a href="Vistainventarioadmin.php">
                    <img src="../public/assets/images/invenEmple.png" alt="Inventario">
                </a>
                <span>Inventario</span>
            </div>
            <div class="menu-item">
                <a href="ventas.php">
                    <img src="../public/assets/images/ventasEmple.png" alt="Ventas">
                </a>
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
                <a href="perfilEmple.php">
                    <img src="../public/assets/images/personas.png" alt="Perfil">
                </a>
                <span>Perfil</span>
            </div>
            <div class="menu-item" id="cerrarSesion">
                <a href="logout.php">
                    <img src="../public/assets/images/exitEmple.png" alt="Cerrar sesión">
                </a>
                <span>Cerrar sesión</span>
            </div>
        </div>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="content container-fluid">
            <h1>Stock Inventario</h1>
             <!-- 🔴 ALERTA de productos con bajo stock -->
        <?php if (!empty($productosBajoStock)): ?>
            <div class="alert alert-danger font-weight-bold">
                ⚠ Alerta de Stock Bajo:
                <ul class="mb-0">
                    <?php foreach ($productosBajoStock as $prod): ?>
                        <li><?= htmlspecialchars($prod['nombre']) ?> (<?= $prod['cantidad'] ?> unidades)</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex flex-grow-1">
                <input type="text" class="form-control w-25 mr-2" placeholder="Filtrar por marca" id="filterSupplier">
                <input type="text" class="form-control w-25 mr-2" placeholder="Filtrar por categoría" id="filterCategory">

                    <input type="number" class="form-control w-25 mr-2" placeholder="Filtrar por costo adquirido" id="filterCost">
                    <input type="number" class="form-control w-25" placeholder="Filtrar por cantidad" id="filterQuantity">
                </div>
                <div class="d-flex">
                    <button id="btnAgregar" class="btn btn-warning mr-2" data-toggle="modal" data-target="#createModal">Agregar</button>
                    <button class="btn btn-danger" id="deleteButton">Eliminar</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="inventoryTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID</th>
                            <th>No.</th>
                            <th>Nombre</th>
                            <th>Código</th>
                            <th>Categoría</th>
                            <th>Cantidad</th>
                            <th>Marca</th>
                            <th>Costo adquirido</th>
                            <th>Costo venta</th>
                            <th>Img</th>
                            <th>Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se cargan los productos desde JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Producto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <div class="form-group"><label>ID</label><input type="text" class="form-control" id="editId" readonly></div>
                        <div class="form-group"><label>Nombre</label><input type="text" class="form-control" id="editName"></div>
                        <div class="form-group"><label>Código</label><input type="text" class="form-control" id="editCode"></div>
                        <div class="form-group">
    <label>Categoría</label>
    <select id="editCategory" class="form-control" disabled>
        <option value="llantas y rines">Llantas y rines</option>
        <option value="repuestos">Repuestos</option>
        <option value="accesorios">Accesorios</option>
        <option value="aceites">Aceites</option>
        <option value="componentes eléctricos">Componentes eléctricos</option>
    </select>
</div>

                        <div class="form-group"><label>Cantidad</label><input type="number" class="form-control" id="editQuantity"></div>
                        <div class="form-group"><label>Marca</label><input type="text" class="form-control" id="editSupplier"></div>
                        <div class="form-group"><label>Costo adquirido</label><input type="text" class="form-control" id="editCost"></div>
                        <div class="form-group"><label>Costo venta</label><input type="text" class="form-control" id="editSaleCost"></div>
                        <div class="form-group"><label>URL de la Imagen</label><input type="text" class="form-control" id="editImgUrl"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="saveChangesButton">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL CREAR -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Agregar Producto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createForm">
                        <div class="form-group"><label>Nombre</label><input type="text" class="form-control" id="nombre"></div>
                        <div class="form-group"><label>Código</label><input type="text" class="form-control" id="codigo"></div>
                        <div class="form-group"><label>Costo adquirido</label><input type="text" class="form-control" id="precioCosto"></div>
                        <div class="form-group"><label>Costo venta</label><input type="text" class="form-control" id="precioVenta"></div>
                        <div class="form-group"><label>Categoría</label>
                        <select id="categoriaId" class="form-control">
  <option value="llantas y rines">Llantas y rines</option>
  <option value="repuestos">Repuestos</option>
  <option value="accesorios">Accesorios</option>
  <option value="aceites">Aceites</option>
  <option value="componentes eléctricos">Componentes eléctricos</option>
</select>
                        
                    </div>
                        <div class="form-group"><label>Marca</label><input type="text" class="form-control" id="marcaId"></div>
                        <div class="form-group"><label>Cantidad</label><input type="number" class="form-control" id="cantidad"></div>
                        <div class="form-group"><label>Tipo</label><input type="text" class="form-control" id="tipoId"></div>
                        <div class="form-group"><label>URL de la Imagen</label><input type="text" class="form-control" id="imgUrl"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="createProductButton">Agregar Producto</button>
                </div>
            </div>
        </div>
    </div>

    

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterSupplier = document.getElementById('filterSupplier');
            const filterCategory = document.getElementById('filterCategory');
            const filterCost = document.getElementById('filterCost');
            const filterQuantity = document.getElementById('filterQuantity');
            const table = document.getElementById('inventoryTable').getElementsByTagName('tbody')[0];

            function filterTable() {
                const supplierValue = filterSupplier.value.toLowerCase();
                const categoryValue = filterCategory.value.toLowerCase();
                const costValue = filterCost.value.toLowerCase();
                const quantityValue = filterQuantity.value.toLowerCase();

                for (let row of table.rows) {
                    const supplier = row.cells[7].textContent.toLowerCase();
                    const category = row.cells[5].textContent.toLowerCase();
                    const cost = row.cells[8].textContent.toLowerCase();
                    const quantity = row.cells[6].textContent.toLowerCase();

                    if ((supplier.includes(supplierValue) || supplierValue === '') &&
                        (category.includes(categoryValue) || categoryValue === '') &&
                        (cost.includes(costValue) || costValue === '') &&
                        (quantity.includes(quantityValue) || quantityValue === '')) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            }

            filterSupplier.addEventListener('input', filterTable);
            filterCategory.addEventListener('input', filterTable);
            filterCost.addEventListener('input', filterTable);
            filterQuantity.addEventListener('input', filterTable);
        });
    </script>
    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="/public/assets/js/Inventario.js"></script>
</body>

</html>
