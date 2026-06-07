<?php

return [

    /*
    |--------------------------------------------------------------------------
    | مزوّد الذكاء الاصطناعي الافتراضي
    |--------------------------------------------------------------------------
    | القيمة الافتراضية 'gemini'. لتغيير المزوّد: غيّر AI_PROVIDER في .env فقط.
    | مزوّدات متاحة: gemini | groq | ollama
    */
    'provider' => env('AI_PROVIDER', 'gemini'),

    /*
    |--------------------------------------------------------------------------
    | إعدادات Google Gemini
    |--------------------------------------------------------------------------
    */
    'gemini' => [
        'api_key'    => env('GEMINI_API_KEY', ''),
        'model'      => env('AI_MODEL', 'gemini-2.5-flash'),
        'endpoint'   => 'https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent',
        'timeout'    => (int) env('AI_TIMEOUT', 20),
        'max_tokens' => (int) env('AI_MAX_TOKENS', 1024),
        'temperature'=> (float) env('AI_TEMPERATURE', 0.4),
    ],

    /*
    |--------------------------------------------------------------------------
    | إعدادات Groq (stub — تفعيل لاحقاً)
    |--------------------------------------------------------------------------
    */
    'groq' => [
        'api_key'    => env('GROQ_API_KEY', ''),
        'model'      => env('GROQ_MODEL', 'llama3-70b-8192'),
        'timeout'    => 20,
        'max_tokens' => 1024,
        'temperature'=> 0.4,
    ],

    /*
    |--------------------------------------------------------------------------
    | إعدادات Ollama (stub — تفعيل لاحقاً)
    |--------------------------------------------------------------------------
    */
    'ollama' => [
        'host'       => env('OLLAMA_HOST', 'http://localhost:11434'),
        'model'      => env('OLLAMA_MODEL', 'llama3'),
        'timeout'    => 60,
        'max_tokens' => 1024,
        'temperature'=> 0.4,
    ],

    /*
    |--------------------------------------------------------------------------
    | حدود الاستخدام والكاش
    |--------------------------------------------------------------------------
    */
    'rate_per_min'    => (int) env('AI_RATE_PER_MIN', 8),
    'cache_ttl'       => (int) env('AI_CACHE_TTL', 600),   // ثوانٍ (10 دقائق)
    'max_msg_length'  => (int) env('AI_MAX_MSG_LENGTH', 500),
    'context_products'=> (int) env('AI_CONTEXT_PRODUCTS', 20),

    /*
    |--------------------------------------------------------------------------
    | إعدادات المساعد
    |--------------------------------------------------------------------------
    */
    'enabled'         => (bool) env('AI_ENABLED', true),
    'log_chats'       => (bool) env('AI_LOG_CHATS', true),
    'welcome_message' => env('AI_WELCOME', 'مرحباً! أنا مساعدك الذكي. أخبرني ماذا تبحث عنه وسأرشدك لأفضل المنتجات.'),
];
