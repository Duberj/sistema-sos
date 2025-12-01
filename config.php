<?php
// config.php - C:\xampp\htdocs\tu-proyecto\config.php

// 🔥 ERROR REPORTING (solo desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// SALT SECRETO (cámbialo por algo aleatorio)
define('SALT_SECRETO', 'x9v3x9v3' . bin2hex(random_bytes(16)));

// CONEXIÓN XAMPP (por defecto)
define('DB_HOST', 'localhost');
define('DB_NAME', 'sos_uni_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// TEST DE CONEXIÓN
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
                   DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("❌ ERROR CRÍTICO: No se conectó a la BD. Verifica XAMPP esté encendido.<br><br><strong>Detalles:</strong> " . $e->getMessage());
}
?>