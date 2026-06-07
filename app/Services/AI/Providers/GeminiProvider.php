<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AiProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements AiProvider
{
    private string $apiKey;
    private string $model;
    private string $endpoint;
    private int    $timeout;
    private int    $maxTokens;
    private float  $temperature;

    public function __construct()
    {
        $cfg             = config('ai.gemini');
        $this->apiKey    = $cfg['api_key']    ?? '';
        $this->model     = $cfg['model']      ?? 'gemini-2.5-flash';
        $this->endpoint  = $cfg['endpoint']   ?? 'https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent';
        $this->timeout   = $cfg['timeout']    ?? 20;
        $this->maxTokens = $cfg['max_tokens'] ?? 1024;
        $this->temperature = $cfg['temperature'] ?? 0.4;
    }

    public function isConfigured(): bool
    {
        return ! empty($this->apiKey);
    }

    public function chat(array $messages, array $opts = []): string
    {
        if (! $this->isConfigured()) {
            return 'عذراً، المساعد الذكي غير مفعّل حالياً. يرجى التواصل مع الدعم.';
        }

        $system     = $opts['system']      ?? '';
        $maxTokens  = $opts['max_tokens']  ?? $this->maxTokens;
        $temperature= $opts['temperature'] ?? $this->temperature;

        // تحويل رسائلنا لصيغة Gemini
        $contents = [];
        foreach ($messages as $msg) {
            $role = $msg['role'] === 'assistant' ? 'model' : 'user';
            $contents[] = [
                'role'  => $role,
                'parts' => [['text' => $msg['content']]],
            ];
        }

        $body = [
            'contents'          => $contents,
            'generationConfig'  => [
                'temperature'     => $temperature,
                'maxOutputTokens' => $maxTokens,
            ],
        ];

        if (! empty($system)) {
            $body['systemInstruction'] = ['parts' => [['text' => $system]]];
        }

        $url = str_replace('{model}', $this->model, $this->endpoint);

        try {
            $response = Http::timeout($this->timeout)
                ->withQueryParameters(['key' => $this->apiKey])
                ->post($url, $body);

            if ($response->status() === 429) {
                return 'وصلنا للحد الأقصى من الطلبات مؤقتاً. جرّب مرة أخرى بعد دقيقة.';
            }

            if ($response->status() === 400) {
                Log::warning('Gemini 400', ['body' => $response->body()]);
                return 'تعذّر معالجة طلبك. جرّب صياغة مختلفة.';
            }

            if (! $response->successful()) {
                Log::error('Gemini error', ['status' => $response->status(), 'body' => $response->body()]);
                return 'حدث خطأ مؤقت. يرجى المحاولة مرة أخرى.';
            }

            $data = $response->json();

            return $data['candidates'][0]['content']['parts'][0]['text']
                ?? 'لم أتمكن من توليد رد. جرّب مرة أخرى.';

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning('Gemini timeout', ['msg' => $e->getMessage()]);
            return 'انتهت مهلة الاتصال. جرّب مرة أخرى.';
        } catch (\Throwable $e) {
            Log::error('Gemini unexpected', ['msg' => $e->getMessage()]);
            return 'حدث خطأ غير متوقع. يرجى المحاولة لاحقاً.';
        }
    }
}
