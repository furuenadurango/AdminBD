<?php
// ==========================================
// SCRIPT PARA EXPORTAR LA BASE DE DATOS
// ==========================================

// Ruta por defecto de mysqldump en XAMPP para Windows
$mysqldump_path = 'c:\\xampp\\mysql\\bin\\mysqldump.exe';
$db_name = 'quehaypahacer';
$user = 'root';
$backup_file = __DIR__ . '/database_backup.sql';

// Si mysqldump no está en la ruta de XAMPP, intentamos usarlo asumiendo que está en el PATH
if (!file_exists($mysqldump_path)) {
    $mysqldump_path = 'mysqldump';
}

// Comando para exportar (sin contraseña porque root no tiene por defecto)
$command = "\"$mysqldump_path\" -u $user $db_name > \"$backup_file\" 2>&1";

exec($command, $output, $return_var);

if ($return_var === 0) {
    echo "<h1>Copia de seguridad exitosa</h1>";
    echo "<p>La base de datos ha sido exportada correctamente al archivo: <strong>database_backup.sql</strong></p>";
    echo "<p>Este archivo ahora puede ser subido al repositorio (git commit) para compartir los datos con otros dispositivos.</p>";
} else {
    echo "<h1>Error al exportar</h1>";
    echo "<p>No se pudo exportar la base de datos. Código de salida: $return_var</p>";
    echo "<pre>" . implode("\n", $output) . "</pre>";
}
?>
