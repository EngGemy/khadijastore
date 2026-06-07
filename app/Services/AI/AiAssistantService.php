<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\AiProvider;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\GroqProvider;
use App\Services\AI\Providers\OllamaProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AiAssistantService
{
    private AiProvider $provider;
    private AssistantContextBuilder $contextBuilder;

    public function __construct(AssistantContextBuilder $contextBuilder)
    {
        $this->contextBuilder = $contextBuilder;
        $this->provider       = $this->resolveProvider();
    }

    /**
     * الرد على رسالة المستخدم مع تحميل السياق من الداتابيز.
     *
     * @return array{reply: string, products: array}
     */
    public function chat(string $userMessage, array $history = [], ?int $brandId = null): array
    {
        if (! config('ai.enabled', true)) {
            return ['reply' => 'المساعد الذكي غير مفعّل حالياً.', 'products' => []];
        }

        if (! $this->provider->isConfigured()) {
            return [
                'reply'    => 'عذراً، المساعد غير مهيّأ بعد. يرجى التواصل مع الدعم.',
                'products' => [],
            ];
        }

        // كاش الردود المتكررة
        $cacheKey = 'ai.chat.' . md5($userMessage . '.' . ($brandId ?? 'all'));
        $cached   = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        // بناء السياق من الداتابيز (RAG)
        $ctx = $this->contextBuilder->build($userMessage, $brandId);

        // بناء قائمة الرسائل
        $messages = [];
        foreach (array_slice($history, -6) as $h) {  // آخر 6 فقط لتقليل التوكنز
            if (! empty($h['role']) && ! empty($h['content'])) {
                $messages[] = ['role' => $h['role'], 'content' => mb_substr($h['content'], 0, 400)];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $reply = $this->provider->chat($messages, [
            'system'      => $ctx['system'],
            'max_tokens'  => config('ai.gemini.max_tokens', 1024),
            'temperature' => config('ai.gemini.temperature', 0.4),
        ]);

        // استخرج معرّفات المنتجات المذكورة في الرد
        $mentionedProducts = $this->extractMentionedProducts($reply, $ctx['products']);

        $result = [
            'reply'    => $reply,
            'products' => $mentionedProducts,
        ];

        Cache::put($cacheKey, $result, config('ai.cache_ttl', 600));

        return $result;
    }

    /**
     * مقارنة منتجات بالبيانات الحقيقية (بدون LLM).
     *
     * @param  int[]  $ids
     */
    public function compare(array $ids, ?int $brandId = null): array
    {
        $ids = array_slice(array_unique(array_filter($ids)), 0, 4);
        if (count($ids) < 2) {
            return [];
        }

        $products = \App\Models\Product::withoutGlobalScopes()
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->with(['variants:id,product_id,name,price,stock,track_stock', 'media'])
            ->when($brandId, fn ($q) => $q->where('brand_id', $brandId))
            ->get();

        return $products->map(fn ($p) => [
            'id'          => $p->id,
            'name'        => $p->name,
            'price'       => $p->price,
            'compare_price'=> $p->compare_price,
            'rating'      => $p->rating,
            'thumb'       => $p->getFirstMediaUrl('cover', 'thumb'),
            'url'         => route('product.show', $p->slug),
            'in_stock'    => ! $p->isOutOfStock(),
            'variants'    => $p->variants->map(fn ($v) => [
                'name'  => $v->name,
                'price' => $v->price,
                'stock' => $v->stock,
            ])->values(),
        ])->values()->all();
    }

    private function extractMentionedProducts(string $reply, array $productMap): array
    {
        $mentioned = [];
        foreach ($productMap as $item) {
            $id   = $item['id'] ?? null;
            $name = $item['name'] ?? '';
            if ($id && (str_contains($reply, "[{$id}]") || mb_stripos($reply, $name) !== false)) {
                $mentioned[] = $item;
            }
        }
        return array_values(array_slice($mentioned, 0, 4));
    }

    private function resolveProvider(): AiProvider
    {
        return match (config('ai.provider', 'gemini')) {
            'groq'   => new GroqProvider(),
            'ollama' => new OllamaProvider(),
            default  => new GeminiProvider(),
        };
    }
}
