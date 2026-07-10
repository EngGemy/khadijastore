<?php

namespace App\Filament\Resources\HomeBlockResource\Pages;

use App\Filament\Resources\HomeBlockResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHomeBlock extends CreateRecord
{
    protected static string $resource = HomeBlockResource::class;

    protected function afterCreate(): void
    {
        forget_home_blocks_cache();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
