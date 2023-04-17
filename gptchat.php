<?php 
// https://github.com/VolkanSah/ChatGPT-4-API-Integration-PHP-JS
// set headers for more security
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Importieren der Konfigurationsdatei
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $message = urlencode($input['message']);
    
    $url = "https://api.openai.com/v1/engines/" . MODEL . "/completions?api_key=" . OPENAI_API_KEY . "&temperature=" . TEMPERATURE . "&max_tokens=" . MAX_TOKENS . "&top_p=" . TOP_P . "&frequency_penalty=" . FREQUENCY_PENALTY . "&presence_penalty=" . PRESENCE_PENALTY . "&stop=" . urlencode('[" Human:", " AI:"]') . "&prompt=" . $message;

    $response = file_get_contents($url);
    echo $response;
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
