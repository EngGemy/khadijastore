<?php

namespace App\Filament\Resources\AuditResource\Pages;

use App\Filament\Resources\AuditResource;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;

class ViewAudit extends ViewRecord
{
    protected static string $resource = AuditResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Audit Info')->schema([
                TextEntry::make('user.name')->label('User')->placeholder('System'),
                TextEntry::make('event')->label('Event')->badge(),
                TextEntry::make('auditable_type')->label('Type')->formatStateUsing(fn ($s) => class_basename($s)),
                TextEntry::make('auditable_id')->label('ID'),
                TextEntry::make('created_at')->label('Time')->dateTime('Y-m-d H:i:s'),
                TextEntry::make('ip_address')->label('IP'),
            ])->columns(2),
            Section::make('Old Values')->schema([
                KeyValueEntry::make('old_values')->label('')->columnSpanFull(),
            ])->collapsible(),
            Section::make('New Values')->schema([
                KeyValueEntry::make('new_values')->label('')->columnSpanFull(),
            ])->collapsible(),
        ]);
    }
}
