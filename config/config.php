<?php
// Detectar si estamos en Render (producción) o local
$host = $_SERVER['HTTP_HOST'] ?? '';

$isRailway = getenv('RAILWAY_ENVIRONMENT') !== false || 
             strpos($host, 'railway.app') !== false;

if ($isRailway) {
    // PRODUCCIÓN (RAILWAY) - Variables que TÚ configuras
    define('BASE_URL', '');
    define('DB_HOST', getenv('DB_HOST'));
    define('DB_NAME', getenv('DB_NAME'));
    define('DB_USER', getenv('DB_USER'));
    define('DB_PASS', getenv('DB_PASS'));
    define('DB_PORT', getenv('DB_PORT') ?: '3306');
    
    // No mostrar errores en producción
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
} else {
    // LOCAL (XAMPP)
    define('BASE_URL', '/EventHub/public');
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'eventhub');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
}

// Helper para URLs
function url($path = '') {
    return BASE_URL . ($path ? '/index.php?page=' . $path : '/index.php');
}

// Helper para rutas absolutas
function asset($path) {
    return BASE_URL . '/' . ltrim($path, '/');
}