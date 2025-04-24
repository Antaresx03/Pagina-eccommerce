<?php
session_start();
require_once '../database/database.php';

$conn = Database::conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ? AND rol = 'cliente'");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['contraseña'])) {
        $_SESSION['cliente_id'] = $usuario['id'];
        $_SESSION['cliente_nombre'] = $usuario['nombre'];
        $_SESSION['cliente_email'] = $usuario['email'];
        header("Location: IndexCliente.php");
        exit;
    } else {
        $_SESSION['error'] = "Correo o contraseña incorrectos";
        header("Location: ClienteLogin.php");
        exit;
    }
}
?>
