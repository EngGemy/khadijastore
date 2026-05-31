<?php

namespace App\Filament\Widgets;

use App\Models\ShippingRule;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActiveShippingRulesWidget extends BaseWidget
{
    protected static ?string $heading = 'قواعد الشحن الفعّالة الآن';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 9;

    public function table(Table $table): Table
    {
        $query = ShippingRule::currentlyActive();

        if (! auth()->user()?->isSuperAdmin()) {
            $query->where(function ($q) {
                $q->whereNull('brand_id')
                  ->orWhere('brand_id', auth()->user()?->brand_id);
            });
        }

        return $table
            ->query($query->latest('priority')->limit(5))
            ->columns([
                TextColumn::make('name')->label('القاعدة')->weight('bold'),
                TextColumn::make('type')->label('النوع')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'free' => 'مجاني',
                        'flat' => 'ثابت',
                        'percent_off' => 'خصم %',
                        'amount_off' => 'خصم مبلغ',
                        default => $state,
                    })->badge(),
                TextColumn::make('scope')->label('النطاق')
                    ->formatStateUsing(fn ($state) => $state === 'all' ? 'كل المحافظات' : 'محدد'),
                TextColumn::make('priority')->label('الأولوية'),
                TextColumn::make('brand.name')->label('البراند')
                    ->badge()
                    ->visible(fn () => auth()->user()?->isSuperAdmin()),
                TextColumn::make('ends_at')->label('ينتهي')
                    ->dateTime('Y-m-d H:i')
                    ->formatStateUsing(fn ($state) => $state ? $state->format('Y-m-d H:i') : '—'),
            ])
            ->paginated(false)
            ->emptyStateHeading('لا توجد قواعد شحن فعّالة حاليًا');
    }
}
