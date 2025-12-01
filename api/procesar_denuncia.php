<?php
header('Content-Type: application/json');
session_start();
session_write_close();

try {
    require_once '../config.php';
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validación
    $tipo = $data['type'] ?? '';
    $descripcion = trim($data['description'] ?? '');
    $campus = trim($data['location']['campus'] ?? '');
    
    if (empty($tipo) || strlen($descripcion) < 10 || empty($campus)) {
        throw new Exception('Datos incompletos o inválidos');
    }

    // Rate limiting
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $ip_hash = hash('sha256', $ip . date('Y-m-d') . SALT_SECRETO);
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM denuncias WHERE ip_hash = ? AND DATE(fecha_creacion) = CURDATE()");
    $stmt->execute([$ip_hash]);
    if ($stmt->fetchColumn() > 5) {
        throw new Exception('Límite de 5 denuncias diarias alcanzado');
    }

    // Generar tracking code
    do {
        $tracking_code = 'SOS-' . strtoupper(bin2hex(random_bytes(4)));
        $stmt = $pdo->prepare("SELECT id FROM denuncias WHERE tracking_code = ?");
        $stmt->execute([$tracking_code]);
    } while ($stmt->fetch());

    // 🔥 CORRECTO: Usar "tipo" (no "categoria")
    $stmt = $pdo->prepare("
        INSERT INTO denuncias (tipo, descripcion, ubicacion_campus, tracking_code, ip_hash)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$tipo, $descripcion, $campus, $tracking_code, $ip_hash]);

    $response = ['success' => true, 'tracking_code' => $tracking_code];

} catch (Exception $e) {
    $response = ['success' => false, 'error' => $e->getMessage()];
}

echo json_encode($response);
exit;
?>