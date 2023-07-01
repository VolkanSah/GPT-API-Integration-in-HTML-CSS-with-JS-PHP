<?php
// Set the content type of the response to JSON
header('Content-Type: application/json');

// Allow cross-origin requests from any domain
header('Access-Control-Allow-Origin: *');

// Allow the POST method for cross-origin requests
header('Access-Control-Allow-Methods: POST');

// Specify allowed headers for cross-origin requests, including Content-Type and Authorization
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Include the configuration file
require_once 'config.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Decode the JSON input and store it in a variable
    $input = json_decode(file_get_contents('php://input'), true);
    
    // URL-encode the message from the input
    $message = urlencode($input['message']);
    
    // Initialize a new cURL session

    $ch = curl_init();
// Change the API endpoint URL according to your needs
// For example, use /v1/chat/completions for GPT-4, GPT-4-0314, GPT-4-32k, GPT-4-32k-0314, GPT-3.5-turbo, and GPT-3.5-turbo-0301 models
// Use /v1/completions for Lingua models like text-davinci-003, text-davinci-002, text-curie-001, text-babbage-001, and text-ada-001
// See the readme.md file for more information
// if you dont want to use /v1/engines/ than you must remove model from CURLOPT_URL and must set it in CURLOPT_POSTFIELDS like "model" => gpt-3.5-turbo-16k,
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
    // Set the Content-Type and Authorization headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: Bearer " . OPENAI_API_KEY
    ));
// Execute the cURL session and store the response
    $response = curl_exec($ch);
    // Output the response
    echo $response;
// Close the cURL session
    curl_close($ch);
} else {
     // Set the HTTP response code to 405 (Method Not Allowed) if the request method is not POST
    http_response_code(405);
    // Output an error message in JSON format
    echo json_encode(['error' => 'Method not allowed']);
}
