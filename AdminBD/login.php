<?php
// Incluir conexión a base de datos (se usa para verificar que esté funcionando si se desea)
// require_once 'includes/db.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QueHayPaHacer - Acceso al Sistema</title>
    <!-- Referencia al archivo CSS moderno -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

    <div class="auth-container">
        <!-- Panel con efecto Glassmorphism -->
        <div class="auth-card glass-panel fade-in-up">
            
            <h1 class="auth-logo">QueHayPaHacer</h1>
            <?php
            if (isset($_GET['error'])) {
                echo '<div style="margin-bottom: 1.5rem; padding: 10px; border-radius: 8px; background: rgba(239, 68, 68, 0.1); color: #ef4444; font-weight: 600;">' . htmlspecialchars($_GET['error']) . '</div>';
            }
            ?>

            <!-- Formulario de Login -->
            <form id="loginForm" action="login_action.php" method="POST">
                
                <div class="form-group" style="text-align: left;">
                    <label for="username" class="form-label">Usuario o Correo</label>
                    <input type="text" id="username" name="username" class="form-input" placeholder="admin" required>
                </div>

                <div class="form-group" style="text-align: left;">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    Iniciar Sesión
                </button>

            </form>

            <div style="margin-top: 2rem; font-size: 0.9rem; color: var(--text-muted);">
                <p>¿Solo quieres explorar? <br><a href="index.php" class="btn" style="border: 1px solid var(--border-color); margin-top: 10px; display: inline-block;">Ver Directorio Público 🌎</a></p>
            </div>
            
            <?php
            // Bloque para verificar rápidamente la conexión a la BD en la misma pantalla (Solo para desarrollo)
            if (file_exists('includes/db.php')) {
                echo '<div style="margin-top: 15px; font-size: 0.8rem; padding: 10px; border-radius: 8px; background: rgba(16, 185, 129, 0.1); color: #10B981;">';
                try {
                    require_once 'includes/db.php';
                    echo '✓ Sistema Conectado a BD';
                } catch(Exception $e) {
                    echo '<span style="color: #ef4444;">✗ Error BD: Verifica credenciales en includes/db.php</span>';
                }
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <!-- Script de lógica frontend -->
    <script src="assets/js/app.js"></script>
</body>
</html>
