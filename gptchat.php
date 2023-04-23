<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $message = urlencode($input['message']);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/engines/" . MODEL . "/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
        "prompt" => $message,
        "max_tokens" => MAX_TOKENS,
        "temperature" => TEMPERATURE,
        "top_p" => TOP_P,
        "frequency_penalty" => FREQUENCY_PENALTY,
        "presence_penalty" => PRESENCE_PENALTY
    )));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: Bearer " . OPENAI_API_KEY
    ));

    $response = curl_exec($ch);
    echo $response;

    curl_close($ch);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}