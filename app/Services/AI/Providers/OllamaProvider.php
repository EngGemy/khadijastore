<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AiProvider;

class OllamaProvider implements AiProvider
{
    public function isConfigured(): bool
    {
        return ! empty(config('ai.ollama.host'));
    }

    public function chat(array $messages, array $opts = []): string
    {
        // TODO: implement Ollama local API
        // POST {host}/api/chat
        // body: {"model":"llama3","messages":[...],"stream":false}
        return 'Ollama provider غير مفعّل بعد.';
    }
}
