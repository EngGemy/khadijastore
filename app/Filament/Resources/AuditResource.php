<?php

namespace App\Filament\Resources;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use App\Filament\Resources\AuditResource\Pages\ListAudits;
use App\Filament\Resources\AuditResource\Pages\ViewAudit;
use App\Filament\Resources\AuditResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use OwenIt\Auditing\Models\Audit;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'سجل التغييرات';

    protected static ?string $modelLabel = 'سجل';

    protected static ?string $pluralModelLabel = 'سجل التغييرات';

    protected static string | \UnitEnum | null $navigationGroup = 'الإدارة';

    protected static ?int $navigationSort = 20;

    // الـ audit log للسوبر أدمن فقط
    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('الوقت')
                    ->dateTime('Y-m-d H:i:s')->sortable(),
                TextColumn::make('user.name')->label('المستخدم')
                    ->placeholder('النظام/الواجهة'),
                TextColumn::make('event')->label('الحدث')->badge()
                    ->formatStateUsing(fn ($s) => match ($s) {
                        'created' => 'إنشاء', 'updated' => 'تعديل', 'deleted' => 'حذف', default => $s,
                    })
                    ->color(fn ($s) => match ($s) {
                        'created' => 'success', 'updated' => 'warning', 'deleted' => 'danger', default => 'gray',
                    }),
                TextColumn::make('auditable_type')->label('النوع')
                    ->formatStateUsing(fn ($s) => class_basename($s)),
                TextColumn::make('auditable_id')->label('المعرّف'),
                TextColumn::make('ip_address')->label('IP')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('event')->label('الحدث')
                    ->options(['created' => 'إنشاء', 'updated' => 'تعديل', 'deleted' => 'حذف']),
            ])
            ->recordActions([ViewAction::make()])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAudits::route('/'),
            'view' => ViewAudit::route('/{record}'),
        ];
    }
}

