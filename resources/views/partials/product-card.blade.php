@props(['product', 'storeBrand' => null, 'compact' => false])

@php
  $storeName = $storeBrand?->name ?? $product->brand->name ?? '';
  $cover = $product->getFirstMediaUrl('cover', 'thumb');
@endphp

<a href="{{ route('product.show', $product->slug) }}"
   class="brand-product-card store-product-card group border border-line rounded-2xl overflow-hidden flex flex-col bg-paper hover:-translate-y-1 hover:shadow-lg2 transition-all duration-400"
   data-category-id="{{ $product->category_id }}"
   data-category-parent="{{ $product->category?->parent_id ?? '' }}"
   data-sales="{{ $product->sales_count ?? 0 }}"
   data-featured="{{ $product->is_featured ? '1' : '0' }}"
   data-has-deal="{{ ($product->compare_price && $product->compare_price > $product->price) ? '1' : '0' }}"
   data-is-new="{{ $product->created_at && $product->created_at->gt(now()->subDays(30)) ? '1' : '0' }}">
  <div class="aspect-square bg-gradient-to-br from-paper2 to-paper3 relative overflow-hidden grid place-items-center">
    @if($product->badge)
      <span class="absolute top-3 start-3 bg-ink text-paper text-[11px] font-bold px-2.5 py-1 rounded-full z-10">{{ $product->badge }}</span>
    @endif
    @if($cover)
      <img src="{{ $cover }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
    @else
      <span class="font-extrabold text-2xl text-ink/10 group-hover:scale-110 transition-transform duration-500">{{ $product->mark ?? mb_substr($product->name, 0, 1) }}</span>
    @endif
    @if($product->isOutOfStock())
      <span class="absolute inset-0 bg-paper/80 backdrop-blur-sm grid place-items-center z-20">
        <span class="bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full">نفد المخزون</span>
      </span>
    @elseif($product->isLowStock())
      <span class="absolute top-3 end-3 bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full z-10">متبقي {{ $product->total_stock }}</span>
    @endif
    <span class="absolute inset-x-0 bottom-0 bg-ink text-white text-center py-3 text-sm font-bold translate-y-full group-hover:translate-y-0 transition-transform duration-300">عرض المنتج ←</span>
  </div>
  <div class="p-3 sm:p-4 flex flex-col gap-1 flex-1">
    @if($storeName && !$compact)
      <span class="text-[11px] text-accentDark font-bold truncate">{{ $storeName }}</span>
    @endif
    <h3 class="font-bold {{ $compact ? 'text-[13px] leading-snug line-clamp-2' : 'text-[14px] sm:text-[15px] leading-snug line-clamp-2' }}">{{ $product->name }}</h3>
    @unless($compact)
    <span class="text-[12px] text-ink/52 font-semibold">★ {{ number_format($product->rating, 1) }} ({{ $product->sales_count }})</span>
    @endunless
    <div class="flex items-baseline gap-1 mt-auto pt-1.5 flex-wrap">
      <span class="font-extrabold {{ $compact ? 'text-[17px]' : 'text-[19px] sm:text-[21px]' }} tracking-tight">{{ number_format($product->price) }}</span>
      <span class="text-[11px] font-bold">ج.م</span>
      @if($product->compare_price && $product->compare_price > $product->price)
        <span class="text-[12px] text-ink/38 line-through">{{ number_format($product->compare_price) }}</span>
        @php $discount = round((1 - $product->price / $product->compare_price) * 100); @endphp
        @if($discount > 0)
          <span class="text-[10px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded">-{{ $discount }}%</span>
        @endif
      @endif
    </div>
  </div>
</a>
