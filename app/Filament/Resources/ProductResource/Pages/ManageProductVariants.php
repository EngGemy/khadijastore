<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use App\Services\VariantMatrixService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ManageProductVariants extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ProductResource::class;

    protected static ?string $title = 'إدارة المتغيرات';

    protected static ?string $navigationLabel = 'المتغيرات';

    protected string $view = 'filament.resources.product-resource.pages.manage-variants';

    /** @var array<int, int> */
    public array $selectedAttributeIds = [];

    /** @var array<int, array<string, mixed>> */
    public array $matrixRows = [];

    public ?string $bulkPrice = null;

    public ?string $bulkStock = null;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->loadMatrix();
    }

    public function getTitle(): string
    {
        return 'إدارة المتغيرات — '.$this->getRecord()->name;
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            ProductResource::getUrl('index') => 'المنتجات',
            ProductResource::getUrl('edit', ['record' => $this->getRecord()]) => $this->getRecord()->name,
            '' => 'المتغيرات',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('رجوع للمنتج')
                ->icon('heroicon-o-arrow-right')
                ->url(fn () => ProductResource::getUrl('edit', ['record' => $this->getRecord()])),
        ];
    }

    public function loadMatrix(): void
    {
        /** @var Product $product */
        $product = $this->getRecord()->loadMissing(['variants', 'brand']);
        $service = app(VariantMatrixService::class);

        if ($this->selectedAttributeIds === []) {
            $this->selectedAttributeIds = $service->detectAttributeIds($product);
        }

        $this->matrixRows = $service->buildMatrix($product, $this->selectedAttributeIds);
    }

    public function regenerateMatrix(): void
    {
        $this->loadMatrix();

        Notification::make()
            ->title('تم تحديث جدول التوليفات')
            ->success()
            ->send();
    }

    public function updatedSelectedAttributeIds(): void
    {
        $this->loadMatrix();
    }

    public function applyBulkPrice(): void
    {
        if ($this->bulkPrice === null || $this->bulkPrice === '') {
            return;
        }

        foreach ($this->matrixRows as $index => $row) {
            $this->matrixRows[$index]['price'] = (int) $this->bulkPrice;
        }
    }

    public function applyBulkStock(): void
    {
        if ($this->bulkStock === null || $this->bulkStock === '') {
            return;
        }

        foreach ($this->matrixRows as $index => $row) {
            $this->matrixRows[$index]['stock'] = (int) $this->bulkStock;
        }
    }

    public function save(): void
    {
        /** @var Product $product */
        $product = $this->getRecord();
        app(VariantMatrixService::class)->syncMatrix($product, $this->matrixRows);

        $this->record = $product->fresh(['variants', 'brand']);
        $this->loadMatrix();

        Notification::make()
            ->title('تم حفظ المتغيرات')
            ->success()
            ->send();
    }

    public function getCatalogAttributesProperty()
    {
        return app(VariantMatrixService::class)->attributesForProduct($this->getRecord());
    }
}
