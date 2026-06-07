<?php

namespace App\Filament\Resources\HomeBlockResource\Pages;

use App\Filament\Resources\HomeBlockResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditHomeBlock extends EditRecord
{
    protected static string $resource = HomeBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()->after(fn () => Cache::forget('home.blocks.resolved'))];
    }

    protected function afterSave(): void
    {
        Cache::forget('home.blocks.resolved');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
