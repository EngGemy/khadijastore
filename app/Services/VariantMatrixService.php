<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class VariantMatrixService
{
    /**
     * @return Collection<int, Attribute>
     */
    public function attributesForProduct(Product $product): Collection
    {
        return Attribute::query()
            ->with('values')
            ->where(function ($query) use ($product) {
                $query->whereNull('brand_id')
                    ->orWhere('brand_id', $product->brand_id);
            })
            ->orderBy('sort')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array<int, int>
     */
    public function detectAttributeIds(Product $product): array
    {
        $ids = [];

        foreach ($product->variants as $variant) {
            foreach ($variant->option_values ?? [] as $option) {
                if (! empty($option['attribute_id'])) {
                    $ids[(int) $option['attribute_id']] = (int) $option['attribute_id'];
                }
            }
        }

        return array_values($ids);
    }

    /**
     * @param  array<int, int>  $attributeIds
     * @return array<int, array<string, mixed>>
     */
    public function buildMatrix(Product $product, array $attributeIds): array
    {
        $attributes = $this->attributesForProduct($product)
            ->whereIn('id', $attributeIds)
            ->values();

        if ($attributes->isEmpty()) {
            return [];
        }

        $existing = $product->variants->keyBy(
            fn (ProductVariant $variant) => $this->optionSignature($variant->option_values ?? [])
        );

        $combinations = $this->cartesianCombinations($attributes);
        $rows = [];

        foreach ($combinations as $optionValues) {
            $signature = $this->optionSignature($optionValues);
            $variant = $existing->get($signature);
            $labels = collect($optionValues)->pluck('value_label')->filter()->values();

            $rows[] = [
                'key' => $signature,
                'option_values' => $optionValues,
                'variant_id' => $variant?->id,
                'name' => $variant?->name ?: $labels->implode(' / '),
                'price' => $variant?->price ?? $product->price,
                'stock' => $variant?->stock ?? 0,
                'sku' => $variant?->sku ?? '',
                'track_stock' => $variant?->track_stock ?? true,
                'low_stock_threshold' => $variant?->low_stock_threshold ?? $product->low_stock_threshold ?? 5,
                'is_popular' => $variant?->is_popular ?? false,
            ];
        }

        return $rows;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function syncMatrix(Product $product, array $rows): void
    {
        $keptIds = [];

        foreach ($rows as $index => $row) {
            $optionValues = collect($row['option_values'] ?? [])
                ->map(fn (array $option) => [
                    'attribute_id' => (int) $option['attribute_id'],
                    'value_id' => (int) $option['value_id'],
                ])
                ->values()
                ->all();

            $payload = [
                'name' => $row['name'] ?: $this->labelFromOptions($row['option_values'] ?? []),
                'price' => (int) ($row['price'] ?? $product->price),
                'stock' => (int) ($row['stock'] ?? 0),
                'sku' => $row['sku'] ?: null,
                'track_stock' => (bool) ($row['track_stock'] ?? true),
                'low_stock_threshold' => (int) ($row['low_stock_threshold'] ?? 5),
                'is_popular' => (bool) ($row['is_popular'] ?? false),
                'option_values' => $optionValues,
                'sort' => $index,
            ];

            if (! empty($row['variant_id'])) {
                $variant = $product->variants()->whereKey($row['variant_id'])->first();
                if ($variant) {
                    $variant->update($payload);
                    $keptIds[] = $variant->id;

                    continue;
                }
            }

            $variant = $product->variants()->create($payload);
            $keptIds[] = $variant->id;
        }

        $product->variants()->whereNotIn('id', $keptIds)->delete();
    }

    /**
     * @param  array<int, array<string, mixed>>  $optionValues
     */
    public function optionSignature(array $optionValues): string
    {
        return collect($optionValues)
            ->filter(fn ($option) => ! empty($option['attribute_id']) && ! empty($option['value_id']))
            ->sortBy('attribute_id')
            ->map(fn ($option) => ((int) $option['attribute_id']).':'.((int) $option['value_id']))
            ->implode('|');
    }

    /**
     * @param  Collection<int, Attribute>  $attributes
     * @return array<int, array<int, array<string, mixed>>>
     */
    protected function cartesianCombinations(Collection $attributes): array
    {
        $result = [[]];

        foreach ($attributes as $attribute) {
            $next = [];

            foreach ($result as $combination) {
                foreach ($attribute->values as $value) {
                    $next[] = array_merge($combination, [[
                        'attribute_id' => $attribute->id,
                        'attribute_name' => $attribute->name,
                        'value_id' => $value->id,
                        'value_label' => $value->label,
                    ]]);
                }
            }

            $result = $next;
        }

        return $result;
    }

    /**
     * @param  array<int, array<string, mixed>>  $optionValues
     */
    protected function labelFromOptions(array $optionValues): string
    {
        return collect($optionValues)
            ->pluck('value_label')
            ->filter()
            ->implode(' / ');
    }
}
