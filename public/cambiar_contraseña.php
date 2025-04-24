<?php
session_start();
require_once __DIR__ . '/../database/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_SESSION['usuario']['id'];
    $contraseña_actual = $_POST['contraseña_actual'] ?? '';
    $nueva_contraseña = $_POST['nueva_contraseña'] ?? '';
    $confirmar_contraseña = $_POST['confirmar_contraseña'] ?? '';

    if ($nueva_contraseña !== $confirmar_contraseña) {
        echo json_encode(['success' => false, 'message' => 'Las nuevas contraseñas no coinciden.']);
        exit();
    }

    $conn = Database::conectar();
    $sql = "SELECT contraseña FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($contraseña_actual, $usuario['contraseña'])) {
        $nueva_hash = password_hash($nueva_contraseña, PASSWORD_BCRYPT);
        $update = $conn->prepare("UPDATE usuarios SET contraseña = ? WHERE id = ?");
        if ($update->execute([$nueva_hash, $id])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la contraseña.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'La contraseña actual es incorrecta.']);
    }
}
?>
