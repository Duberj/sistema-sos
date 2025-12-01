<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['admin_id'])) {
    try {
        $ip_hash = hash('sha256', $_SERVER['REMOTE_ADDR'] . SALT_SECRETO);
        $pdo->prepare("INSERT INTO auditoria (admin_id,accion,tracking_code,detalles,ip_hash,fecha) VALUES (?,?,?,?,?,NOW())")
             ->execute([$_SESSION['admin_id'], 'logout', '', 'Logout desde '.$_SERVER['REMOTE_ADDR'], $ip_hash]);
    } catch(PDOException $e) {}
}

session_destroy();
header('Location: admin_login.php');
exit;