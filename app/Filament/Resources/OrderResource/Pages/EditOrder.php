<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    // عند تأكيد الطلب، سجّل من تعامل معه ووقت التأكيد
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['status'] ?? null) === 'confirmed' && empty($this->record->confirmed_at)) {
            $data['confirmed_at'] = now();
            $data['handled_by'] = auth()->id();
        }

        return $data;
    }
}
