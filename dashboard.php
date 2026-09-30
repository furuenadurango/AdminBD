<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php?error=Debes iniciar sesión para acceder");
    exit();
}
require 'includes/db.php';

require 'includes/db.php';

// Obtener datos resumidos para el Superadministrador (Rol 1)
$estadisticas = [
    'comercios_pendientes' => 0,
    'turismo_pendiente' => 0,
    'total_usuarios' => 0
];

if ($_SESSION['usuario_rol'] == 1) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM Comercios WHERE estado = 'Pendiente'");
        $estadisticas['comercios_pendientes'] = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(*) FROM Turismo WHERE estado = 'Pendiente'");
        $estadisticas['turismo_pendiente'] = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(*) FROM Usuarios");
        $estadisticas['total_usuarios'] = $stmt->fetchColumn();
    } catch (Exception $e) {
        // Ignorar error por ahora en el dashboard
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - QueHayPaHacer</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 260px;
            background: var(--bg-card);
            backdrop-filter: var(--glass-blur);
            border-right: 1px solid var(--border-color);
            padding: 2rem 1.5rem;
        }
        .main-content {
            flex-grow: 1;
            padding: 2.5rem;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .stat-card {
            padding: 1.5rem;
            text-align: center;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0.5rem 0;
        }
        .sidebar-nav {
            margin-top: 3rem;
            list-style: none;
        }
        .sidebar-nav li {
            margin-bottom: 1rem;
        }
        .sidebar-nav a {
            color: var(--text-muted);
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            color: var(--primary-color);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        
        <!-- Sidebar -->
        <aside class="sidebar fade-in-up">
            <h2 class="auth-logo" style="font-size: 1.8rem;">QueHayPaHacer</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Panel de Administración</p>

            <ul class="sidebar-nav">
                <li><a href="dashboard.php" class="active">📊 Resumen</a></li>
                <?php if ($_SESSION['usuario_rol'] == 1): ?>
                <li><a href="comercios.php">🏪 Gestionar Comercios</a></li>
                <li><a href="#">🏖️ Aprobar Turismo</a></li>
                <li><a href="#">👥 Usuarios y Roles</a></li>
                <?php else: ?>
                <li><a href="comercios.php">🏪 Mis Negocios</a></li>
                <?php endif; ?>
                <li style="margin-top: 2rem;"><a href="logout.php" style="color: #ef4444;">🚪 Cerrar Sesión</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content fade-in-up">
            <div class="header">
                <div>
                    <h1 style="margin-bottom: 0.5rem;">Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?> 👋</h1>
                    <p style="color: var(--text-muted);">Bienvenido al sistema de administración.</p>
                </div>
                <div>
                    <span style="background: var(--primary-color); padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.9rem;">
                        <?php echo ($_SESSION['usuario_rol'] == 1) ? 'Superadministrador' : 'Proveedor'; ?>
                    </span>
                </div>
            </div>

            <?php if ($_SESSION['usuario_rol'] == 1): ?>
            <h3>Alertas Pendientes</h3>
            <div class="stat-grid">
                <div class="glass-panel stat-card">
                    <p style="color: var(--text-muted);">Comercios por Aprobar</p>
                    <div class="stat-number"><?php echo $estadisticas['comercios_pendientes']; ?></div>
                </div>
                <div class="glass-panel stat-card">
                    <p style="color: var(--text-muted);">Turismo por Aprobar</p>
                    <div class="stat-number"><?php echo $estadisticas['turismo_pendiente']; ?></div>
                </div>
                <div class="glass-panel stat-card">
                    <p style="color: var(--text-muted);">Usuarios Registrados</p>
                    <div class="stat-number"><?php echo $estadisticas['total_usuarios']; ?></div>
                </div>
            </div>
            <?php else: ?>
            <div class="glass-panel" style="padding: 2rem; margin-top: 2rem; text-align: center;">
                <h3 style="margin-bottom: 1rem;">Bienvenido a tu panel de control</h3>
                <p style="color: var(--text-muted);">Aún no tienes establecimientos registrados. ¡Empieza creando tu primer comercio!</p>
                <button class="btn btn-primary" style="margin-top: 1.5rem;">+ Crear Comercio</button>
            </div>
            <?php endif; ?>

        </main>
    </div>

</body>
</html>
