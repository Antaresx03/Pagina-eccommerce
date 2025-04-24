<?php
require_once __DIR__ . '/../database/database.php';

class Usuario {

    public static function crear($nombre, $email, $password, $rol = 'cliente') {
        $conn = Database::conectar();
        $sql = "INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$nombre, $email, $password, $rol]);
    }

    public static function obtenerPorEmail($email) {
        $conexion = Database::conectar();
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function crearCompleto($nombre, $cedula, $telefono, $email, $password, $rol) {
        $conn = Database::conectar();
        $sql = "INSERT INTO usuarios (nombre, cedula, telefono, email, contraseña, rol) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$nombre, $cedula, $telefono, $email, $password, $rol]);
    }
    public static function listarColaboradores() {
        $conn = Database::conectar();
        $sql = "SELECT * FROM usuarios WHERE rol = 'colaborador'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function actualizarContacto($id, $email, $telefono) {
        $conn = Database::conectar();
        $sql = "UPDATE usuarios SET email = ?, telefono = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$email, $telefono, $id]);
    }
    
    
}
