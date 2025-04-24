<?php
require_once '../database/database.php';

$email = 'admin@mrm.com'; // Tu correo real
$nuevaClave = 'admin123';

$hash = password_hash($nuevaClave, PASSWORD_BCRYPT);

$conexion = Database::conectar();
$sql = "UPDATE usuarios SET contraseña = :hash WHERE email = :email";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':hash', $hash);
$stmt->bindParam(':email', $email);

if ($stmt->execute()) {
    echo "✅ Contraseña del admin actualizada correctamente con hash:<br><br>";
    echo "<pre>$hash</pre>";
} else {
    echo "❌ Falló al actualizar la contraseña.";
}
?>
