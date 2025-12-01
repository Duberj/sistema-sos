<?php
// api/clasificar_urgencia.php
header('Content-Type: application/json');

$descripcion = $_POST['descripcion'] ?? '';
if (empty($descripcion)) {
    echo json_encode(['prioridad' => 'baja', 'confianza' => 0]);
    exit;
}

// LLAMADA A GOOGLE CLOUD NATURAL LANGUAGE
$api_key = 'TU_API_KEY_AQUI';
$url = "https://language.googleapis.com/v1/documents:classifyText?key=$api_key";

$data = [
    'document' => [
        'type' => 'PLAIN_TEXT',
        'language' => 'es',
        'content' => $descripcion
    ]
];

$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);
$result = json_decode($response, true);

// CLASIFICAR POR CATEGORÍAS Y PALABRAS CLAVE
$categorias = $result['categories'] ?? [];
$texto = strtolower($descripcion);

// Palabras clave para urgencia
$palabrasAlta = ['arma', 'violencia', 'amenaza', 'suicidio', 'abuso', 'violación', 'pistola', 'cuchillo'];
$palabrasMedia = ['droga', 'robo', 'discriminación', 'acoso', 'extorsión'];

$prioridad = 'baja';
$confianza = 0.7;

// Si encuentra palabras de alta urgencia
foreach ($palabrasAlta as $palabra) {
    if (strpos($texto, $palabra) !== false) {
        $prioridad = 'alta';
        $confianza = 0.95;
        break;
    }
}

// Si no, busca palabras de media urgencia
if ($prioridad === 'baja') {
    foreach ($palabrasMedia as $palabra) {
        if (strpos($texto, $palabra) !== false) {
            $prioridad = 'media';
            $confianza = 0.85;
            break;
        }
    }
}

// Si Google detecta categorías relevantes
foreach ($categorias as $cat) {
    if (isset($cat['name'])) {
        if (strpos($cat['name'], '/Law & Government') !== false && $prioridad === 'baja') {
            $prioridad = 'media';
        }
    }
}

echo json_encode([
    'prioridad' => $prioridad,
    'confianza' => $confianza,
    'categorias' => $categorias
]);
?>