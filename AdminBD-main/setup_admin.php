<?php
require 'includes/db.php';

try {
    // Generamos el hash de la contraseña de forma segura
    $password = 'quehaypahacer';
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Verificamos si el usuario ya existe
    $stmt = $pdo->prepare("SELECT id_usuario FROM Usuarios WHERE email = 'admin'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        // Insertamos el Superadministrador (id_rol = 1)
        $insert = $pdo->prepare("INSERT INTO Usuarios (nombre, email, password_hash, id_rol, activo) VALUES ('Superadministrador', 'admin', ?, 1, 1)");
        $insert->execute([$password_hash]);
        echo "✅ Usuario Superadministrador creado exitosamente.\n";
    } else {
        echo "⚠️ El usuario 'admin' ya existe en la base de datos.\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
