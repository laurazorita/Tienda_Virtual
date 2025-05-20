<?php
// Mostrar todos los errores (solo en desarrollo, no en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();

    // Evitar fijación de sesión (una sola vez)
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }
}

// Configuración de conexión a la base de datos
$host = 'localhost';
$db   = 'tienda1';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$port = '3307';

// DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";

// Opciones recomendadas para PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Mostrar excepciones en errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devolver resultados como arrays asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usar sentencias preparadas nativas
];

// Conexión a la base de datos
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "Conexión exitosa"; // Descomentar para pruebas
} catch (PDOException $e) {
    // Mostrar mensaje de error (en producción deberías registrar esto en un log en lugar de mostrarlo)
    die("Error de conexión: " . $e->getMessage());
}

// Inicializar carrito en la sesión si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
?>
