<?php

namespace App\Filament\Resources\ShippingRuleResource\Pages;

use App\Filament\Resources\ShippingRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShippingRules extends ListRecords
{
    protected static string $resource = ShippingRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
