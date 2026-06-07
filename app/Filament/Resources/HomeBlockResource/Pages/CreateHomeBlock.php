<?php

namespace App\Filament\Resources\HomeBlockResource\Pages;

use App\Filament\Resources\HomeBlockResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;

class CreateHomeBlock extends CreateRecord
{
    protected static string $resource = HomeBlockResource::class;

    protected function afterCreate(): void
    {
        Cache::forget('home.blocks.resolved');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
