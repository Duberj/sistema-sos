<?php
header('Content-Type: application/json');
session_write_close();

$debug_file = '../debug_user_register.txt';

try {
    require_once '../config.php';
    
    $raw_data = file_get_contents('php://input');
    $data = json_decode($raw_data, true);
    
    if (!$data) throw new Exception('Datos inválidos');

    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    // Validaciones
    if (strlen($username) < 3) throw new Exception('Usuario mínimo 3 caracteres');
    if (strlen($password) < 8) throw new Exception('Contraseña mínimo 8 caracteres');
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Email inválido');
    
    // Verificar si existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) throw new Exception('Usuario ya existe');

    // Crear usuario
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email ?: null, $hash]);
    
    $response = ['success' => true];

} catch (Exception $e) {
    $response = ['success' => false, 'error' => $e->getMessage()];
    file_put_contents($debug_file, date('H:i:s') . " - ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
}

echo json_encode($response);
exit;