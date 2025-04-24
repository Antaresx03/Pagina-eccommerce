<?php
session_start();
require_once __DIR__ . '/../database/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_SESSION['usuario']['id'];

    // Validaciones: asegurarse que vengan todos los campos
    if (!isset($_POST['nombre'], $_POST['cedula'], $_POST['telefono'])) {
        echo "<script>alert('❌ Datos incompletos.'); window.location.href='perfilEmple.php';</script>";
        exit();
    }

    $nombre = trim($_POST['nombre']);
    $cedula = trim($_POST['cedula']);
    $telefono = trim($_POST['telefono']);

    $conn = Database::conectar();
    $sql = "UPDATE usuarios SET nombre = ?, cedula = ?, telefono = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$nombre, $cedula, $telefono, $id])) {
        $_SESSION['usuario']['nombre'] = $nombre;
        $_SESSION['usuario']['cedula'] = $cedula;
        $_SESSION['usuario']['telefono'] = $telefono;

        echo "<script>alert('✅ Perfil actualizado correctamente.'); window.location.href='perfilEmple.php';</script>";
        exit();
    } else {
        echo "<script>alert('❌ Error al actualizar.'); window.location.href='perfilEmple.php';</script>";
    }
}
?>
