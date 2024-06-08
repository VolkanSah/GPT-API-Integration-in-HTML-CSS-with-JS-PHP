<?php
// index.php
header('Content-Type: application/json');

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$message = $_POST['message'];
$fileUrl = null;

if (!empty($_FILES['file']['name'])) {
    $fileName = basename($_FILES['file']['name']);
    $targetFilePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
        $fileUrl = 'uploads/' . $fileName;
    } else {
        echo json_encode(['error' => 'File upload failed']);
        exit;
    }
}

$apiKey = 'YOUR_API_KEY';
$apiUrl = 'https://api.openai.com/v1/engines/gpt-4/completions';

$data = [
    'prompt' => $message,
    'max_tokens' => 150
];

$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n" .
                    "Authorization: Bearer $apiKey\r\n",
        'method' => 'POST',
        'content' => json_encode($data),
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($apiUrl, false, $context);
if ($response === FALSE) {
    echo json_encode(['error' => 'API request failed']);
    exit;
}

$responseData = json_decode($response, true);
$reply = $responseData['choices'][0]['text'];

echo json_encode(['message' => $reply, 'imageUrl' => $fileUrl]);
?>
