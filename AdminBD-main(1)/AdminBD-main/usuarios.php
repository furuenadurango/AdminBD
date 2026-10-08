<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header("Location: dashboard.php?error=Acceso denegado");
    exit();
}

require 'includes/db.php';

$mensaje = '';
$tipo_alerta = '';

// Procesar eliminación de usuario
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    if ($id_eliminar !== $_SESSION['usuario_id']) { // No permitir eliminarse a sí mismo
        try {
            $stmt = $pdo->prepare("DELETE FROM Usuarios WHERE id_usuario = ?");
            $stmt->execute([$id_eliminar]);
            $mensaje = "Usuario eliminado correctamente.";
            $tipo_alerta = "success";
        } catch (PDOException $e) {
            $mensaje = "Error al eliminar usuario (posiblemente tenga comercios asociados).";
            $tipo_alerta = "danger";
        }
    } else {
        $mensaje = "No puedes eliminar tu propia cuenta.";
        $tipo_alerta = "danger";
    }
}

// Procesar creación de usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_usuario'])) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $id_rol = (int)$_POST['id_rol'];
    
    if (!empty($nombre) && !empty($email) && !empty($password) && !empty($id_rol)) {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO Usuarios (nombre, email, password_hash, id_rol, activo) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$nombre, $email, $password_hash, $id_rol]);
            $mensaje = "Usuario creado exitosamente.";
            $tipo_alerta = "success";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $mensaje = "El correo electrónico ya está registrado.";
            } else {
                $mensaje = "Error al crear usuario: " . $e->getMessage();
            }
            $tipo_alerta = "danger";
        }
    } else {
        $mensaje = "Todos los campos son obligatorios.";
        $tipo_alerta = "warning";
    }
}

// Obtener roles para el formulario
$stmt_roles = $pdo->query("SELECT * FROM Roles ORDER BY id_rol ASC");
$roles = $stmt_roles->fetchAll();

// Obtener lista de usuarios
$stmt_usuarios = $pdo->query("
    SELECT u.id_usuario, u.nombre, u.email, u.fecha_registro, u.activo, r.nombre as rol_nombre 
    FROM Usuarios u 
    JOIN Roles r ON u.id_rol = r.id_rol 
    ORDER BY u.id_usuario DESC
");
$usuarios = $stmt_usuarios->fetchAll();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - QueHayPaHacer</title>
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
        
        .table-container { overflow-x: auto; margin-top: 2rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        th { color: var(--text-muted); font-weight: 600; }
        
        .alert-banner { padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 2rem; }
        .alert-banner.success { background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; }
        .alert-banner.danger { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; }
        .alert-banner.warning { background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #fbbf24; }

        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
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
                <h1>Gestión de Usuarios y Roles</h1>
                <p style="color: var(--text-muted);">Administra los accesos, crea nuevos usuarios y asigna permisos.</p>
            </div>

            <?php if (!empty($mensaje)): ?>
            <div class="alert-banner <?php echo $tipo_alerta; ?>">
                <?php echo $mensaje; ?>
            </div>
            <?php endif; ?>

            <!-- Formulario para crear usuario -->
            <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem;">➕ Crear Nuevo Usuario</h3>
                <form method="POST" action="">
                    <input type="hidden" name="crear_usuario" value="1">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Rol del Usuario</label>
                            <select name="id_rol" class="form-input" required>
                                <option value="">Selecciona un rol</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?php echo $rol->id_rol; ?>"><?php echo htmlspecialchars($rol->nombre); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">Crear Usuario</button>
                </form>
            </div>

            <!-- Lista de Usuarios -->
            <div class="glass-panel" style="padding: 2rem;">
                <h3 style="margin-bottom: 1rem;">👥 Usuarios Registrados</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><?php echo $u->id_usuario; ?></td>
                                <td><?php echo htmlspecialchars($u->nombre); ?></td>
                                <td><?php echo htmlspecialchars($u->email); ?></td>
                                <td>
                                    <span style="background: rgba(79, 70, 229, 0.2); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.85rem; color: #a5b4fc;">
                                        <?php echo htmlspecialchars($u->rol_nombre); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($u->activo): ?>
                                        <span style="color: #34d399;">Activo</span>
                                    <?php else: ?>
                                        <span style="color: #f87171;">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($u->fecha_registro)); ?></td>
                                <td>
                                    <a href="editar_usuario.php?id=<?php echo $u->id_usuario; ?>" class="btn" style="padding: 0.4rem 0.8rem; background: rgba(255,255,255,0.1); color: white; font-size: 0.9rem; margin-right: 0.5rem;">✏️ Editar</a>
                                    
                                    <?php if ($u->id_usuario !== $_SESSION['usuario_id']): ?>
                                        <a href="usuarios.php?eliminar=<?php echo $u->id_usuario; ?>" onclick="return confirm('¿Estás seguro de eliminar a este usuario? Esta acción no se puede deshacer.');" class="btn" style="padding: 0.4rem 0.8rem; background: rgba(239,68,68,0.2); color: #f87171; font-size: 0.9rem;">🗑️ Eliminar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
