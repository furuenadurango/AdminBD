<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
require 'includes/db.php';

// Obtener la lista de comercios
$comercios = [];
try {
    $sql = "SELECT c.id_comercio, c.nombre, c.estado, c.fecha_creacion, cat.nombre as categoria, u.nombre as propietario
            FROM Comercios c
            JOIN Categorias cat ON c.id_categoria = cat.id_categoria
            JOIN Usuarios u ON c.id_usuario_propietario = u.id_usuario
            ORDER BY c.fecha_creacion DESC";
    $stmt = $pdo->query($sql);
    $comercios = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Error al obtener comercios: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comercios - QueHayPaHacer</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: var(--bg-card); backdrop-filter: var(--glass-blur); border-right: 1px solid var(--border-color); padding: 2rem 1.5rem; }
        .main-content { flex-grow: 1; padding: 2.5rem; }
        .sidebar-nav { margin-top: 3rem; list-style: none; }
        .sidebar-nav li { margin-bottom: 1rem; }
        .sidebar-nav a { color: var(--text-muted); font-size: 1.1rem; display: flex; align-items: center; padding: 0.5rem 0; }
        .sidebar-nav a:hover, .sidebar-nav a.active { color: var(--primary-color); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color); }
        
        .table-container { width: 100%; overflow-x: auto; margin-top: 2rem; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 1rem; border-bottom: 1px solid var(--border-color); }
        th { color: var(--text-muted); font-weight: 600; }
        .badge { padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.85rem; font-weight: 600; }
        .badge-pendiente { background: rgba(243, 213, 141, 0.6); color: #8A3B08; } /* Mantequilla y Cafe */
        .badge-aprobado { background: rgba(229, 157, 44, 0.3); color: #8A3B08; } /* Oro y Cafe */
        .badge-rechazado { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2 class="auth-logo" style="font-size: 1.8rem;">QueHayPaHacer</h2>
            <ul class="sidebar-nav">
                <li><a href="dashboard.php">📊 Resumen</a></li>
                <li><a href="comercios.php" class="active">🏪 Gestionar Comercios</a></li>
                <li style="margin-top: 2rem;"><a href="logout.php" style="color: #ef4444;">🚪 Cerrar Sesión</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content fade-in-up">
            <div class="header">
                <div>
                    <h1 style="margin-bottom: 0.5rem;">Directorio de Comercios</h1>
                    <p style="color: var(--text-muted);">Administra los establecimientos registrados.</p>
                </div>
                <div>
                    <a href="crear_comercio.php" class="btn btn-primary">+ Nuevo Comercio</a>
                </div>
            </div>

            <?php if (isset($_GET['mensaje'])): ?>
                <div style="padding: 10px; background: rgba(16,185,129,0.1); color: var(--secondary-color); border-radius: 8px; margin-bottom: 1rem; font-weight: 600;">
                    ✅ <?php echo htmlspecialchars($_GET['mensaje']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) || isset($error)): ?>
                <div style="padding: 10px; background: rgba(239,68,68,0.1); color: #ef4444; border-radius: 8px; margin-bottom: 1rem;">
                    ❌ <?php echo isset($_GET['error']) ? htmlspecialchars($_GET['error']) : $error; ?>
                </div>
            <?php endif; ?>

            <div class="glass-panel table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Propietario</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($comercios) > 0): ?>
                            <?php foreach ($comercios as $c): ?>
                            <tr>
                                <td>#<?php echo $c->id_comercio; ?></td>
                                <td style="font-weight: 600;"><?php echo htmlspecialchars($c->nombre); ?></td>
                                <td><?php echo htmlspecialchars($c->categoria); ?></td>
                                <td><?php echo htmlspecialchars($c->propietario); ?></td>
                                <td><?php echo date('d M Y', strtotime($c->fecha_creacion)); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($c->estado); ?>">
                                        <?php echo $c->estado; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($c->estado == 'Pendiente' && $_SESSION['usuario_rol'] == 1): ?>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <a href="cambiar_estado_comercio.php?id=<?php echo $c->id_comercio; ?>&estado=Aprobado" 
                                               class="btn" style="padding: 0.2rem 0.6rem; font-size: 0.8rem; background-color: var(--secondary-color); color: white;">Aprobar</a>
                                            <a href="cambiar_estado_comercio.php?id=<?php echo $c->id_comercio; ?>&estado=Rechazado" 
                                               class="btn" style="padding: 0.2rem 0.6rem; font-size: 0.8rem; background-color: #ef4444; color: white;">Rechazar</a>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-size: 0.9rem;">Sin acciones</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-muted);">No hay comercios registrados aún.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
