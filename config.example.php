<?php
/**
 * Configuration.
 *
 * Copy this file to config.php (which is git-ignored) and adjust it,
 * or leave it as is and set the environment variables instead.
 * NEVER commit real API keys.
 */

function env(string $key, $default = '')
{
    $value = getenv($key);
    return ($value === false || $value === '') ? $default : $value;
}

return [
    // Base URL of any OpenAI-compatible API (no trailing slash needed).
    // OpenAI:      https://api.openai.com/v1
    // Ollama:      http://localhost:11434/v1
    // LM Studio:   http://localhost:1234/v1
    // llama.cpp:   http://localhost:8080/v1
    // OpenRouter:  https://openrouter.ai/api/v1
    'api_base' => env('API_BASE_URL', 'https://api.openai.com/v1'),

    // API key. Leave empty for local servers that need none.
    'api_key' => env('API_KEY', env('OPENAI_API_KEY', '')),

    // Model name as your provider expects it, e.g. "gpt-4o-mini" or "llama3.1".
    'model' => env('MODEL', 'gpt-4o-mini'),

    // Server-side system prompt (clients cannot override it).
    'system_prompt' => env('SYSTEM_PROMPT', 'You are a helpful assistant.'),

    'temperature' => (float) env('TEMPERATURE', 0.7),

    'max_tokens' => (int) env('MAX_TOKENS', 1000),

    // Some newer OpenAI models require "max_completion_tokens" instead.
    'max_tokens_param' => env('MAX_TOKENS_PARAM', 'max_tokens'),

    // --- Limits -------------------------------------------------------

    // Max number of messages (history) accepted per request.
    'max_messages' => (int) env('MAX_MESSAGES', 40),

    // Max characters per single message.
    'max_message_chars' => (int) env('MAX_MESSAGE_CHARS', 4000),

    // Requests per minute per IP. 0 disables rate limiting.
    'rate_limit_per_minute' => (int) env('RATE_LIMIT_PER_MINUTE', 20),

    // --- CORS ---------------------------------------------------------

    // Comma-separated list of allowed origins, e.g. "https://example.com".
    // Empty = same-origin only (recommended unless you embed the chat elsewhere).
    'allowed_origins' => array_filter(array_map('trim', explode(',', env('ALLOWED_ORIGINS', '')))),

    // --- Moderation (optional) ---------------------------------------

    // Checks user messages against the provider's /moderations endpoint.
    // Only enable if your provider supports it (OpenAI does, most local servers don't).
    'moderation_enabled' => filter_var(env('MODERATION_ENABLED', 'false'), FILTER_VALIDATE_BOOLEAN),
    'moderation_model' => env('MODERATION_MODEL', 'omni-moderation-latest'),

    // If the moderation call itself fails: true = let the message through, false = block it.
    'moderation_fail_open' => filter_var(env('MODERATION_FAIL_OPEN', 'false'), FILTER_VALIDATE_BOOLEAN),
];
