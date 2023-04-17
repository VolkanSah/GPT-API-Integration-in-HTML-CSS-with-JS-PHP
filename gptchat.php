<?php 
// https://github.com/VolkanSah/ChatGPT-4-API-Integration-PHP-JS
// set headers for more security
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Replace YOUR_API_KEY with your actual GPTChat API key
define('API_KEY', 'YOUR_API_KEY');
// function
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $message = urlencode($input['message']);
// change url to you needs and please ad limits to you openai account
    $url = "https://api.gptchat.com/v1/engines/gpt-4/complete?api_key=" . API_KEY . "&message=" . $message;
// get response
    $response = file_get_contents($url);
    echo $response; // print
} else {
    // show error if else
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
