<?php

namespace App\Services\AI\Contracts;

interface AiProvider
{
    /**
     * أرسل محادثة وارجع رد الـ AI نصًا.
     *
     * @param  array  $messages  [['role'=>'user','content'=>'...'], ['role'=>'assistant','content'=>'...']]
     * @param  array  $opts      ['system'=>string, 'max_tokens'=>int, 'temperature'=>float]
     * @return string
     */
    public function chat(array $messages, array $opts = []): string;

    /**
     * هل هذا المزوّد مُعدَّ (مفتاح API موجود مثلاً)؟
     */
    public function isConfigured(): bool;
}
