<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) { echo json_encode(['success'=>false,'message'=>'No autorizado']); exit; }
require_once '../config.php';

$code = $_GET['tracking_code'] ?? '';
if (empty($code)) { echo json_encode(['success'=>false,'message'=>'Código requerido']); exit; }

try {
    $stmt = $pdo->prepare("SELECT d.*, GROUP_CONCAT(e.filename ORDER BY e.id) as evidencias, COUNT(e.id) as total_evidencias FROM denuncias d LEFT JOIN evidencias e ON d.tracking_code = e.tracking_code WHERE d.tracking_code = ? GROUP BY d.id");
    $stmt->execute([$code]);
    $d = $stmt->fetch();
    if (!$d) { echo json_encode(['success'=>false,'message'=>'Denuncia no encontrada']); exit; }
    
    $evidencias = [];
    if ($d['total_evidencias'] > 0 && $d['evidencias']) {
        foreach (explode(',', $d['evidencias']) as $file) {
            $evidencias[] = ['filename'=>$file];
        }
    }
    
    echo json_encode(['success'=>true,'data'=>[
        'tracking_code' => $d['tracking_code'],
        'tipo' => $d['tipo'],
        'tipo_traducido' => translateType($d['tipo']),
        'descripcion' => $d['descripcion'],
        'ubicacion_campus' => $d['ubicacion_campus'],
        'ubicacion_edificio' => $d['ubicacion_edificio'] ?? '',
        'ubicacion_area' => $d['ubicacion_area'] ?? '',
        'prioridad' => $d['prioridad'],
        'status' => $d['status'],
        'status_traducido' => translateStatus($d['status']),
        'fecha_creacion' => $d['fecha_creacion'],
        'tiempo_transcurrido' => getTimeAgo($d['fecha_creacion']),
        'evidencias' => $evidencias
    ]]);
} catch(PDOException $e) {
    echo json_encode(['success'=>false,'message'=>'Error: '.$e->getMessage()]);
}

// Funciones de traducción (copiadas aquí para independencia)
function translateType($t) { $m=['harassment'=>'Acoso Sexual','violence'=>'Violencia','discrimination'=>'Discriminación','drugs'=>'Consumo de Drogas','weapons'=>'Armas','academic'=>'Acoso Académico','suicide'=>'Riesgo Suicida','extortion'=>'Extorsión','theft'=>'Robo','other'=>'Otro']; return $m[strtolower($t)]??ucfirst($t); }
function translateStatus($s) { $m=['pendiente'=>'Pendiente','en_proceso'=>'En proceso','resuelto'=>'Resuelto','archivado'=>'Archivado']; return $m[strtolower($s)]??ucfirst(str_replace('_',' ',$s)); }
function getTimeAgo($dt) { $d=time()-strtotime($dt); if($d<60)return'unos segundos';if($d<3600)return floor($d/60).' minutos';if($d<86400)return floor($d/3600).' horas';return floor($d/86400).' días'; }