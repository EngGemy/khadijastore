<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function resolveRecord(int|string $key): Model
    {
        return \App\Models\Order::withoutGlobalScopes()
            ->with(['items', 'statusHistories.changer', 'brand'])
            ->findOrFail($key);
    }
}
