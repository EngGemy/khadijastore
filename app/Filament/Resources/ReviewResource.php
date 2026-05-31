<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\ReviewResource\Pages\ListReviews;
use App\Filament\Resources\ReviewResource\Pages\EditReview;
use App\Models\Review;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'المراجعات';

    protected static ?string $modelLabel = 'مراجعة';

    protected static ?string $pluralModelLabel = 'المراجعات';

    protected static string | \UnitEnum | null $navigationGroup = 'الإدارة';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('customer_name')->label('اسم العميل')->required(),
            TextInput::make('rating')->label('التقييم')->numeric()->minValue(1)->maxValue(5)->required(),
            Textarea::make('comment')->label('التعليق')->columnSpanFull(),
            Toggle::make('is_approved')->label('معتمد')->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->label('المنتج')->searchable()->weight('bold'),
                TextColumn::make('customer_name')->label('العميل')->searchable(),
                TextColumn::make('rating')->label('التقييم')
                    ->formatStateUsing(fn ($state) => str_repeat('★', $state) . str_repeat('☆', 5 - $state)),
                TextColumn::make('comment')->label('التعليق')->limit(50),
                ToggleColumn::make('is_approved')->label('معتمد')
                    ->afterStateUpdated(fn ($record) => $record->product?->recomputeRating()),
                TextColumn::make('brand.name')->label('البراند')
                    ->badge()
                    ->visible(fn () => auth()->user()->isSuperAdmin()),
                TextColumn::make('created_at')->label('التاريخ')
                    ->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_approved')->label('معتمد'),
                SelectFilter::make('brand_id')->label('البراند')
                    ->relationship('brand', 'name')
                    ->visible(fn () => auth()->user()->isSuperAdmin()),
                SelectFilter::make('rating')->label('التقييم')
                    ->options([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}
