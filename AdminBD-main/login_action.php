<?php
session_start();
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        header("Location: login.php?error=Por favor llena todos los campos");
        exit();
    }

    try {
        // Buscar al usuario por email (que estamos usando como username)
        $stmt = $pdo->prepare("SELECT id_usuario, nombre, email, password_hash, id_rol, activo FROM Usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user->password_hash)) {
            if ($user->activo == 1) {
                // Credenciales correctas, iniciar sesión
                $_SESSION['usuario_id'] = $user->id_usuario;
                $_SESSION['usuario_nombre'] = $user->nombre;
                $_SESSION['usuario_rol'] = $user->id_rol;

                header("Location: dashboard.php");
                exit();
            } else {
                header("Location: login.php?error=Tu cuenta está inactiva. Contacta al administrador.");
                exit();
            }
        } else {
            // Credenciales incorrectas
            header("Location: login.php?error=Usuario o contraseña incorrectos");
            exit();
        }
    } catch (Exception $e) {
        header("Location: login.php?error=Error en el servidor");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
