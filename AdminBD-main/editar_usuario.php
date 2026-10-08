<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header("Location: dashboard.php?error=Acceso denegado");
    exit();
}

require 'includes/db.php';

$mensaje = '';
$tipo_alerta = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: usuarios.php");
    exit();
}

$id_usuario = (int)$_GET['id'];

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_usuario'])) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $id_rol = (int)$_POST['id_rol'];
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    // Si envían una nueva contraseña, la actualizamos
    $password_query = "";
    $params = [$nombre, $email, $id_rol, $activo];
    
    if (!empty($_POST['password'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_query = ", password_hash = ?";
        $params[] = $password_hash;
    }
    
    $params[] = $id_usuario; // Para el WHERE
    
    if (!empty($nombre) && !empty($email) && !empty($id_rol)) {
        try {
            $stmt = $pdo->prepare("UPDATE Usuarios SET nombre = ?, email = ?, id_rol = ?, activo = ? $password_query WHERE id_usuario = ?");
            $stmt->execute($params);
            $mensaje = "Usuario actualizado exitosamente.";
            $tipo_alerta = "success";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $mensaje = "El correo electrónico ya está registrado por otro usuario.";
            } else {
                $mensaje = "Error al actualizar: " . $e->getMessage();
            }
            $tipo_alerta = "danger";
        }
    } else {
        $mensaje = "Nombre, Email y Rol son obligatorios.";
        $tipo_alerta = "warning";
    }
}

// Obtener datos del usuario
$stmt_usuario = $pdo->prepare("SELECT * FROM Usuarios WHERE id_usuario = ?");
$stmt_usuario->execute([$id_usuario]);
$usuario = $stmt_usuario->fetch();

if (!$usuario) {
    header("Location: usuarios.php?error=Usuario no encontrado");
    exit();
}

// Obtener roles
$stmt_roles = $pdo->query("SELECT * FROM Roles ORDER BY id_rol ASC");
$roles = $stmt_roles->fetchAll();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - QueHayPaHacer</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: var(--bg-card); padding: 2rem 1.5rem; border-right: 1px solid var(--border-color); }
        .main-content { flex-grow: 1; padding: 2.5rem; }
        .sidebar-nav { list-style: none; margin-top: 3rem; }
        .sidebar-nav li { margin-bottom: 1rem; }
        .sidebar-nav a { color: var(--text-muted); text-decoration: none; display: flex; align-items: center; padding: 0.5rem 0; font-size: 1.1rem; }
        .sidebar-nav a:hover, .sidebar-nav a.active { color: var(--primary-color); }
        .header { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color); }
        
        .alert-banner { padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 2rem; }
        .alert-banner.success { background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; }
        .alert-banner.danger { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; }
        .alert-banner.warning { background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #fbbf24; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar fade-in-up">
            <h2 class="auth-logo" style="font-size: 1.8rem;">QueHayPaHacer</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Panel de Administración</p>
            <ul class="sidebar-nav">
                <li><a href="dashboard.php">📊 Resumen</a></li>
                <li><a href="comercios.php">🏪 Gestionar Comercios</a></li>
                <li><a href="#">🏖️ Aprobar Turismo</a></li>
                <li><a href="usuarios.php" class="active">👥 Usuarios y Roles</a></li>
                <li><a href="logout.php" style="color: #ef4444; margin-top: 2rem;">🚪 Cerrar Sesión</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content fade-in-up">
            <div class="header">
                <h1>Editar Usuario</h1>
                <p style="color: var(--text-muted);">Modifica los datos, cambia roles o desactiva la cuenta.</p>
            </div>

            <?php if (!empty($mensaje)): ?>
            <div class="alert-banner <?php echo $tipo_alerta; ?>">
                <?php echo $mensaje; ?>
            </div>
            <?php endif; ?>

            <div class="glass-panel" style="padding: 2rem; max-width: 600px;">
                <form method="POST" action="">
                    <input type="hidden" name="actualizar_usuario" value="1">
                    
                    <div class="form-group">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-input" value="<?php echo htmlspecialchars($usuario->nombre); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-input" value="<?php echo htmlspecialchars($usuario->email); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Rol del Usuario</label>
                        <select name="id_rol" class="form-input" required>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?php echo $rol->id_rol; ?>" <?php echo ($rol->id_rol == $usuario->id_rol) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($rol->nombre); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
                        <input type="checkbox" name="activo" id="activo" <?php echo ($usuario->activo) ? 'checked' : ''; ?> style="width: 1.2rem; height: 1.2rem;">
                        <label for="activo" class="form-label" style="margin: 0;">Usuario Activo (Puede iniciar sesión)</label>
                    </div>
                    
                    <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 1.5rem 0;">
                    
                    <div class="form-group">
                        <label class="form-label">Nueva Contraseña (Dejar en blanco para no cambiar)</label>
                        <input type="password" name="password" class="form-input" placeholder="••••••••">
                    </div>

                    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <a href="usuarios.php" class="btn" style="background: rgba(255,255,255,0.1); color: white;">Cancelar</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
