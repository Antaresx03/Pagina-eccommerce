<?php session_start(); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Login Cliente - MRM</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="assets/css/estilo.css" rel="stylesheet">
    <link href="assets/css/spinner.css" rel="stylesheet">
    <style>
        body {
            background-image: url('assets/images/FONDOOO.gif');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

       
    </style>
</head>
<body>

<div class="inner-container cliente">
    <div class="header text-center">
        <img src="assets/images/logo.png" alt="Logo de la empresa">
        <h1 class="h4">Bienvenido Cliente</h1>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['registro_exitoso'])): ?>
        <div class="alert alert-success text-center">
            Cuenta creada exitosamente. Ahora puedes iniciar sesión.
        </div>
        <script>
            // Mostrar automáticamente el modal si vienes de registro exitoso
            window.onload = function () {
                $('#registroModal').modal('show');
            };
        </script>
        <?php unset($_SESSION['registro_exitoso']); ?>
    <?php endif; ?>

    <form action="procesar_login_cliente.php" method="POST">
        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" name="email" class="form-control" 
            placeholder="Introduce tu correo electrónico" required>
            
        </div>
        <div class="form-group password-container">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Introduce tu contraseña" required>
                <img src="assets/images/ojo.png" alt="Mostrar/Ocultar contraseña" class="eye-icon"
                    onclick="togglePassword()">
            </div>
        <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
    </form>

    <p class="mt-4 text-center">¿No tienes cuenta? <a href="#" data-toggle="modal" data-target="#registroModal">Crear una</a></p>

    <div class="text-center mt-3">
        <a href="login.php">
            <img src="assets/images/mecanico.png" alt="Administrativo" style="width: 40px;">
        </a>
    </div>
</div>

<!-- Modal de registro -->
<div class="modal fade" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="registrar_cliente.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear cuenta de cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nombre completo</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Cédula</label>
                    <input type="text" name="cedula" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Correo</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="submit">Registrarme</button>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>
<script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            passwordInput.type = passwordInput.type === "password" ? "text" : "password";
        }
    </script>
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
