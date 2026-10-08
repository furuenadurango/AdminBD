<?php
$host = '127.0.0.1';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents('schema.sql');
    if ($sql === false) {
        die("Error leyendo schema.sql\n");
    }

    $pdo->exec($sql);
    echo "Base de datos y tablas creadas exitosamente.\n";
} catch (PDOException $e) {
    echo "Error de conexion/ejecucion: " . $e->getMessage() . "\n";
}
