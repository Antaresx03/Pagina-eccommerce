<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once '../models/Usuario.php';
$colaboradores = Usuario::listarColaboradores();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Colaboradores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <div class="d-flex">
        <div class="sidebar">
            <div class="menu-item"><a href="Vistainventarioadmin.php"><img src="assets/images/invenEmple.png" alt="Inventario"></a><span>Inventario</span></div>
            <div class="menu-item"><a href="Ventas.php"><img src="assets/images/ventasEmple.png" alt="Ventas"></a><span>Ventas</span></div>
            <div class="menu-item"><a href="empleados.php"><img src="assets/images/colabEmple.png" alt="Colaboradores"></a><span>Colaboradores</span></div>
            <div class="menu-item"><a href="perfilEmple.php"><img src="assets/images/personas.png" alt="Perfil"></a><span>Perfil</span></div>
            <div class="menu-item" id="cerrarSesion"><a href="logout.php"><img src="assets/images/exitEmple.png" alt="Cerrar sesión"></a><span>Cerrar sesión</span></div>
        </div>

        <div class="content">
            <div class="container header-container">
                <h1>Gestión de colaboradores</h1>
                <div class="barra-superior">
                    <a href="crear_colaborador.php" class="btn btn-rojo">Agregar empleado</a>
                </div>
            </div>

            <div class="container">
                <div class="row" id="employeesContainer">
                    <?php foreach ($colaboradores as $colab): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card p-3 shadow">
                                <h5 class="card-title"><?php echo htmlspecialchars($colab['nombre']); ?></h5>
                                <p class="card-text">
                                    <strong>Correo:</strong> <?php echo htmlspecialchars($colab['email']); ?><br>
                                    <strong>Cédula:</strong> <?php echo htmlspecialchars($colab['cedula']); ?><br>
                                    <strong>Teléfono:</strong> <?php echo htmlspecialchars($colab['telefono']); ?><br>
                                    <strong>Rol:</strong> <?php echo htmlspecialchars($colab['rol']); ?>
                                </p>
                                <button 
                                    class="btn btn-primary btn-sm editar-btn"
                                    data-id="<?php echo $colab['id']; ?>"
                                    data-nombre="<?php echo $colab['nombre']; ?>"
                                    data-email="<?php echo $colab['email']; ?>"
                                    data-telefono="<?php echo $colab['telefono']; ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal">
                                    Editar
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar colaborador -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="../controllers/UsuarioController.php">
                <input type="hidden" name="accion" value="editar_colaborador">
                <input type="hidden" id="editId" name="id">

                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Colaborador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editNombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="editNombre" name="nombre" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Correo electrónico:</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTelefono" class="form-label">Teléfono:</label>
                        <input type="text" class="form-control" id="editTelefono" name="telefono" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const editButtons = document.querySelectorAll('.editar-btn');
            editButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    document.getElementById('editId').value = this.dataset.id;
                    document.getElementById('editNombre').value = this.dataset.nombre;
                    document.getElementById('editEmail').value = this.dataset.email;
                    document.getElementById('editTelefono').value = this.dataset.telefono;
              
                });
            });
        });
    </script>
</body>

</html>
