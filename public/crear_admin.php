<?php
require_once '../models/Usuario.php';

$nombre = "Admin MRM";
$email = "admin@mrm.com";
$passwordPlano = "admin123";
$rol = "admin";

$passwordHash = password_hash($passwordPlano, PASSWORD_BCRYPT);

if (Usuario::crear($nombre, $email, $passwordHash, $rol)) {
    echo "✅ Administrador creado con éxito.<br>";
    echo "Correo: $email<br>";
    echo "Contraseña: $passwordPlano";
} else {
    echo "❌ Error al crear el administrador. ¿Ya existe?";
}
?>
