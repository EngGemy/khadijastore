<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $productName,
        public readonly int $stock
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'تحذير: مخزون منخفض',
            'body' => "المنتج: {$this->productName} · المتاح: {$this->stock}",
            'status' => 'warning',
            'icon' => 'heroicon-o-exclamation-triangle',
        ];
    }
}
