<?php
require_once __DIR__ . '/../database/database.php';

class Producto {

    public static function obtenerTodos() {
        $conn = Database::conectar();
        $stmt = $conn->query("SELECT * FROM productos ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function agregar($data) {
        try {
            $conn = Database::conectar();
            $sql = "INSERT INTO productos (nombre, codigo, categoria, cantidad, marca, precio_costo, precio_venta, tipo, imagen)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            return $stmt->execute([
                $data['nombre'] ?? '',
                $data['codigo'] ?? '',
                $data['categoria'] ?? '',
                $data['cantidad'] ?? 0,
                $data['marca'] ?? '',
                $data['precio_costo'] ?? 0,
                $data['precio_venta'] ?? 0,
                $data['tipo'] ?? '',
                $data['imagen'] ?? ''
            ]);
        } catch (PDOException $e) {
            error_log("❌ Error al agregar producto: " . $e->getMessage());
            return false;
        }
    }

    public static function actualizar($data) {
        try {
            $conn = Database::conectar();
            $sql = "UPDATE productos SET nombre=?, codigo=?, categoria=?, cantidad=?, marca=?, precio_costo=?, precio_venta=?, tipo=?, imagen=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            return $stmt->execute([
                $data['nombre'] ?? '',
                $data['codigo'] ?? '',
                $data['categoria'] ?? '',
                $data['cantidad'] ?? 0,
                $data['marca'] ?? '',
                $data['precio_costo'] ?? 0,
                $data['precio_venta'] ?? 0,
                $data['tipo'] ?? '',
                $data['imagen'] ?? '',
                $data['id']
            ]);
        } catch (PDOException $e) {
            error_log("❌ Error al actualizar producto: " . $e->getMessage());
            return false;
        }
    }

    public static function eliminar($id) {
        try {
            $conn = Database::conectar();
            $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("❌ Error al eliminar producto: " . $e->getMessage());
            return false;
        }
    }
}
