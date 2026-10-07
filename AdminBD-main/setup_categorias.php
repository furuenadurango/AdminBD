<?php
require 'includes/db.php';

$categorias = [
    ['nombre' => 'Restaurantes y Comida', 'tipo' => 'Comercio', 'descripcion' => 'Lugares para comer o beber.'],
    ['nombre' => 'Hoteles y Hospedaje', 'tipo' => 'Comercio', 'descripcion' => 'Sitios para descansar y dormir.'],
    ['nombre' => 'Tiendas y Supermercados', 'tipo' => 'Comercio', 'descripcion' => 'Compra de víveres y productos.'],
    ['nombre' => 'Bares y Discotecas', 'tipo' => 'Comercio', 'descripcion' => 'Entretenimiento nocturno.'],
    ['nombre' => 'Ecoturismo y Aventura', 'tipo' => 'Turismo', 'descripcion' => 'Actividades al aire libre.'],
    ['nombre' => 'Museos y Cultura', 'tipo' => 'Turismo', 'descripcion' => 'Sitios de interés cultural e histórico.']
];

try {
    foreach ($categorias as $cat) {
        $stmt = $pdo->prepare("SELECT id_categoria FROM Categorias WHERE nombre = ?");
        $stmt->execute([$cat['nombre']]);
        if ($stmt->rowCount() == 0) {
            $insert = $pdo->prepare("INSERT INTO Categorias (nombre, tipo, descripcion) VALUES (?, ?, ?)");
            $insert->execute([$cat['nombre'], $cat['tipo'], $cat['descripcion']]);
            echo "✅ Categoría agregada: " . $cat['nombre'] . "\n";
        }
    }
    echo "Proceso de categorías completado.\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
