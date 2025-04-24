<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../database/database.php';

class AuthController {

    public function login($email, $password) {
        session_start();
        $usuario = Usuario::obtenerPorEmail($email);

        if ($usuario && isset($usuario['contraseña']) && password_verify($password, $usuario['contraseña'])) {

            // ⚠️ Validar rol autorizado (ajusta aquí si necesitas más roles válidos)
            $rolPermitido = ['admin', 'colaborador'];


            if (!in_array(strtolower($usuario['rol']), $rolPermitido)) {
                $_SESSION['error_login'] = '❌ Acceso denegado: personal no autorizado.';
                header('Location: ../public/login.php');
                exit();
            }

            // ✅ Acceso permitido
            $_SESSION['usuario'] = $usuario;
            header('Location: ../public/perfilEmple.php');
            exit();

        } else {
            $_SESSION['error_login'] = '❌ Correo o contraseña incorrectos.';
            header('Location: ../public/login.php');
            exit();
        }
    }

    public function registrar($nombre, $email, $password, $rol = 'cliente') {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $resultado = Usuario::crear($nombre, $email, $hash, $rol);

        if ($resultado) {
            echo "Registro exitoso. Inicia sesión.";
        } else {
            echo "Error al registrar.";
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: ../public/login.php');
    }
}
