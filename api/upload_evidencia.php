<?php
session_start();
header('Content-Type: application/json');

// Verificar token CSRF
$csrf_token = $_POST['csrf_token'] ?? '';
if ($csrf_token !== (sessionStorage::get('csrf_token') ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Token CSRF inválido']);
    exit;
}

// Verificar tracking_code
$tracking_code = $_GET['tracking_code'] ?? '';
if (empty($tracking_code)) {
    echo json_encode(['success' => false, 'error' => 'Tracking code requerido']);
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions/security.php';

$uploadDir = __DIR__ . '/../uploads/evidence/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$uploadedFiles = [];
$errors = [];

// PROCESAR CADA ARCHIVO
foreach ($_FILES['evidence']['name'] as $key => $filename) {
    if ($_FILES['evidence']['error'][$key] === UPLOAD_ERR_OK) {
        $tmpPath = $_FILES['evidence']['tmp_name'][$key];
        $fileSize = $_FILES['evidence']['size'][$key];
        $mimeType = mime_content_type($tmpPath);
        
        // Validaciones
        if ($fileSize > 10 * 1024 * 1024) {
            $errors[] = "$filename excede 10MB";
            continue;
        }
        
        // Verificar tipo por extensión y mime
        if (!isValidFileType($tmpPath, $mimeType)) {
            $errors[] = "$filename tiene formato no permitido";
            continue;
        }
        
        // Generar nombre único
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $newFilename = uniqid('ev_', true) . '.' . strtolower($extension);
        $destination = $uploadDir . $newFilename;
        
        // Mover archivo
        if (move_uploaded_file($tmpPath, $destination)) {
            // ============== ELIMINACIÓN DE METADATOS ==============
            $result = eliminarMetadatos($destination, $mimeType);
            
            if ($result) {
                // Guardar en BD
                $stmt = $pdo->prepare("INSERT INTO evidencias (tracking_code, filename, file_path, file_size, mime_type, fecha_subida) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$tracking_code, $filename, $newFilename, $fileSize, $mimeType]);
                
                $uploadedFiles[] = [
                    'filename' => $filename,
                    'stored_name' => $newFilename,
                    'size' => formatBytes($fileSize),
                    'type' => $mimeType,
                    'metadata_stripped' => true
                ];
            } else {
                $errors[] = "No se pudo procesar $filename";
                unlink($destination);
            }
        } else {
            $errors[] = "Error al mover archivo";
        }
    }
}

echo json_encode([
    'success' => empty($errors),
    'files' => $uploadedFiles,
    'errors' => $errors
]);

// ===========================================
// FUNCIONES DE ELIMINACIÓN DE METADATOS
// ===========================================

function eliminarMetadatos($filePath, $mimeType) {
    try {
        if (strpos($mimeType, 'image/') === 0) {
            return eliminarMetadatosImagen($filePath, $mimeType);
        } elseif (strpos($mimeType, 'video/') === 0) {
            return eliminarMetadatosVideo($filePath);
        } elseif ($mimeType === 'application/pdf') {
            return eliminarMetadatosPDF($filePath);
        }
        return true; // Otros formatos no procesados
    } catch (Exception $e) {
        error_log("Error eliminando metadatos: " . $e->getMessage());
        return false;
    }
}

function eliminarMetadatosImagen($filePath, $mimeType) {
    $info = getimagesize($filePath);
    if (!$info) return false;
    
    $mime = $info['mime'];
    $img = null;
    
    switch ($mime) {
        case 'image/jpeg':
            $img = imagecreatefromjpeg($filePath);
            imagejpeg($img, $filePath, 95);
            break;
        case 'image/png':
            $img = imagecreatefrompng($filePath);
            imagepng($img, $filePath, 6);
            break;
        case 'image/gif':
            $img = imagecreatefromgif($filePath);
            imagegif($img, $filePath);
            break;
        case 'image/webp':
            $img = imagecreatefromwebp($filePath);
            imagewebp($img, $filePath, 95);
            break;
        default:
            return false;
    }
    
    if ($img) imagedestroy($img);
    return true;
}

function eliminarMetadatosVideo($filePath) {
    // Requiere FFmpeg instalado
    $output = $filePath . '.tmp';
    $cmd = "ffmpeg -i " . escapeshellarg($filePath) . " -map_metadata -1 -c:v copy -c:a copy " . escapeshellarg($output) . " 2>&1";
    
    exec($cmd, $output, $returnCode);
    
    if ($returnCode === 0 && file_exists($output)) {
        unlink($filePath);
        rename($output, $filePath);
        return true;
    }
    return false;
}

function eliminarMetadatosPDF($filePath) {
    // Requiere pdftk instalado
    $output = $filePath . '.tmp';
    $cmd = "pdftk " . escapeshellarg($filePath) . " output " . escapeshellarg($output) . " 2>&1";
    
    exec($cmd, $output, $returnCode);
    
    if ($returnCode === 0 && file_exists($output)) {
        unlink($filePath);
        rename($output, $filePath);
        return true;
    }
    return false;
}

// ===========================================
// FUNCIONES AUXILIARES
// ===========================================

function isValidFileType($filePath, $mimeType) {
    $allowedTypes = [
        'image/jpeg', 'image/png', 'image/heic', 'image/heif', 'image/webp',
        'video/mp4', 'video/quicktime', 'video/3gpp', 'video/webp',
        'application/pdf', 'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'heic', 'heif', 'webp', 'mp4', 'mov', '3gp', 'webm', 'pdf', 'doc', 'docx'];
    
    return in_array($mimeType, $allowedTypes) && in_array($extension, $allowedExtensions);
}

function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 1) . ' ' . $units[$i];
}
?>