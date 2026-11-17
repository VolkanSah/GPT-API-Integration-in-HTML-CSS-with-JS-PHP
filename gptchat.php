<?php
/**
 * Chat backend for any OpenAI-compatible API.
 *
 * Accepts   POST {"messages":[{"role":"user","content":"..."}, ...]}
 * Streams   Server-Sent Events:  data: {"delta":"..."}  ...  data: [DONE]
 */

$configFile = file_exists(__DIR__ . '/config.php') ? __DIR__ . '/config.php' : __DIR__ . '/config.example.php';
$config = require $configFile;

// ---------------------------------------------------------------- helpers

function fail($code, $message)
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['error' => $message]);
    exit;
}

function upstream_error_message($body, $fallback)
{
    $json = json_decode($body, true);
    if (is_array($json)) {
        if (isset($json['error']['message']) && is_string($json['error']['message'])) {
            return $json['error']['message'];
        }
        if (isset($json['error']) && is_string($json['error'])) {
            return $json['error'];
        }
    }
    return $fallback;
}

function rate_limited($limitPerMinute)
{
    if ($limitPerMinute <= 0) {
        return false;
    }
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $file = sys_get_temp_dir() . '/gptchat_rl_' . hash('sha256', $ip);
    $fh = fopen($file, 'c+');
    if (!$fh) {
        return false; // do not block users if the temp dir is not writable
    }
    flock($fh, LOCK_EX);
    $now = time();
    $stored = trim((string) stream_get_contents($fh));
    $times = [];
    foreach ($stored === '' ? [] : explode(',', $stored) as $t) {
        if ($now - (int) $t < 60) {
            $times[] = (int) $t;
        }
    }
    $limited = count($times) >= $limitPerMinute;
    if (!$limited) {
        $times[] = $now;
        ftruncate($fh, 0);
        rewind($fh);
        fwrite($fh, implode(',', $times));
    }
    flock($fh, LOCK_UN);
    fclose($fh);
    return $limited;
}

function api_headers($config)
{
    $headers = ['Content-Type: application/json'];
    if ($config['api_key'] !== '') {
        $headers[] = 'Authorization: Bearer ' . $config['api_key'];
    }
    return $headers;
}

function moderate($config, $text)
{
    $ch = curl_init(rtrim($config['api_base'], '/') . '/moderations');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['model' => $config['moderation_model'], 'input' => $text]),
        CURLOPT_HTTPHEADER => api_headers($config),
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
    ]);
    $body = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $json = ($body !== false && $status === 200) ? json_decode($body, true) : null;
    if (!isset($json['results'][0]['flagged'])) {
        if ($config['moderation_fail_open']) {
            return;
        }
        fail(503, 'Moderation is currently unavailable.');
    }
    if ($json['results'][0]['flagged']) {
        fail(400, 'Your message was blocked by the content filter.');
    }
}

// ------------------------------------------------------------------- CORS

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '' && in_array($origin, $config['allowed_origins'], true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST, OPTIONS');
    fail(405, 'Method not allowed.');
}

// ------------------------------------------------------------- validation

if (rate_limited($config['rate_limit_per_minute'])) {
    header('Retry-After: 60');
    fail(429, 'Too many requests. Please wait a moment.');
}

$input = json_decode(file_get_contents('php://input'), true);
$messages = $input['messages'] ?? null;

if (!is_array($messages) || count($messages) === 0) {
    fail(400, 'Field "messages" must be a non-empty array.');
}
if (count($messages) > $config['max_messages']) {
    fail(400, 'Conversation is too long. Please start a new chat.');
}

$clean = [];
$userText = [];
foreach ($messages as $m) {
    $role = $m['role'] ?? '';
    $content = $m['content'] ?? null;
    if (!in_array($role, ['user', 'assistant'], true) || !is_string($content) || trim($content) === '') {
        fail(400, 'Invalid message format.');
    }
    if (mb_strlen($content) > $config['max_message_chars']) {
        fail(400, 'A message is too long (max ' . $config['max_message_chars'] . ' characters).');
    }
    $clean[] = ['role' => $role, 'content' => $content];
    if ($role === 'user') {
        $userText[] = $content;
    }
}
if (end($clean)['role'] !== 'user') {
    fail(400, 'The last message must come from the user.');
}

if ($config['moderation_enabled']) {
    moderate($config, implode("\n", $userText));
}

// --------------------------------------------------------------- upstream

$payload = [
    'model' => $config['model'],
    'messages' => array_merge([['role' => 'system', 'content' => $config['system_prompt']]], $clean),
    'stream' => true,
    'temperature' => $config['temperature'],
    $config['max_tokens_param'] => $config['max_tokens'],
];

$status = 0;
$started = false;
$buffer = '';
$errorBody = '';

$emit = function ($data) {
    echo 'data: ' . $data . "\n\n";
    if (ob_get_level() > 0) {
        ob_flush();
    }
    flush();
};

$handleLine = function ($line) use ($emit) {
    if (strncmp($line, 'data:', 5) !== 0) {
        return;
    }
    $data = trim(substr($line, 5));
    if ($data === '' || $data === '[DONE]') {
        return;
    }
    $json = json_decode($data, true);
    $delta = $json['choices'][0]['delta']['content'] ?? '';
    if (is_string($delta) && $delta !== '') {
        $emit(json_encode(['delta' => $delta]));
    }
};

$ch = curl_init(rtrim($config['api_base'], '/') . '/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => api_headers($config),
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 180,
    CURLOPT_HEADERFUNCTION => function ($ch, $header) use (&$status) {
        if (preg_match('#^HTTP/\S+\s+(\d{3})#', $header, $m)) {
            $status = (int) $m[1];
        }
        return strlen($header);
    },
    CURLOPT_WRITEFUNCTION => function ($ch, $chunk) use (&$status, &$started, &$buffer, &$errorBody, $handleLine) {
        if ($status !== 200) {
            $errorBody .= $chunk;
            return strlen($chunk);
        }
        if (!$started) {
            $started = true;
            header('Content-Type: text/event-stream; charset=utf-8');
            header('Cache-Control: no-cache');
            header('X-Accel-Buffering: no'); // disable nginx buffering
        }
        $buffer .= $chunk;
        while (($pos = strpos($buffer, "\n")) !== false) {
            $line = trim(substr($buffer, 0, $pos));
            $buffer = substr($buffer, $pos + 1);
            $handleLine($line);
        }
        if (connection_aborted()) {
            return 0; // client left, stop the upstream request
        }
        return strlen($chunk);
    },
]);

$ok = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if (!$started) {
    if ($status === 429) {
        fail(429, 'The AI provider is rate limiting requests. Please try again shortly.');
    }
    if ($status !== 0 && $status !== 200) {
        error_log('gptchat upstream HTTP ' . $status . ': ' . $errorBody);
        fail(502, upstream_error_message($errorBody, 'The AI provider returned an error (HTTP ' . $status . ').'));
    }
    error_log('gptchat upstream failure: ' . $curlError);
    fail(502, 'Could not reach the AI provider.');
}

if ($buffer !== '') {
    $handleLine(trim($buffer));
}
if ($ok === false && !connection_aborted()) {
    error_log('gptchat stream interrupted: ' . $curlError);
    $emit(json_encode(['error' => 'The connection to the AI provider was interrupted.']));
}
$emit('[DONE]');
