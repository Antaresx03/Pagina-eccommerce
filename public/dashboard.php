<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$nombre = $_SESSION['usuario']['nombre'];
$rol = $_SESSION['usuario']['rol'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido al panel - MRM</title>
</head>
<body>
    <h1>¡Bienvenido, <?php echo $nombre; ?>!</h1>
    <p>Tu rol: <strong><?php echo $rol; ?></strong></p>

    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
