<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../models/Producto.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'listar':
        echo json_encode(Producto::obtenerTodos());
        break;

    case 'agregar':
        $result = Producto::agregar($_POST);
        echo json_encode(['success' => $result]);
        if (!$result) {
            error_log("Fallo al insertar el producto. Data recibida:");
            error_log(print_r($_POST, true));
        }
        break;

    case 'editar':
        $id = $_POST['id'] ?? null;
        $cantidad = intval($_POST['cantidad'] ?? 0);

        if ($cantidad <= 0 && $id) {
            // Si la cantidad es 0 o menor, eliminar producto
            $deleted = Producto::eliminar($id);
            echo json_encode([
                'success' => $deleted,
                'message' => $deleted
                    ? '🗑️ Producto eliminado automáticamente por quedarse sin stock.'
                    : '❌ Error al intentar eliminar producto con stock 0.'
            ]);
        } else {
            // Si hay cantidad válida, editar normalmente
            $result = Producto::actualizar($_POST);
            echo json_encode(['success' => $result]);
        }
        break;

    case 'eliminar':
        $ids = $_POST['ids'] ?? [];
        foreach ($ids as $id) {
            Producto::eliminar($id);
        }
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
        break;
}
