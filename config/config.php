<?php
// Detectar si estamos en Render (producción) o local
$isProduction = isset($_ENV['RENDER']) || !empty(getenv('RENDER'));

if ($isProduction) {
    // PRODUCCIÓN (Render)
    define('BASE_URL', '');
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_NAME', getenv('DB_NAME') ?: 'eventhub');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') ?: '');
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