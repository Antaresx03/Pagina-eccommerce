<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Empleado - MRM</title>
    <link rel="stylesheet" href="assets/css/Colaboradores.css">
    <link href="assets/css/spinner.css" rel="stylesheet" />
</head>

<body>
    <div class="container">
        <header>
            <div class="logo">
                <img src="assets/images/_6d2b0277-817a-4654-8437-63b0eea52eb6.jpg" alt="Repuestos Melos" class="logo-img">
            </div>
            <div class="header-content">
                <h1>REPUESTOS MELO</h1>
                <h3>Norte santandereanos</h3>
            </div>
        </header>
        <main>
            <div class="col-md-7 col-lg-8">
                <h4 class="mb-3">AGREGAR COLABORADOR</h4>
                <div class="formulario-container">
                    <form class="registroForm_Empl" method="POST" action="../controllers/UsuarioController.php">
                        <input type="hidden" name="accion" value="crear_colaborador">
                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ingresa el nombre..." required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Número celular:</label>
                            <input type="text" id="telefono" name="telefono" placeholder="Ingresa número celular..." required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo electrónico:</label>
                            <input type="email" id="email" name="email" placeholder="Ingresa el correo..." required>
                        </div>
                        <div class="form-group">
                            <label for="cedula">Cédula:</label>
                            <input type="text" id="cedula" name="cedula" placeholder="Ingresa la cédula..." required>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña:</label>
                            <input type="password" id="password" name="password" placeholder="Contraseña..." required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn-agregar">Agregar empleado</button>
                            <a href="empleados.php" class="btn btn-secondary">Salir</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <footer>
            <p>&copy; 2024 Repuestos Melos. All Rights Reserved.</p>
            <a href="https://minegociocolombiano.co/">https://minegociocolombiano.co/</a>
        </footer>
    </div>
</body>

</html>
