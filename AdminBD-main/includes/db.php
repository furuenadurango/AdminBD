<?php

// ==========================================
// CONFIGURACIÓN DE LA BASE DE DATOS
// ==========================================

$host = '127.0.0.1';
$port = '3306';
$dbname = 'quehaypahacer';
$username = 'root';
$password = ''; // root no tiene contraseña


// ==========================================
// CONEXIÓN A LA BASE DE DATOS
// ==========================================

try {

    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    // Mostrar errores como excepciones
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Devolver los resultados como objetos
    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_OBJ
    );

} catch (PDOException $e) {

    die(
        "Error de conexión a la base de datos: "
        . $e->getMessage()
    );

}

?>