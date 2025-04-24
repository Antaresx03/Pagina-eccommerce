<?php
require_once '../controllers/AuthController.php';

$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'registrar') {
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $rol = $_POST['rol'] ?? '';

        if (!empty($nombre) && !empty($email) && !empty($password) && !empty($rol)) {
            $auth->registrar($nombre, $email, $password, $rol);
        } else {
            echo "Por favor, completa todos los campos del registro.";
        }

    } elseif ($accion === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!empty($email) && !empty($password)) {
            $auth->login($email, $password);
        } else {
            echo "Por favor, ingresa tu correo y contraseña.";
        }
    }
}
