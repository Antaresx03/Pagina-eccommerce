<?php
session_start();
$mensaje = '';
if (isset($_SESSION['error_login'])) {
    $mensaje = $_SESSION['error_login'];
    unset($_SESSION['error_login']);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Iniciar sesión Personal MRM</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/estilo.css" rel="stylesheet">
    <link href="assets/css/spinner.css" rel="stylesheet">
</head>

<style>
    body {
        background-image: url('assets/images/FONDO%20ADMI.jpg');
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

<body>
    <div class="inner-container">
        <div class="header">
            <img src="assets/images/mecanico.png" alt="Logo de la empresa">
            <h1 class="h4">Personal MRM</h1>
        </div>
        <h1 class="h4 text-center mb-4">Iniciar sesión</h1>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-danger text-center font-weight-bold mt-2">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="procesar_auth.php">
            <input type="hidden" name="accion" value="login">
            
            <div class="form-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" class="form-control" id="email" name="email"
                    placeholder="Introduce tu correo electrónico" required>
            </div>
            <div class="form-group password-container">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Introduce tu contraseña" required>
                <img src="assets/images/ojo.png" alt="Mostrar/Ocultar contraseña" class="eye-icon"
                    onclick="togglePassword()">
            </div>
            
            <button type="submit" class="btn btn-danger btn-block">INGRESAR</button>
        </form>

        <div class="opciones-login text-center mt-4">
            <ul class="list-inline">
                <li class="list-inline-item">
                    <a href="ClienteLogin.php">
                        <img src="assets/images/Salir.png" alt="Salir" class="img-fluid">
                    </a>
                </li>
            </ul>
        </div>
        <div id="loginMessage" class="text-danger text-center mt-3"></div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            passwordInput.type = passwordInput.type === "password" ? "text" : "password";
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
