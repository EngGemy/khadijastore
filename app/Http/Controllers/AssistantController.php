<?php

namespace App\Http\Controllers;

use App\Models\AssistantLog;
use App\Services\AI\AiAssistantService;
use App\Services\AI\OrderChatFlow;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AssistantController extends Controller
{
    public function __construct(
        private readonly AiAssistantService $ai,
    ) {}

    /** صفحة المساعد الذكي */
    public function page(): View
    {
        $welcome = config('ai.welcome_message', 'مرحباً! أخبرني ماذا تبحث عنه.');

        return view('assistant.index', array_merge(
            compact('welcome'),
            $this->sharedData(),
        ));
    }

    /** نقطة نهاية الويدجت — دردشة + طلبات عبر state machine ──────────────── */
    public function widgetChat(Request $request): JsonResponse
    {
        if (! config('ai.enabled', true)) {
            return response()->json($this->widgetReply('المساعد غير مفعّل حالياً.', 'chat'));
        }

        $request->validate([
            'message' => 'required|string|max:600',
        ]);

        // Rate limit
        $key = 'ai.wrl.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 20)) {
            $secs = RateLimiter::availableIn($key);
            return response()->json($this->widgetReply("الحد الأقصى. انتظر {$secs} ثانية.", 'chat'));
        }
        RateLimiter::hit($key, 60);

        $message = trim($request->input('message'));
        $flow    = new OrderChatFlow(app(OrderService::class));

        // كلمات تشغيل طلب منتج (بدون كلمات تشير للخدمات/الدليل)
        $orderTriggers = ['اطلب', 'اشتري', 'اوردر', 'order', '__START_ORDER__'];
        // كلمات تشير لاستفسار عن دليل (تُلغي نية الطلب)
        $directorySignals = [
            'دكتور', 'طبيب', 'حضانة', 'روضة', 'استشاري', 'أخصائي', 'عيادة',
            'باطنة', 'قلب', 'عيون', 'أسنان', 'أعصاب', 'نساء', 'توليد',
            'مونتيسوري', 'رقم', 'تواصل', 'اتصل', 'موعد', 'حجز',
            'أفضل', 'افضل', 'تقييم', 'معلومات', 'اسأل', 'سؤال',
        ];

        $hasOrderTrigger    = false;
        $hasDirectorySignal = false;
        $lowerMsg           = mb_strtolower($message);

        foreach ($orderTriggers as $t) {
            if (mb_stripos($lowerMsg, $t) !== false) { $hasOrderTrigger = true; break; }
        }
        foreach ($directorySignals as $s) {
            if (mb_stripos($lowerMsg, $s) !== false) { $hasDirectorySignal = true; break; }
        }

        // نية الطلب: فقط لو فيه trigger طلب بدون إشارة للدليل
        $isOrderIntent = $hasOrderTrigger && ! $hasDirectorySignal;

        // command مباشر من الـ UI
        if (str_starts_with($message, '__')) {
            $result = $flow->handle($message);
            $this->logChat($request, $message, $result['text'] ?? '');
            return response()->json($result);
        }

        // flow جارٍ (المستخدم في منتصف طلب)
        if ($flow->isActive()) {
            $result = $flow->handle($message);
            $this->logChat($request, $message, $result['text'] ?? '');
            return response()->json($result);
        }

        // بدء flow طلب جديد
        if ($isOrderIntent) {
            $flow->handle('__START_ORDER__');
            $result = $flow->handle($message);
            $this->logChat($request, $message, $result['text'] ?? '');
            return response()->json($result);
        }

        // محادثة عادية عبر Gemini (RAG)
        $history = $request->input('history', []);
        $aiResult = $this->ai->chat($message, $history);

        $this->logChat($request, $message, $aiResult['reply'] ?? '');

        return response()->json([
            'text'        => $aiResult['reply'],
            'action'      => 'chat',
            'data'        => ['products' => $aiResult['products'] ?? []],
            'quick'       => [],
        ]);
    }

    /** نقطة نهاية الدردشة (صفحة المساعد الكاملة) */
    public function chat(Request $request): JsonResponse
    {
        if (! config('ai.enabled', true)) {
            return response()->json(['reply' => 'المساعد غير مفعّل حالياً.', 'products' => []], 503);
        }

        $request->validate([
            'message'             => 'required|string|max:' . config('ai.max_msg_length', 500),
            'history'             => 'nullable|array|max:10',
            'history.*.role'      => 'in:user,assistant',
            'history.*.content'   => 'string|max:600',
        ]);

        $key = 'ai.rl.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, config('ai.rate_per_min', 8))) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'reply'    => "وصلت للحد الأقصى. انتظر {$seconds} ثانية ثم حاول مجددًا.",
                'products' => [],
            ], 429);
        }
        RateLimiter::hit($key, 60);

        $start   = hrtime(true);
        $message = $request->input('message');
        $history = $request->input('history', []);
        $result  = $this->ai->chat($message, $history);
        $ms      = (int) ((hrtime(true) - $start) / 1e6);

        $this->logChat($request, $message, $result['reply'] ?? '', $ms);

        return response()->json($result);
    }

    /** مقارنة منتجات بدون LLM */
    public function compare(Request $request): JsonResponse
    {
        $request->validate([
            'ids'   => 'required|array|min:2|max:4',
            'ids.*' => 'integer|min:1',
        ]);

        $products = $this->ai->compare($request->input('ids'));

        return response()->json(['products' => $products]);
    }

    // ─── مساعدات ─────────────────────────────────────────────────────────────

    private function widgetReply(string $text, string $action, array $data = [], array $quick = []): array
    {
        return compact('text', 'action', 'data', 'quick');
    }

    private function logChat(Request $request, string $query, string $reply, int $ms = 0): void
    {
        if (! config('ai.log_chats', true)) {
            return;
        }

        try {
            AssistantLog::create([
                'session_id'  => substr(session()->getId(), 0, 64),
                'ip'          => $request->ip(),
                'query'       => mb_substr($query, 0, 1000),
                'reply'       => mb_substr($reply, 0, 2000),
                'response_ms' => min($ms, 65535),
            ]);
        } catch (\Throwable) {
            // لا نوقف التطبيق لو فشل التسجيل
        }
    }

    private function sharedData(): array
    {
        return [
            'storeName'            => setting('store.name', 'متجر العلامات'),
            'themeCss'             => '',
            'stripText'            => setting('store.strip_text', 'شحن مجاني داخل القاهرة والجيزة · الدفع عند الاستلام'),
            'storeSupportWhatsapp' => setting('store.support_whatsapp', ''),
        ];
    }
}
