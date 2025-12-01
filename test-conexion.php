<?php
require_once 'config.php';
echo "<h1>✅ CONEXIÓN EXITOSA</h1>";
echo "<p>Base de datos: " . DB_NAME . "</p>";
echo "<p>Usuario: " . DB_USER . "</p>";

// Prueba una consulta
$stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
$result = $stmt->fetch();
echo "<p>Total usuarios: " . $result['total'] . "</p>";
?>