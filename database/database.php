<?php
class Database {
    private static $host = 'localhost';
    private static $db = 'mrm_ecommerce';
    private static $user = 'root';
    private static $pass = '';

    public static function conectar() {
        try {
            $conexion = new PDO("mysql:host=".self::$host.";dbname=".self::$db, self::$user, self::$pass);
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conexion;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
?>
