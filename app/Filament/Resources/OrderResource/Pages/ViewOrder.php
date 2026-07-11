<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderStatusService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected string $view = 'filament.resources.order-resource.pages.view-order';

    public ?string $statusNote = '';

    public ?string $newNote = '';

    protected function resolveRecord(int|string $key): Model
    {
        return Order::withoutGlobalScopes()
            ->with(['items', 'statusHistories.changer', 'brand', 'handler', 'staffNotes.author'])
            ->findOrFail($key);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('تعديل البيانات'),
            Action::make('print')
                ->label('طباعة')
                ->icon('heroicon-o-printer')
                ->action(fn () => $this->js('window.print()')),
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
        } catch (InvalidArgumentException $e) {
            Notification::make()
                ->title('تعذّر تحديث الحالة')
                ->body('لا يمكن الانتقال إلى هذه الحالة من الوضع الحالي.')
                ->danger()
                ->send();

            return;
        }

        $this->statusNote = '';
        $this->record->refresh()->load(['items', 'statusHistories.changer', 'brand', 'handler', 'staffNotes.author']);

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

        Notification::make()
            ->title('تم إرسال الملاحظة')
            ->success()
            ->send();
    }
}
