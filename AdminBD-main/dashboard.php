<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php?error=Debes iniciar sesión para acceder");
    exit();
}
require 'includes/db.php';
require_once 'includes/backup_helper.php';

$backup_file = __DIR__ . '/database_backup.sql';
$mensaje_backup = '';
$tipo_alerta = '';

// Procesar acciones de backup para Superadministrador (Rol 1)
if ($_SESSION['usuario_rol'] == 1 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion_backup'])) {
        if ($_POST['accion_backup'] === 'exportar') {
            $resultado = exportar_base_datos($pdo, $backup_file);
            if ($resultado['status']) {
                $mensaje_backup = "¡Copia de seguridad guardada con éxito en <strong>database_backup.sql</strong>! Se han guardado todos los datos actuales de comercios, categorías, usuarios y auditoría.";
                $tipo_alerta = 'success';
            } else {
                $mensaje_backup = "Error al generar la copia: " . $resultado['mensaje'];
                $tipo_alerta = 'danger';
            }
        } elseif ($_POST['accion_backup'] === 'importar') {
            $resultado = importar_base_datos($pdo, $backup_file);
            if ($resultado['status']) {
                $mensaje_backup = "¡Base de datos restaurada con éxito desde <strong>database_backup.sql</strong>!";
                $tipo_alerta = 'success';
            } else {
                $mensaje_backup = "Error al restaurar: " . $resultado['mensaje'];
                $tipo_alerta = 'danger';
            }
        }
    }
}

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

$info_backup = obtener_info_backup($backup_file);
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
        .alert-banner {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.95rem;
        }
        .alert-banner.success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }
        .alert-banner.danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
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
                <li><a href="usuarios.php">👥 Usuarios y Roles</a></li>
                <li><a href="#backup-card">💾 Copia de Seguridad</a></li>
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
                    <span style="background: var(--primary-color); color: #8A3B08; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.9rem;">
                        <?php echo ($_SESSION['usuario_rol'] == 1) ? 'Superadministrador' : 'Proveedor'; ?>
                    </span>
                </div>
            </div>

            <?php if (!empty($mensaje_backup)): ?>
            <div class="alert-banner <?php echo $tipo_alerta; ?>">
                <div><?php echo $mensaje_backup; ?></div>
            </div>
            <?php endif; ?>

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

            <!-- Panel de Copia de Seguridad Integrado -->
            <div class="glass-panel" id="backup-card" style="margin-top: 2.5rem; padding: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <h3 style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            💾 Copia y Sincronización de Base de Datos
                        </h3>
                        <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 620px; line-height: 1.5;">
                            Actualiza el archivo <code>database_backup.sql</code> con todos los datos actuales (comercios, usuarios, categorías, etc.) para sincronizar con otros equipos a través de GitHub.
                        </p>
                    </div>

                    <div style="background: rgba(255, 255, 255, 0.7); padding: 0.9rem 1.4rem; border-radius: 12px; border: 1px solid var(--border-color); font-size: 0.88rem;">
                        <div><span style="color: var(--text-muted);">Archivo:</span> <code style="color: #8A3B08;">database_backup.sql</code></div>
                        <div style="margin-top: 0.35rem;"><span style="color: var(--text-muted);">Último respaldo:</span> <strong><?php echo $info_backup['fecha']; ?></strong></div>
                        <div style="margin-top: 0.35rem;"><span style="color: var(--text-muted);">Tamaño:</span> <strong><?php echo $info_backup['tamano']; ?></strong></div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                    <form method="POST" style="margin: 0;">
                        <input type="hidden" name="accion_backup" value="exportar">
                        <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                            💾 Guardar Datos Actuales en el Archivo
                        </button>
                    </form>

                    <form method="POST" style="margin: 0;" onsubmit="return confirm('¿Deseas restaurar la base de datos desde database_backup.sql? Esto reemplazará los datos actuales con los del archivo.');">
                        <input type="hidden" name="accion_backup" value="importar">
                        <button type="submit" class="btn" style="background: rgba(255, 255, 255, 0.08); border: 1px solid var(--border-color); color: var(--text-main); display: inline-flex; align-items: center; gap: 0.5rem;">
                            📥 Cargar Datos desde el Archivo
                        </button>
                    </form>
                </div>

                <div style="margin-top: 1.5rem; padding: 0.85rem 1.1rem; border-radius: 8px; background: rgba(79, 70, 229, 0.1); border-left: 4px solid var(--primary-color); font-size: 0.88rem; color: var(--text-muted); line-height: 1.5;">
                    💡 <strong>¿Cómo compartir los cambios?</strong>
                    Al dar clic en <em>"Guardar Datos Actuales en el Archivo"</em>, el archivo <code>database_backup.sql</code> se actualizará inmediatamente con todos los comercios y usuarios actuales. Solo debes hacer commit y push a GitHub para que tus compañeros reciban todo.
                </div>
            </div>

            <?php else: ?>
            <div class="glass-panel" style="padding: 2rem; margin-top: 2rem; text-align: center;">
                <h3 style="margin-bottom: 1rem;">Bienvenido a tu panel de control</h3>
                <p style="color: var(--text-muted);">Aún no tienes establecimientos registrados. ¡Empieza creando tu primer comercio!</p>
                <a href="crear_comercio.php" class="btn btn-primary" style="margin-top: 1.5rem;">+ Crear Comercio</a>
            </div>
            <?php endif; ?>

        </main>
    </div>

</body>
</html>
