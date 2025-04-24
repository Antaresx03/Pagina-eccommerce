<?php
require_once '../database/database.php';
session_start();

$conn = Database::conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $cedula = $_POST['cedula'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $clave = password_hash($_POST['password'], PASSWORD_DEFAULT); // Siempre encriptada

    try {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, cedula, email, telefono, contraseña, rol) VALUES (?, ?, ?, ?, ?, 'cliente')");
        $stmt->execute([$nombre, $cedula, $email, $telefono, $clave]);

        // Mostrar alerta y redirigir a login
        echo "<script>
            alert('✅ Cuenta creada exitosamente. Ahora puedes iniciar sesión.');
            window.location.href = 'ClienteLogin.php';
        </script>";
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: ClienteLogin.php");
        exit;
    }
}
?>
