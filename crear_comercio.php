<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
require 'includes/db.php';

// Obtener categorías de tipo "Comercio"
$categorias = [];
try {
    $stmt = $pdo->query("SELECT id_categoria, nombre FROM Categorias WHERE tipo = 'Comercio' ORDER BY nombre");
    $categorias = $stmt->fetchAll();
} catch (Exception $e) {
    die("Error al cargar categorías.");
}

$mensaje = "";
$error = "";

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $categoria = $_POST['categoria'];
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);
    $descripcion = trim($_POST['descripcion']);
    $id_propietario = $_SESSION['usuario_id']; // El usuario actual será el propietario

    if (empty($nombre) || empty($categoria) || empty($direccion)) {
        $error = "Nombre, categoría y dirección son obligatorios.";
    } else {
        try {
            $sql = "INSERT INTO Comercios (nombre, id_categoria, direccion, telefono, descripcion, id_usuario_propietario, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, 'Pendiente')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $categoria, $direccion, $telefono, $descripcion, $id_propietario]);
            
            $mensaje = "Comercio registrado exitosamente. Está en estado 'Pendiente' de aprobación.";
        } catch (Exception $e) {
            $error = "Error al guardar: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Comercio - QueHayPaHacer</title>
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
        
        .form-container { max-width: 600px; padding: 2rem; }
        select.form-input { appearance: auto; background-color: rgba(15, 23, 42, 0.8); }
        textarea.form-input { resize: vertical; min-height: 100px; }
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
                    <h1 style="margin-bottom: 0.5rem;">Registrar Nuevo Comercio</h1>
                    <p style="color: var(--text-muted);">Ingresa los datos del establecimiento. Quedará en revisión.</p>
                </div>
                <div>
                    <a href="comercios.php" class="btn" style="border: 1px solid var(--border-color); color: white;">Volver</a>
                </div>
            </div>

            <?php if (!empty($mensaje)): ?>
                <div style="padding: 1rem; background: rgba(16,185,129,0.1); color: var(--secondary-color); border-radius: 8px; margin-bottom: 1.5rem; font-weight: 600;">
                    ✅ <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div style="padding: 1rem; background: rgba(239,68,68,0.1); color: #ef4444; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 600;">
                    ❌ <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="glass-panel form-container">
                <form action="crear_comercio.php" method="POST">
                    <div class="form-group">
                        <label class="form-label">Nombre del Comercio *</label>
                        <input type="text" name="nombre" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Categoría *</label>
                        <select name="categoria" class="form-input" required>
                            <option value="">-- Selecciona una categoría --</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat->id_categoria; ?>"><?php echo htmlspecialchars($cat->nombre); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Dirección *</label>
                        <input type="text" name="direccion" class="form-input" placeholder="Ej. Calle Principal #123" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Teléfono de Contacto</label>
                        <input type="text" name="telefono" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Descripción Breve</label>
                        <textarea name="descripcion" class="form-input" placeholder="¿Qué ofrece este comercio?"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Guardar Comercio</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
