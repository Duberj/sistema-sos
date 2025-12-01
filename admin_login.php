<?php
session_start();
require_once 'config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // MENSAJE DE DEPURACIÓN VISUAL
    $debug_msg = '';
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM administradores WHERE username = ? AND activo = 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin) {
            $debug_msg = "✅ Usuario '$username' encontrado. ";
            $debug_msg .= "Hash BD: " . substr($admin['password_hash'], 0, 20) . "... ";
            $debug_msg .= "Verifica pass: " . (password_verify($password, $admin['password_hash']) ? 'SÍ' : 'NO');
        } else {
            $debug_msg = "❌ Usuario '$username' NO encontrado en BD";
        }
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: admin.php');
            exit;
        } else {
            $error = "❌ Credenciales incorrectas";
        }
    } catch (Exception $e) {
        $error = "⚠️ Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-6">🔐 Login Admin</h2>
        
        <?php if($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
            <?= $error ?>
        </div>
        <?php endif; ?>
        
        <?php if(isset($debug_msg)): ?>
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-4 text-xs">
            <strong>Debug:</strong> <?= $debug_msg ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="space-y-4">
                <input type="text" name="username" placeholder="admin" required 
                       class="w-full border p-3 rounded-lg">
                <input type="password" name="password" placeholder="password" required 
                       class="w-full border p-3 rounded-lg">
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold">
                    Entrar
                </button>
            </div>
        </form>
    </div>
</body>
</html>