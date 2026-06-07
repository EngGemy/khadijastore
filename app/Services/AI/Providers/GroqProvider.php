<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AiProvider;

class GroqProvider implements AiProvider
{
    public function isConfigured(): bool
    {
        return ! empty(config('ai.groq.api_key'));
    }

    public function chat(array $messages, array $opts = []): string
    {
        // TODO: implement Groq API (OpenAI-compatible endpoint)
        // POST https://api.groq.com/openai/v1/chat/completions
        // Authorization: Bearer {api_key}
        return 'Groq provider غير مفعّل بعد.';
    }
}
