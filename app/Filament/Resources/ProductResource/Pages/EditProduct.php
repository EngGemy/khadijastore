<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('variants')
                ->label('إدارة المتغيرات')
                ->icon('heroicon-o-table-cells')
                ->color('info')
                ->url(fn () => ProductResource::getUrl('variants', ['record' => $this->getRecord()])),
            DeleteAction::make(),
        ];
    }
}
