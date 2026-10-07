<?php
// ==========================================
// SCRIPT PARA IMPORTAR LA BASE DE DATOS
// ==========================================

require 'includes/db.php'; // Usa la conexión PDO ya configurada
$backup_file = __DIR__ . '/database_backup.sql';

if (!file_exists($backup_file)) {
    die("<h1>Error</h1><p>No se encontró el archivo <strong>database_backup.sql</strong>. Por favor, asegúrate de haberlo descargado del repositorio.</p>");
}

try {
    // Leemos el contenido del archivo SQL
    $sql = file_get_contents($backup_file);
    
    // Ejecutamos todo el script SQL
    $pdo->exec($sql);
    
    echo "<h1>Importación exitosa</h1>";
    echo "<p>La base de datos ha sido restaurada con los datos de <strong>database_backup.sql</strong>.</p>";
    echo "<p>Todos los dispositivos ahora tendrán la misma información local.</p>";
    
} catch (PDOException $e) {
    echo "<h1>Error al importar</h1>";
    echo "<p>Ocurrió un error al intentar cargar la base de datos: " . $e->getMessage() . "</p>";
}
?>
