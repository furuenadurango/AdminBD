<?php
// Configuración de la base de datos
$host = '127.0.0.1';
$dbname = 'quehaypahacer';
$username = 'root';
// TODO: Si tu usuario root tiene contraseña (como vimos en el error anterior),
// por favor colócala entre las comillas de la variable $password.
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Configurar PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Configurar el modo de fetch por defecto a objetos
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $e) {
    // Si hay error en la conexión, se detiene la ejecución
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>
