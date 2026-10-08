<?php
require 'includes/db.php';

// Obtener categorías para el filtro
$categorias = [];
try {
    $stmt = $pdo->query("SELECT id_categoria, nombre FROM Categorias ORDER BY nombre");
    $categorias = $stmt->fetchAll();
} catch (Exception $e) {}

// Obtener los comercios APROBADOS
$comercios = [];
$filtro_cat = isset($_GET['categoria']) ? intval($_GET['categoria']) : 0;

try {
    $sql = "SELECT c.id_comercio, c.nombre, c.descripcion, c.direccion, c.telefono, cat.nombre as categoria 
            FROM Comercios c
            JOIN Categorias cat ON c.id_categoria = cat.id_categoria
            WHERE c.estado = 'Aprobado'";
            
    if ($filtro_cat > 0) {
        $sql .= " AND c.id_categoria = :cat";
    }
    $sql .= " ORDER BY c.fecha_creacion DESC";
    
    $stmt = $pdo->prepare($sql);
    if ($filtro_cat > 0) {
        $stmt->bindParam(':cat', $filtro_cat, PDO::PARAM_INT);
    }
    $stmt->execute();
    $comercios = $stmt->fetchAll();
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explora - QueHayPaHacer</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .public-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: var(--glass-blur);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 5%;
        }
        .hero {
            text-align: center;
            margin: 4rem 0;
        }
        .hero h1 { font-size: 3.5rem; margin-bottom: 1rem; }
        .hero p { font-size: 1.2rem; color: var(--text-muted); max-width: 600px; margin: 0 auto 2rem; }
        
        .filters {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 0.5rem 1.5rem;
            border-radius: 999px;
            border: 1px solid var(--border-color);
            background: rgba(255,255,255,0.05);
            color: var(--text-main);
            cursor: pointer;
            transition: var(--transition);
        }
        .filter-btn:hover, .filter-btn.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        .card {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }
        .card:hover { transform: translateY(-5px); }
        .card-cat { color: var(--secondary-color); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem; }
        .card-title { font-size: 1.5rem; margin-bottom: 1rem; }
        .card-desc { color: var(--text-muted); margin-bottom: 1.5rem; flex-grow: 1; }
        .card-meta { font-size: 0.9rem; color: #cbd5e1; display: flex; align-items: center; margin-bottom: 0.5rem; }
        .card-meta i { margin-right: 0.5rem; }
    </style>
</head>
<body>

    <!-- Navegación Pública -->
    <nav class="public-nav">
        <h2 class="auth-logo" style="margin:0; font-size: 1.5rem;">QueHayPaHacer</h2>
        <div>
            <a href="login.php" class="btn" style="border: 1px solid var(--border-color); padding: 0.5rem 1rem;">Iniciar Sesión</a>
        </div>
    </nav>

    <div class="container fade-in-up">
        <!-- Cabecera -->
        <div class="hero">
            <h1>Descubre tu ciudad.</h1>
            <p>Explora los mejores restaurantes, museos, tiendas y experiencias turísticas, todas verificadas en nuestro directorio oficial.</p>
        </div>

        <!-- Filtros de Categorías -->
        <div class="filters">
            <a href="index.php" class="filter-btn <?php echo ($filtro_cat == 0) ? 'active' : ''; ?>">Todos</a>
            <?php foreach ($categorias as $cat): ?>
                <a href="index.php?categoria=<?php echo $cat->id_categoria; ?>" 
                   class="filter-btn <?php echo ($filtro_cat == $cat->id_categoria) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat->nombre); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Grid de Tarjetas -->
        <div class="grid">
            <?php if (count($comercios) > 0): ?>
                <?php foreach ($comercios as $c): ?>
                    <div class="glass-panel card">
                        <div class="card-cat"><?php echo htmlspecialchars($c->categoria); ?></div>
                        <h3 class="card-title"><?php echo htmlspecialchars($c->nombre); ?></h3>
                        <p class="card-desc"><?php echo htmlspecialchars($c->descripcion ? $c->descripcion : 'Sin descripción disponible.'); ?></p>
                        
                        <div class="card-meta">
                            📍 <?php echo htmlspecialchars($c->direccion); ?>
                        </div>
                        <?php if ($c->telefono): ?>
                        <div class="card-meta">
                            📞 <?php echo htmlspecialchars($c->telefono); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 0;">
                    <h3 style="color: var(--text-muted);">No hay establecimientos en esta categoría por el momento.</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
