@props(['product', 'storeBrand' => null, 'compact' => false])

@php
  $storeName = $storeBrand?->name ?? $product->brand->name ?? '';
  $brand = $storeBrand ?? $product->brand;
  $discount = ($product->compare_price && $product->compare_price > $product->price)
    ? round((1 - $product->price / $product->compare_price) * 100) : 0;
@endphp

<a href="{{ route('product.show', $product->slug) }}"
   class="product-card brand-product-card store-product-card group flex flex-col"
   data-category-id="{{ $product->category_id }}"
   data-category-parent="{{ $product->category?->parent_id ?? '' }}"
   data-sales="{{ $product->sales_count ?? 0 }}"
   data-featured="{{ $product->is_featured ? '1' : '0' }}"
   data-has-deal="{{ ($product->compare_price && $product->compare_price > $product->price) ? '1' : '0' }}"
   data-is-new="{{ $product->created_at && $product->created_at->gt(now()->subDays(30)) ? '1' : '0' }}">
  <div class="product-card__media">
    @if($product->badge)
      <span class="product-card__badge">{{ $product->badge }}</span>
    @endif
    @if($discount > 0)
      <span class="product-card__discount">-{{ $discount }}%</span>
    @endif
    @include('partials.product-cover', ['product' => $product])
    @if($product->isOutOfStock())
      <span class="absolute inset-0 bg-paper/85 backdrop-blur-sm grid place-items-center z-20">
        <span class="bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full">نفد المخزون</span>
      </span>
    @elseif($product->isLowStock())
      <span class="absolute top-3 end-3 bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full z-10">متبقي {{ $product->total_stock }}</span>
    @endif
    <div class="product-card__overlay">
      <span class="product-card__cta">عرض المنتج ←</span>
    </div>
  </div>
  <div class="product-card__body {{ $compact ? '!p-3' : '' }}">
    @if($storeName && ! $compact && $brand)
      <div class="product-card__brand">
        @include('partials.brand-avatar', ['brand' => $brand, 'size' => 'xs'])
        <span class="truncate">{{ $storeName }}</span>
      </div>
    @endif
    <h3 class="product-card__title {{ $compact ? '!text-[13px]' : '' }}">{{ $product->name }}</h3>
    @unless($compact)
    <div class="product-card__meta">★ {{ number_format($product->rating, 1) }} · {{ number_format($product->sales_count) }} مبيعة</div>
    @endunless
    <div class="product-card__price-row">
      <span class="product-card__price {{ $compact ? '!text-[17px]' : '' }}">{{ number_format($product->price) }}</span>
      <span class="text-[11px] font-bold text-ink/50">ج.م</span>
      @if($product->compare_price && $product->compare_price > $product->price)
        <span class="product-card__compare">{{ number_format($product->compare_price) }}</span>
      @endif
    </div>
  </div>
</a>
