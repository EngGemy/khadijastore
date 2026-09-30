<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected ?int $submittedPrice = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('variants')
                ->label('إدارة المتغيرات')
                ->icon('heroicon-o-table-cells')
                ->color('info')
                ->url(fn () => ProductResource::getUrl('variants', ['record' => $this->getRecord()])),
            Action::make('syncPriceToPackage')
                ->label('مزامنة السعر مع الباقة')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('مزامنة السعر؟')
                ->modalDescription('سيتم تحديث سعر الباقة الأساسية ليطابق حقل التسعير ('.number_format((float) $this->getRecord()->price).' ج.م).')
                ->action(function (): void {
                    $product = $this->getRecord()->fresh();
                    $synced = $product->syncPrimaryVariantPrice((int) $product->price);
                    forget_home_blocks_cache();

                    Notification::make()
                        ->title($synced ? 'تمت مزامنة سعر الباقة الأساسية' : 'الباقة الأساسية مطابقة بالفعل')
                        ->success()
                        ->send();
                }),
            DeleteAction::make()->after(fn () => forget_home_blocks_cache()),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->submittedPrice = isset($data['price']) ? (int) $data['price'] : null;

        return $data;
    }

    protected function afterSave(): void
    {
        // Filament يحفظ الباقات بعد المنتج ويعيد السعر القديم من الفورم —
        // نعيد تطبيق سعر التسعير على الباقة الأساسية بعد انتهاء كل الحفظ.
        $product = $this->getRecord()->fresh(['variants']);
        $price = $this->submittedPrice ?? (int) $product->price;
        $product->syncPrimaryVariantPrice($price);

        if ((int) $product->price !== $price) {
            $product->forceFill(['price' => $price])->saveQuietly();
        }

        forget_home_blocks_cache();
        $this->submittedPrice = null;
        $this->fillForm();
    }
}
