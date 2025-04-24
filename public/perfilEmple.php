<?php
session_start();

// Verifica si hay sesión y si el rol es admin o colaborador
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'colaborador'])) {
    header('Location: login.php');
    exit();
}



$nombre = $_SESSION['usuario']['nombre'];
$email = $_SESSION['usuario']['email'];
$telefono = $_SESSION['usuario']['telefono'] ?? 'No registrado';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/Bienvenido.css">
    <link rel="stylesheet" href="assets/css/Vistas.css">
    <link rel="stylesheet" href="assets/css/perfilEmpl.css">
    <link rel="stylesheet" href="assets/css/spinner.css">
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

</head>

<body>
    <!-- Sidebars -->
    <div id="sidebar-admin" class="sidebar">
        <div class="menu-item">
            <a href="Vistainventarioadmin.php">
                <img src="assets/images/invenEmple.png" alt="Inventario">
            </a>
            <span>Inventario</span>
        </div>
        <div class="menu-item">
            <a href="Ventas.php">
                <img src="assets/images/ventasEmple.png" alt="Ventas">
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
                <img src="assets/images/personas.png" alt="Perfil">
            </a>
            <span>Perfil</span>
        </div>
        <div class="menu-item" id="cerrarSesion">
            <a href="logout.php">
                <img src="assets/images/exitEmple.png" alt="Cerrar sesión">
            </a>
            <span>Cerrar sesión</span>
        </div>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <div class="container">
        <?php
$rol = $_SESSION['usuario']['rol'];
$saludo = $rol === 'admin' ? 'Bienvenido Administrador' : 'Bienvenido Colaborador';
?>
<h2 class="text-center mb-4"><?php echo $saludo; ?></h2>

            <div class="row justify-content-center">
                <!-- Columna 1 -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <svg class="bi bi-person-fill text-primary mr-3" width="4em" height="4em"
                                    viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M8 0a8 8 0 0 0-8 8c0 4.418 3.582 8 8 8s8-3.582 8-8a8 8 0 0 0-8-8zm0 3a3 3 0 1 0 0 6 3 3 0 0 0 0-6zm0 1a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" />
                                </svg>
                                <div>
                                    <h3 class="h4 font-weight-bold mb-0" id="userName"><?php echo $nombre; ?></h3>
                                    <p class="text-muted mb-0" id="userEmail"><?php echo $email; ?></p>
                                </div>
                            </div>
                            <button class="btn btn-outline-primary btn-block" data-toggle="modal"
                                data-target="#editProfileModal">Editar Perfil</button>
                        </div>
                    </div>
                </div>

                <!-- Columna 2 -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="h5 font-weight-bold mb-4">Información Personal</h3>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Nombre</span>
                                </div>
                                <input type="text" class="form-control" readonly value="<?php echo $nombre; ?>">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Email</span>
                                </div>
                                <input type="email" class="form-control" readonly value="<?php echo $email; ?>">
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">N° celular</span>
                                </div>
                                <input type="text" class="form-control" readonly value="<?php echo $telefono; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="row justify-content-left">
                <div class="col-md-6 text-center">
                    <button class="btn btn-outline-primary mr-2" id="changePasswordBtn" data-toggle="modal"
                        data-target="#changePasswordModal">Cambiar Contraseña</button>
                </div>
            </div>
        </div>
        <!-- Modal Editar Perfil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editProfileForm" method="POST" action="actualizar_perfil.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editProfileModalLabel">Editar Perfil</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="nombre" value="<?php echo $nombre; ?>" required>
          </div>
          <div class="form-group">
            <label for="cedula">Cédula</label>
            <input type="text" class="form-control" name="cedula" id="cedula" value="<?php echo $_SESSION['usuario']['cedula'] ?? ''; ?>" required>
          </div>
          <div class="form-group">
            <label for="telefono">Número de Celular</label>
            <input type="text" class="form-control" name="telefono" id="telefono" value="<?php echo $telefono; ?>" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- Modal Cambiar Contraseña -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="changePasswordForm" method="POST" action="cambiar_contraseña.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="changePasswordModalLabel">Cambiar Contraseña</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="contraseña_actual">Contraseña Actual</label>
            <input type="password" class="form-control" name="contraseña_actual" id="contraseña_actual" required>
          </div>
          <div class="form-group">
            <label for="nueva_contraseña">Nueva Contraseña</label>
            <input type="password" class="form-control" name="nueva_contraseña" id="nueva_contraseña" required>
          </div>
          <div class="form-group">
            <label for="confirmar_contraseña">Confirmar Nueva Contraseña</label>
            <input type="password" class="form-control" name="confirmar_contraseña" id="confirmar_contraseña" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
        </div>
      </div>
    </form>
  </div>
</div>

    </div>


    <!-- Modals -->
    <!-- Aquí puedes dejar tus modales y JS igual que los tenías -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/js/profile.js"></script>
    <script src="assets/js/password.js"></script>
    <script src="assets/js/editProfile.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
