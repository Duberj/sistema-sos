<?php
header('Content-Type: application/json');
session_start();
session_write_close(); // Mejorar performance

// 🔥 LOG SILENCIOSO (solo si falla)
$debug_file = '../debug_user_login.txt';

try {
    require_once '../config.php';
    
    // Leer datos
    $raw_data = file_get_contents('php://input');
    if (empty($raw_data)) {
        throw new Exception('No se recibieron datos');
    }
    
    $data = json_decode($raw_data, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON inválido');
    }

    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';
    
    // Buscar en USUARIOS (no en admin)
    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception('Usuario no encontrado');
    }

    if (!password_verify($password, $user['password_hash'])) {
        throw new Exception('Contraseña incorrecta');
    }

    // Login exitoso
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    
    $response = ['success' => true];

} catch (Exception $e) {
    $response = ['success' => false, 'error' => $e->getMessage()];
    // Guardar error silencioso
    file_put_contents($debug_file, date('H:i:s') . " - ERROR: " . $e->getMessage() . " (User: $username)\n", FILE_APPEND);
}

// Asegurar JSON limpio
echo json_encode($response);
exit;