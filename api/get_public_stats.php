<?php
header('Content-Type: application/json');
// Conexión simple
$host = 'localhost';
$db = 'sos_uni_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM denuncias WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $stats = $stmt->fetch();
    echo json_encode(['weekly' => $stats['total']]);
} catch(Exception $e) {
    echo json_encode(['weekly' => 0]);
}
?>