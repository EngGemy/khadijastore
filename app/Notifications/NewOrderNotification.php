<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /** بيانات قاعدة البيانات بصيغة Filament 4 */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => "طلب جديد رقم {$this->order->order_no}",
            'body' => "العميل: {$this->order->customer_name} · الإجمالي: ".number_format($this->order->total).' ج.م',
            'status' => 'success',
            'icon' => 'heroicon-o-shopping-bag',
            'actions' => [
                [
                    'name' => 'view',
                    'label' => 'عرض الطلب',
                    'url' => $notifiable instanceof User
                        ? $notifiable->panelOrderUrl($this->order->id)
                        : url('/merchant/orders/'.$this->order->id),
                    'isButton' => true,
                    'color' => 'primary',
                    'shouldOpenUrlInNewTab' => false,
                ],
            ],
        ];
    }

    /** بريد إلكتروني (يذهب للـ log في بيئة dev عبر MAIL_MAILER=log) */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("طلب جديد رقم {$this->order->order_no}")
            ->view('emails.new-order', [
                'order' => $this->order,
                'notifiable' => $notifiable,
            ]);
    }
}
