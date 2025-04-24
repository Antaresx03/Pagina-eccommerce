<?php
require_once '../database/database.php';

$email = 'admin@mrm.com'; // El correo que registraste
$passwordIngresado = 'admin123'; // La clave que estás probando

$conexion = Database::conectar();
$sql = "SELECT * FROM usuarios WHERE email = :email";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    echo "✅ Usuario encontrado<br>";
    echo "Hash en base de datos: " . $usuario['contraseña'] . "<br>";
    echo "Contraseña ingresada: $passwordIngresado<br>";

    if (password_verify($passwordIngresado, $usuario['contraseña'])) {
        echo "<br>✅ Coinciden. ¡Login exitoso!";
    } else {
        echo "<br>❌ No coinciden. El hash no es válido con esta contraseña.";
    }
} else {
    echo "❌ Usuario NO encontrado en la base de datos.";
}
?>
