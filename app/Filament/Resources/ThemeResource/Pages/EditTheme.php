<?php
namespace App\Filament\Resources\ThemeResource\Pages;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\ThemeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditTheme extends EditRecord
{
    protected static string $resource = ThemeResource::class;

    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
