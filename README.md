# GPTChat

A minimal, dependency-free chat widget (PHP + vanilla JS) that works with **any OpenAI-compatible API**: OpenAI, OpenRouter, Ollama, LM Studio, llama.cpp, vLLM, and others.

## Features

- Streaming responses (Server-Sent Events), with a Stop button
- Conversation history (sent with every request)
- Provider-neutral: just change the base URL, key and model
- Optional moderation via the provider's `/moderations` endpoint
- Per-IP rate limiting, input limits, same-origin CORS by default
- API key stays on the server; the system prompt cannot be overridden by clients
- XSS-safe rendering (`textContent`), dark mode, mobile friendly
- No Composer, no npm, no build step

## Requirements

- PHP 7.4+ with the `curl` and `mbstring` extensions

## Quick start

```bash
git clone <this repo>
cd <this repo>

# Option A: environment variables
export API_BASE_URL=http://localhost:11434/v1   # e.g. Ollama
export MODEL=llama3.1
php -S localhost:8000

# Option B: config file
cp config.example.php config.php   # then edit config.php
php -S localhost:8000
```

Open http://localhost:8000.

## Configuration

Every option can be set in `config.php` or via environment variable.

| Environment variable | Default | Description |
| --- | --- | --- |
| `API_BASE_URL` | `https://api.openai.com/v1` | Base URL of the OpenAI-compatible API |
| `API_KEY` (or `OPENAI_API_KEY`) | empty | API key; leave empty for local servers |
| `MODEL` | `gpt-4o-mini` | Model name |
| `SYSTEM_PROMPT` | `You are a helpful assistant.` | Server-side system prompt |
| `TEMPERATURE` | `0.7` | Sampling temperature |
| `MAX_TOKENS` | `1000` | Max tokens per response |
| `MAX_TOKENS_PARAM` | `max_tokens` | Set to `max_completion_tokens` for newer OpenAI models |
| `MAX_MESSAGES` | `40` | Max messages per request |
| `MAX_MESSAGE_CHARS` | `4000` | Max characters per message |
| `RATE_LIMIT_PER_MINUTE` | `20` | Requests per IP per minute (`0` = off) |
| `ALLOWED_ORIGINS` | empty | Comma-separated origins allowed for CORS (empty = same-origin only) |
| `MODERATION_ENABLED` | `false` | Check user messages via `/moderations` |
| `MODERATION_MODEL` | `omni-moderation-latest` | Moderation model |
| `MODERATION_FAIL_OPEN` | `false` | Let messages through if moderation fails |

### Provider examples

```bash
# OpenAI
API_BASE_URL=https://api.openai.com/v1 API_KEY=sk-... MODEL=gpt-4o-mini

# Ollama
API_BASE_URL=http://localhost:11434/v1 MODEL=llama3.1

# LM Studio
API_BASE_URL=http://localhost:1234/v1 MODEL=your-loaded-model

# OpenRouter
API_BASE_URL=https://openrouter.ai/api/v1 API_KEY=sk-or-... MODEL=meta-llama/llama-3.1-8b-instruct
```

## How it works

```
browser ──POST {messages}──▶ gptchat.php ──POST /chat/completions (stream)──▶ provider
browser ◀─SSE {"delta":…}──  gptchat.php ◀────────── SSE chunks ───────────────
```

The backend validates the input, optionally moderates it, adds the system prompt, and forwards the stream to the browser as simplified events (`data: {"delta":"..."}` ... `data: [DONE]`).

## Security notes

- Keep `config.php` and your key out of version control (it is in `.gitignore`).
- The endpoint has no user authentication. If you expose it publicly, anyone can use **your** API quota. The built-in rate limit helps, but consider adding auth, a CAPTCHA, or a provider-side spending cap.
- Behind a reverse proxy, make sure `REMOTE_ADDR` is the real client IP, otherwise the rate limit applies to all users together.
- For nginx, response buffering is disabled via the `X-Accel-Buffering` header. On Apache with PHP-FPM you may need to disable output compression for `gptchat.php` for streaming to work smoothly.

## Upgrading from the old version

The old version used the discontinued `text-davinci-003` / `/v1/engines/...` API and a hardcoded key in `config.php`. The new `config.php` is a different format; start from `config.example.php`. If you ever committed a real key, revoke it.

## Tips

Error messages from the provider (wrong model name, invalid key, ...) are shown in the chat and logged with `error_log()` on the server.


> **Note:** This project has been rewritten from an OpenAI-specific example into a
> provider-neutral chat widget. It works with any OpenAI-compatible API (including
> local and self-hosted models). I was motivated by the idea that people should be able
> to choose their AI provider freely and, where possible, run it themselves.
> The code was tested against a local mock server. I do not provide support for
> OpenAI-specific issues; please check your provider's documentation for those.
