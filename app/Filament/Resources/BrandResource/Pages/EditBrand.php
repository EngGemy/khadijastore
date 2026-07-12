<?php

namespace App\Filament\Resources\BrandResource\Pages;

use App\Filament\Resources\BrandResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBrand extends EditRecord
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewStore')
                ->label('عرض المتجر')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => brand_page_url($this->record->slug))
                ->openUrlInNewTab(),
            DeleteAction::make()->after(fn () => forget_home_blocks_cache()),
        ];
    }

    protected function afterSave(): void
    {
        forget_home_blocks_cache();
    }
}
