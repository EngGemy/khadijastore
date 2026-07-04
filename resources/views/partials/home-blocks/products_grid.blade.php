{{-- Block: products_grid — product cards grid --}}
@php
  $blockProducts = $block->resolvedProducts ?? collect();
  $eyebrow       = $block->subtitle ?? setting('home.products.eyebrow', 'الأكثر طلبًا · BESTSELLERS');
  $title         = $block->title    ?? setting('home.products.title',   'منتجات يحبها عملاؤنا');
@endphp
@if($blockProducts->isNotEmpty())
<section id="products" class="max-w-[1180px] mx-auto px-5 py-18" style="padding-top:72px;padding-bottom:72px">
  <div class="reveal flex items-end justify-between gap-5 mb-9">
    <div>
      <span class="text-xs font-bold tracking-[.14em] uppercase text-accentDark block mb-2.5">{{ $eyebrow }}</span>
      <h2 class="font-extrabold tracking-tight" style="font-size:clamp(24px,3.5vw,36px)">{{ $title }}</h2>
    </div>
    <a href="#" class="text-sm font-bold inline-flex items-center gap-1.5 hover:gap-2.5 transition-all whitespace-nowrap">عرض الكل <span>←</span></a>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 stagger">
    @foreach($blockProducts as $p)
    <a href="{{ route('product.show', $p->slug) }}"
       class="product-card group border border-line rounded-[18px] overflow-hidden flex flex-col bg-paper hover:-translate-y-1.5 hover:shadow-lg2 transition-all duration-500"
       data-brand-id="{{ $p->brand_id }}"
       data-sales="{{ $p->sales_count ?? 0 }}"
       data-featured="{{ $p->is_featured ? '1' : '0' }}"
       data-has-deal="{{ ($p->compare_price && $p->compare_price > $p->price) ? '1' : '0' }}"
       data-is-new="{{ $p->created_at && $p->created_at->gt(now()->subDays(30)) ? '1' : '0' }}">
      <div class="aspect-square bg-gradient-to-br from-paper2 to-paper3 relative overflow-hidden grid place-items-center">
        @if($p->badge)<span class="absolute top-3 start-3 bg-ink text-paper text-[11px] font-bold px-2.5 py-1 rounded-full z-10">{{ $p->badge }}</span>@endif
        @php $cover = $p->getFirstMediaUrl('cover', 'thumb'); @endphp
        @if($cover)
          <img src="{{ $cover }}" alt="{{ $p->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
        @else
          <span class="font-extrabold text-2xl text-ink/10 group-hover:scale-110 transition-transform duration-500">{{ $p->mark ?? mb_substr($p->name, 0, 1) }}</span>
        @endif
        @if($p->isOutOfStock())
          <span class="absolute inset-0 bg-paper/80 backdrop-blur-sm grid place-items-center z-20">
            <span class="bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full">نفد المخزون</span>
          </span>
        @elseif($p->isLowStock())
          <span class="absolute top-3 end-3 bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full z-10">متبقي {{ $p->total_stock }}</span>
        @endif
        <span class="absolute inset-x-0 bottom-0 bg-ink text-white text-center py-3 text-sm font-bold translate-y-full group-hover:translate-y-0 transition-transform duration-300">عرض المنتج ←</span>
      </div>
      <div class="p-4 flex flex-col gap-1.5 flex-1">
        <span class="text-[11px] text-accentDark font-bold">{{ $p->brand->name ?? '' }}</span>
        <h3 class="font-bold text-[15px] leading-snug">{{ $p->name }}</h3>
        <span class="text-[12.5px] text-ink/52 font-semibold">★ {{ number_format($p->rating, 1) }} ({{ $p->sales_count }})</span>
        <div class="flex items-baseline gap-1.5 mt-auto pt-2">
          <span class="font-extrabold text-[21px] tracking-tight">{{ number_format($p->price) }}</span>
          <span class="text-[13px] font-bold">ج.م</span>
          @if($p->compare_price)<span class="text-[13px] text-ink/38 line-through ms-0.5">{{ number_format($p->compare_price) }}</span>@endif
        </div>
      </div>
    </a>
    @endforeach
  </div>
</section>
@endif
