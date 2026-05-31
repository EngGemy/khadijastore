<?php

namespace App\Filament\Widgets;

use App\Models\Review;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentReviewsTable extends BaseWidget
{
    protected static ?string $heading = 'المراجعات الأخيرة';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $isSuper = $user->isSuperAdmin();

        $base = $isSuper
            ? Review::query()->withoutGlobalScopes()
            : Review::query();

        return $table
            ->query(
                (clone $base)
                    ->with(['product', 'brand'])
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                TextColumn::make('product.name')
                    ->label('المنتج')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('brand.name')
                    ->label('البراند')
                    ->badge()
                    ->visible(fn () => $isSuper),

                TextColumn::make('customer_name')
                    ->label('العميل')
                    ->searchable(),

                TextColumn::make('rating')
                    ->label('التقييم')
                    ->formatStateUsing(fn ($state) => str_repeat('★', $state).str_repeat('☆', 5 - $state)),

                TextColumn::make('comment')
                    ->label('التعليق')
                    ->limit(40),

                IconColumn::make('is_approved')
                    ->label('معتمد')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->paginated(false)
            ->emptyStateHeading('لا توجد مراجعات حاليًا');
    }
}
