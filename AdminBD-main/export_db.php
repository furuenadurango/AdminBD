<?php
require 'includes/db.php';
require_once 'includes/backup_helper.php';

$backup_file = __DIR__ . '/database_backup.sql';
$resultado = exportar_base_datos($pdo, $backup_file);

header('Content-Type: text/html; charset=utf-8');
if ($resultado['status']) {
    echo "<h1>✅ Respaldo Exitoso</h1>";
    echo "<p>{$resultado['mensaje']}</p>";
    echo "<p>Archivo: <strong>database_backup.sql</strong></p>";
    echo "<p><a href='dashboard.php'>Volver al Panel</a></p>";
} else {
    echo "<h1>❌ Error</h1>";
    echo "<p>{$resultado['mensaje']}</p>";
    echo "<p><a href='dashboard.php'>Volver al Panel</a></p>";
}
