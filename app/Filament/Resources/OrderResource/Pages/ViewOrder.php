<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderStatusService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected static ?string $title = '';

    protected Width|string|null $maxContentWidth = Width::Full;

    protected string $view = 'filament.resources.order-resource.pages.view-order';

    public ?string $statusNote = '';

    public ?string $newNote = '';

    public string $activePanel = 'timeline';

    protected function resolveRecord(int|string $key): Model
    {
        return Order::withoutGlobalScopes()
            ->with([
                'items.product.media',
                'statusHistories.changer',
                'brand',
                'handler',
                'staffNotes.author',
            ])
            ->findOrFail($key);
    }

    public function getTitle(): string|Htmlable
    {
        return 'طلب '.$this->record->order_no;
    }

    public function getSubheading(): string|Htmlable|null
    {
        $status = Order::STATUSES[$this->record->status] ?? $this->record->status;

        return $this->record->customer_name.' · '.$status;
    }

    protected function getHeaderActions(): array
    {
        $order = $this->record;
        $wa = $this->whatsappUrl($order);

        return [
            Action::make('whatsapp')
                ->label('واتساب')
                ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                ->color('success')
                ->url($wa)
                ->openUrlInNewTab(),
            Action::make('call')
                ->label('اتصال')
                ->icon('heroicon-o-phone')
                ->url('tel:'.$order->customer_phone)
                ->openUrlInNewTab(),
            Action::make('print')
                ->label('طباعة')
                ->icon('heroicon-o-printer')
                ->action(fn () => $this->js('window.print()')),
            EditAction::make()->label('تعديل'),
        ];
    }

    /** @return list<string> */
    public function getNextStatusesProperty(): array
    {
        return app(OrderStatusService::class)->nextStatuses($this->record);
    }

    public function updateStatus(string $status): void
    {
        try {
            app(OrderStatusService::class)->transition(
                $this->record,
                $status,
                filled($this->statusNote) ? trim($this->statusNote) : null,
                auth()->user(),
            );
        } catch (InvalidArgumentException) {
            Notification::make()
                ->title('تعذّر تحديث الحالة')
                ->body('لا يمكن الانتقال إلى هذه الحالة من الوضع الحالي.')
                ->danger()
                ->send();

            return;
        }

        $this->statusNote = '';
        $this->record->refresh()->load([
            'items.product.media',
            'statusHistories.changer',
            'brand',
            'handler',
            'staffNotes.author',
        ]);

        Notification::make()
            ->title('تم تحديث الحالة')
            ->body('الحالة الجديدة: '.(Order::STATUSES[$status] ?? $status))
            ->success()
            ->send();
    }

    public function addNote(): void
    {
        $this->validate([
            'newNote' => ['required', 'string', 'max:2000'],
        ], [
            'newNote.required' => 'اكتب الملاحظة أولاً.',
        ]);

        $this->record->staffNotes()->create([
            'user_id' => auth()->id(),
            'body' => trim($this->newNote),
        ]);

        $this->newNote = '';
        $this->record->load('staffNotes.author');
        $this->activePanel = 'notes';

        Notification::make()
            ->title('تم إرسال الملاحظة')
            ->success()
            ->send();
    }

    private function whatsappUrl(Order $order): string
    {
        $digits = preg_replace('/\D+/', '', (string) $order->customer_phone);
        if (str_starts_with($digits, '0')) {
            $phone = '2'.$digits;
        } elseif (str_starts_with($digits, '20')) {
            $phone = $digits;
        } else {
            $phone = '20'.ltrim($digits, '0');
        }

        $text = rawurlencode("مرحباً {$order->customer_name}، بخصوص طلبك رقم {$order->order_no}");

        return "https://wa.me/{$phone}?text={$text}";
    }
}
