<?php
require_once '../models/Usuario.php';
require_once '../database/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear_colaborador') {
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $cedula = $_POST['cedula'] ?? '';

        if (!empty($nombre) && !empty($email) && !empty($password) && !empty($telefono) && !empty($cedula)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $rol = 'colaborador';

            $resultado = Usuario::crearCompleto($nombre, $cedula, $telefono, $email, $hash, $rol);

            if ($resultado) {
                echo "✅ Colaborador registrado correctamente.<br><a href='../public/crear_colaborador.php'>← Volver</a>";
            } else {
                echo "❌ Error al registrar el colaborador. Puede que ya exista el correo.";
            }
        } else {
            echo "❌ Por favor completa todos los campos.";
        }
    }
    if ($accion === 'editar_colaborador') {
        $id = $_POST['id'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
    
        if (!empty($id) && !empty($email) && !empty($telefono)) {
            $resultado = Usuario::actualizarContacto($id, $email, $telefono);
    
            if ($resultado) {
                header("Location: ../public/empleados.php");
                exit();
            } else {
                echo "❌ Error al actualizar.";
            }
        } else {
            echo "❌ Faltan datos.";
        }
    }
    
}
