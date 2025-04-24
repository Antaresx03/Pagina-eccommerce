<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro MRM</title>
</head>
<body>
    <h2>Registro de usuario</h2>
    <form action="procesar_auth.php" method="POST">
        <input type="hidden" name="accion" value="registrar">

        <label for="nombre">Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label for="email">Correo electrónico:</label><br>
        <input type="email" name="email" required><br><br>

        <label for="password">Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <label for="rol">Rol:</label><br>
        <select name="rol">
            <option value="cliente">Cliente</option>
            <option value="colaborador">Colaborador</option>
            <option value="admin">Administrador</option>
        </select><br><br>

        <button type="submit">Registrarse</button>
    </form>
</body>
</html>
