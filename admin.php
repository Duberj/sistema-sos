<?php
// =====================================
// VERIFICACIÓN DE ARCHIVOS CRÍTICOS
// =====================================
if (!file_exists(__DIR__ . '/config.php')) {
    die("<h1>❌ ERROR CRÍTICO</h1><p>No se encontró <code>config.php</code> en: " . __DIR__ . "</p>");
}
if (!file_exists(__DIR__ . '/api/get_report.php')) {
    die("<h1>❌ ERROR CRÍTICO</h1><p>No se encontró <code>api/get_report.php</code></p>");
}

// =====================================
// INICIO DE SESIÓN Y CONFIGURACIÓN
// =====================================
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    // Redirección absoluta y segura
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: http://$host$uri/index.html");
    exit;
}
require_once __DIR__ . '/config.php';

// ... (RESTO DEL CÓDIGO PHP IGUAL QUE ANTES) ...
// FUNCIONES AUXILIARES (copia las mismas funciones de antes)
function sanitize($data) { return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8'); }
function getEmoji($tipo) {
    $map = ['harassment'=>'🚫','violence'=>'⚠️','discrimination'=>'🤝','drugs'=>'💊','weapons'=>'🔫','academic'=>'📚','suicide'=>'🆘','extortion'=>'💰','theft'=>'💼','other'=>'❓'];
    return $map[strtolower($tipo)] ?? '📋';
}
function translateType($tipo) {
    $map = ['harassment'=>'Acoso Sexual','violence'=>'Violencia','discrimination'=>'Discriminación','drugs'=>'Consumo de Drogas','weapons'=>'Armas','academic'=>'Acoso Académico','suicide'=>'Riesgo Suicida','extortion'=>'Extorsión','theft'=>'Robo','other'=>'Otro'];
    return $map[strtolower($tipo)] ?? ucfirst($tipo);
}
function translateStatus($status) {
    $map = ['pendiente'=>'Pendiente','en_proceso'=>'En proceso','resuelto'=>'Resuelto','archivado'=>'Archivado'];
    return $map[strtolower($status)] ?? ucfirst(str_replace('_', ' ', $status));
}
function getPriorityClass($p) { 
    $c = ['alta'=>'high-priority','media'=>'medium-priority','baja'=>'low-priority']; 
    return $c[strtolower($p)] ?? 'low-priority'; 
}
function getPriorityBadge($p) {
    $b = ['alta'=>['Urgente','high'],'media'=>['Medio','medium'],'baja'=>['Bajo','low']];
    $data = $b[strtolower($p)] ?? ['Bajo','low'];
    return "<div class='priority-badge priority-{$data[1]}'>{$data[0]}</div>";
}
function getStatusBadge($s) {
    $map = ['pendiente'=>['gray','Pendiente'],'en_proceso'=>['yellow','En proceso'],'resuelto'=>['green','Resuelto'],'archivado'=>['blue','Archivado']];
    $data = $map[strtolower($s)] ?? ['gray',ucfirst($s)];
    return "<span class='text-xs bg-{$data[0]}-100 text-{$data[0]}-800 px-2 py-1 rounded-full'>{$data[1]}</span>";
}
function getTimeAgo($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'unos segundos';
    if ($diff < 3600) return floor($diff/60).' minutos';
    if ($diff < 86400) return floor($diff/3600).' horas';
    return floor($diff/86400).' días';
}

// FILTRO ACTUAL - MAPEO CORRECTO
$filter = $_GET['filter'] ?? 'all';
$filter_sql = ''; 
if ($filter !== 'all') {
    $filter_map = [
        'high' => "prioridad = 'alta'",
        'pending' => "status = 'pendiente'",
        'harassment' => "tipo = 'harassment'",
        'violence' => "tipo = 'violence'",
        'drugs' => "tipo = 'drugs'"
    ];
    if (isset($filter_map[$filter])) {
        $filter_sql = 'WHERE '.$filter_map[$filter];
    }
}

// ESTADÍSTICAS
$stats_total = $pdo->query("SELECT COUNT(*) FROM denuncias")->fetchColumn();
$stats_pending = $pdo->query("SELECT COUNT(*) FROM denuncias WHERE status IN ('pendiente','en_proceso')")->fetchColumn();
$stats_resolved_today = $pdo->query("SELECT COUNT(*) FROM denuncias WHERE status='resuelto' AND DATE(fecha_creacion)=CURDATE()")->fetchColumn();

// GRÁFICOS
$cat_data = $pdo->query("SELECT tipo, COUNT(*) as c FROM denuncias GROUP BY tipo")->fetchAll();
$trend_data = $pdo->query("SELECT DATE_FORMAT(fecha_creacion,'%a') as dia, COUNT(*) as c FROM denuncias WHERE fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(fecha_creacion) ORDER BY fecha_creacion")->fetchAll();
$campus_data = $pdo->query("SELECT ubicacion_campus as campus, COUNT(*) as c FROM denuncias WHERE ubicacion_campus != '' GROUP BY ubicacion_campus")->fetchAll();

// LISTA DENUNCIAS
$stmt = $pdo->prepare("SELECT d.*, COUNT(e.id) as num_files FROM denuncias d LEFT JOIN evidencias e ON d.tracking_code = e.tracking_code $filter_sql GROUP BY d.id ORDER BY FIELD(d.prioridad,'alta','media','baja'), d.fecha_creacion DESC LIMIT 50");
$stmt->execute();
$denuncias = $stmt->fetchAll();

// ACTUALIZAR ESTADO - CORRECCIÓN CLAVE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tracking_code'], $_POST['submit_action'])) {
    $code = $_POST['tracking_code'];
    $action = $_POST['submit_action'];
    $status_map = ['mark_as_in_progress'=>'en_proceso','mark_as_resolved'=>'resuelto','request_more_info'=>'pendiente'];
    
    if (isset($status_map[$action])) {
        $new_status = $status_map[$action];
        $pdo->prepare("UPDATE denuncias SET status=?, fecha_actualizacion=NOW() WHERE tracking_code=?")
            ->execute([$new_status, $code]);
        
        // Auditoría
        $ip_hash = hash('sha256', $_SERVER['REMOTE_ADDR'] . SALT_SECRETO);
        $pdo->prepare("INSERT INTO auditoria (admin_id,accion,tracking_code,detalles,ip_hash,fecha) VALUES (?,?,?,?,?,NOW())")
             ->execute([$_SESSION['admin_id'], $action, $code, "Estado cambiado a: ".translateStatus($new_status), $ip_hash]);
        
        echo "<script>alert('✅ Estado actualizado correctamente'); window.location.href='admin_panel.php?filter=$filter';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - SOS-UNI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/plotly.js/3.0.3/plotly.min.js"></script>
    <style>
        :root { --primary:#1e3a8a; --secondary:#3b82f6; --danger:#dc2626; --success:#059669; --warning:#f59e0b; }
        body { font-family:'Inter',sans-serif; background:linear-gradient(135deg,#f8fafc 0%,#e2e8f0 100%); min-height:100vh; }
        .stat-card { background:white; border-radius:16px; padding:20px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); transition:all 0.3s ease; }
        .stat-card:hover { transform:translateY(-4px); box-shadow:0 10px 25px rgba(0,0,0,0.15); }
        .report-card { background:white; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:12px; cursor:pointer; transition:all 0.3s ease; }
        .report-card:hover { border-color:var(--secondary); box-shadow:0 4px 12px rgba(59,130,246,0.15); }
        .report-card.high-priority { border-left:4px solid var(--danger); background:rgba(220,38,38,0.02); }
        .report-card.medium-priority { border-left:4px solid var(--warning); background:rgba(245,158,11,0.02); }
        .report-card.low-priority { border-left:4px solid var(--success); background:rgba(5,150,105,0.02); }
        .filter-btn { padding:8px 16px; border-radius:8px; border:2px solid #e2e8f0; background:white; color:var(--color-neutral); font-weight:500; cursor:pointer; transition:all 0.3s ease; white-space:nowrap; }
        .filter-btn.active { border-color:var(--primary); background:var(--primary); color:white; }
        .filter-btn:hover { border-color:var(--secondary); }
        .priority-badge { padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; text-transform:uppercase; }
        .priority-badge.priority-high { background:rgba(220,38,38,0.1); color:var(--danger); }
        .priority-badge.priority-medium { background:rgba(245,158,11,0.1); color:var(--warning); }
        .priority-badge.priority-low { background:rgba(5,150,105,0.1); color:var(--success); }
        .bottom-nav { background:rgba(255,255,255,0.95); backdrop-filter:blur(10px); border-top:1px solid rgba(226,232,240,0.5); }
        .nav-item { transition:all 0.3s ease; color:#6b7280; cursor:pointer; }
        .nav-item.active { color:var(--primary); }
        .modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:50; align-items:center; justify-content:center; padding:16px; }
        .modal.active { display:flex; }
        .chart-container { background:white; border-radius:16px; padding:20px; margin-bottom:20px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gray-50 pb-20">
    <!-- Header -->
    <div class="bg-white shadow-sm px-4 py-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-900">Panel Administrativo</h1>
            <div class="flex items-center space-x-3">
                <button onclick="refreshData()" class="text-gray-600 hover:text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
                <button onclick="showSettings()" class="text-gray-600 hover:text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </button>
                <form action="logout.php" method="POST" class="inline">
                    <button type="submit" class="text-gray-600 hover:text-red-600" onclick="return confirm('¿Cerrar sesión?')">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Stats Overview -->
        <div class="grid grid-cols-3 gap-3 mt-4">
            <div class="stat-card text-center">
                <div class="text-2xl font-bold text-red-600" id="totalReports"><?= $stats_total ?></div>
                <div class="text-xs text-gray-600">Reportes totales</div>
            </div>
            <div class="stat-card text-center">
                <div class="text-2xl font-bold text-orange-600" id="pendingReports"><?= $stats_pending ?></div>
                <div class="text-xs text-gray-600">Pendientes</div>
            </div>
            <div class="stat-card text-center">
                <div class="text-2xl font-bold text-green-600" id="resolvedToday"><?= $stats_resolved_today ?></div>
                <div class="text-xs text-gray-600">Resueltos hoy</div>
            </div>
        </div>
    </div>
    
    <!-- FILTROS - MAPEO CORRECTO -->
    <div class="px-4 py-4 overflow-x-auto">
        <div class="flex space-x-2 pb-2">
            <button onclick="window.location.href='admin_panel.php?filter=all'" class="filter-btn <?= $filter=='all'?'active':'' ?>">Todos</button>
            <button onclick="window.location.href='admin_panel.php?filter=high'" class="filter-btn <?= $filter=='high'?'active':'' ?>">Alta Prioridad</button>
            <button onclick="window.location.href='admin_panel.php?filter=pending'" class="filter-btn <?= $filter=='pending'?'active':'' ?>">Pendientes</button>
            <button onclick="window.location.href='admin_panel.php?filter=harassment'" class="filter-btn <?= $filter=='harassment'?'active':'' ?>">Acoso</button>
            <button onclick="window.location.href='admin_panel.php?filter=violence'" class="filter-btn <?= $filter=='violence'?'active':'' ?>">Violencia</button>
            <button onclick="window.location.href='admin_panel.php?filter=drugs'" class="filter-btn <?= $filter=='drugs'?'active':'' ?>">Drogas</button>
        </div>
    </div>
    
    <!-- Reports List -->
    <div class="px-4">
        <div id="reportsList">
            <?php foreach ($denuncias as $d): ?>
            <div class="report-card <?= getPriorityClass($d['prioridad']) ?>" onclick="showReportDetail('<?= sanitize($d['tracking_code']) ?>')">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3"><?= getEmoji($d['tipo']) ?></span>
                        <div>
                            <div class="font-semibold text-gray-900"><?= translateType($d['tipo']) ?></div>
                            <div class="text-sm text-gray-600"><?= sanitize($d['ubicacion_campus']) ?> • Hace <?= getTimeAgo($d['fecha_creacion']) ?></div>
                        </div>
                    </div>
                    <?= getPriorityBadge($d['prioridad']) ?>
                </div>
                <p class="text-sm text-gray-700 mb-3"><?= strlen($d['descripcion'])>120 ? sanitize(substr($d['descripcion'],0,120)).'...' : sanitize($d['descripcion']) ?></p>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">ID: <?= sanitize($d['tracking_code']) ?></span>
                    <?= getStatusBadge($d['status']) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Analytics Section -->
    <div class="px-4 mt-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Análisis de Incidentes</h2>
        <div class="chart-container">
            <h3 class="font-semibold text-gray-900 mb-4">Incidentes por Categoría</h3>
            <div id="categoryChart" style="height: 250px;"></div>
        </div>
        <div class="chart-container">
            <h3 class="font-semibold text-gray-900 mb-4">Tendencias Semanales</h3>
            <div id="trendChart" style="height: 250px;"></div>
        </div>
        <div class="chart-container">
            <h3 class="font-semibold text-gray-900 mb-4">Distribución por Campus</h3>
            <div id="campusChart" style="height: 250px;"></div>
        </div>
    </div>
    
    <!-- Modal -->
    <div id="reportModal" class="modal">
        <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Detalle del Reporte</h3>
                    <button onclick="closeModal('reportModal')" class="text-gray-500 hover:text-gray-700">✕</button>
                </div>
                <div id="reportDetailContent"></div>
                <form method="POST" action="" class="mt-6 space-y-3">
                    <input type="hidden" name="tracking_code" id="modalTrackingCode">
                    <button type="submit" name="submit_action" value="mark_as_in_progress" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-3 rounded-xl font-semibold">Marcar como En Proceso</button>
                    <button type="submit" name="submit_action" value="mark_as_resolved" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-semibold">Marcar como Resuelto</button>
                    <button type="submit" name="submit_action" value="request_more_info" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold">Solicitar Más Información</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bottom Navigation -->
    <div class="bottom-nav fixed bottom-0 left-0 right-0 px-4 py-2">
        <div class="flex justify-around items-center">
            <div class="nav-item flex flex-col items-center py-2" onclick="goHome()"><span class="text-2xl mb-1">🏠</span><span class="text-xs">Inicio</span></div>
            <div class="nav-item active flex flex-col items-center py-2" onclick="showAdmin()"><span class="text-2xl mb-1">📊</span><span class="text-xs">Admin</span></div>
            <div class="nav-item flex flex-col items-center py-2" onclick="showSettings()"><span class="text-2xl mb-1">⚙️</span><span class="text-xs">Configuración</span></div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gráficos
            const categoryData = <?= json_encode(array_map(function($row) { return ['tipo'=>translateType($row['tipo']), 'c'=>$row['c']]; }, $cat_data)) ?>;
            
            Plotly.newPlot('categoryChart', [{
                x: categoryData.map(r => r.tipo),
                y: categoryData.map(r => r.c),
                type: 'bar',
                marker: { color: ['#dc2626','#f59e0b','#8b5cf6','#eab308','#dc2626','#6b7280','#ef4444','#f97316','#84cc16','#06b6d4'] }
            }], {margin:{t:20,r:20,b:60,l:40},xaxis:{tickangle:-45},yaxis:{title:'Número de casos'}}, {responsive:true});
            
            Plotly.newPlot('trendChart', [{
                x: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
                y: <?= json_encode(array_column($trend_data, 'c')) ?>,
                type: 'scatter', mode: 'lines+markers',
                line: {color:'#3b82f6',width:3}, marker:{color:'#1e3a8a',size:8}
            }], {margin:{t:20,r:20,b:40,l:40},yaxis:{title:'Reportes por día'}}, {responsive:true});
            
            Plotly.newPlot('campusChart', [{
                values: <?= json_encode(array_column($campus_data, 'c')) ?>,
                labels: <?= json_encode(array_column($campus_data, 'campus')) ?>,
                type: 'pie',
                marker: {colors: ['#1e3a8a','#3b82f6','#60a5fa','#93c5fd','#dbeafe','#bfdbfe']}
            }], {margin:{t:20,r:20,b:20,l:20}}, {responsive:true});
            
            // Animaciones
            anime({targets:'.stat-card', scale:[0.8,1], opacity:[0,1], duration:600, delay:anime.stagger(100), easing:'easeOutElastic(1,.8)'});
            anime({targets:'.report-card', translateX:[-50,0], opacity:[0,1], duration:400, delay:anime.stagger(50), easing:'easeOutCubic'});
        });
        
        function showReportDetail(code) {
            fetch('api/get_report.php?tracking_code='+code)
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        const r = d.data, c = document.getElementById('reportDetailContent'), m = document.getElementById('reportModal');
                        c.innerHTML = `
                            <div class="space-y-4">
                                <div class="flex justify-between items-center"><span class="text-sm text-gray-500">ID: ${r.tracking_code}</span><div class="priority-badge priority-${r.prioridad}">${r.prioridad=='alta'?'Urgente':r.prioridad=='media'?'Medio':'Bajo'}</div></div>
                                <div><div class="font-semibold text-gray-900 mb-2">${r.tipo_traducido}</div><div class="text-sm text-gray-600">${r.ubicacion_campus} • Hace ${r.tiempo_transcurrido}</div></div>
                                <div><div class="font-semibold text-gray-900 mb-2">Descripción:</div><p class="text-sm text-gray-700">${r.descripcion}</p></div>
                                ${r.evidencias.length>0?`<div><div class="font-semibold text-gray-900 mb-2">Evidencia:</div><div class="space-y-2">${r.evidencias.map(f=>`<div class="flex items-center bg-gray-50 rounded-lg p-2"><span class="text-lg mr-2">📎</span><span class="text-sm text-gray-700">${f.filename}</span></div>`).join('')}</div></div>`:''}
                                <div><div class="font-semibold text-gray-900 mb-2">Estado:</div><span class="text-sm bg-gray-100 text-gray-800 px-2 py-1 rounded-full">${r.status_traducido}</span></div>
                            </div>`;
                        document.getElementById('modalTrackingCode').value = r.tracking_code;
                        m.classList.add('active');
                        anime({targets:m.querySelector('.bg-white'), scale:[0.8,1], opacity:[0,1], duration:300, easing:'easeOutCubic'});
                    } else { alert('❌ Error: '+d.message); }
                })
                .catch(e => { console.error(e); alert('❌ Error al cargar detalles'); });
        }
        
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }
        function refreshData() { anime({targets:'.stat-card', rotate:[0,360], duration:600, easing:'easeInOutCubic'}); setTimeout(()=>window.location.reload(),600); }
        function goHome() { alert('🏠 Redirigiendo a inicio...'); }
        function showAdmin() { window.location.reload(); }
        function showSettings() { alert('⚙️ Función en desarrollo'); }
    </script>
</body>
</html>