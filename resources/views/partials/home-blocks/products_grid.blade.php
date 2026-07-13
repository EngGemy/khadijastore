{{-- Block: products_grid — premium product cards --}}
@php
  $blockProducts = $block->resolvedProducts ?? collect();
  $eyebrow       = $block->subtitle ?? setting('home.products.eyebrow', 'الأكثر طلبًا · BESTSELLERS');
  $title         = $block->title    ?? setting('home.products.title',   'منتجات يحبها عملاؤنا');
@endphp
<section id="products" class="home-section max-w-[1180px] mx-auto px-4 sm:px-5">
  <div class="reveal flex items-end justify-between gap-5 mb-8">
    <div>
      <span class="text-xs font-bold tracking-[.14em] uppercase text-accentDark block mb-2.5">{{ $eyebrow }}</span>
      <h2 class="font-extrabold tracking-tight" style="font-size:clamp(24px,3.5vw,36px)">{{ $title }}</h2>
    </div>
    @if($blockProducts->isNotEmpty())
    <a href="{{ route('home') }}#products" class="text-sm font-bold text-accentDark inline-flex items-center gap-1.5 hover:gap-2.5 transition-all whitespace-nowrap">عرض الكل <span>←</span></a>
    @endif
  </div>

  @if($blockProducts->isNotEmpty())
  <div id="products-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
    @foreach($blockProducts as $p)
    @php
      $discount = ($p->compare_price && $p->compare_price > $p->price)
        ? round((1 - $p->price / $p->compare_price) * 100) : 0;
    @endphp
    <a href="{{ route('product.show', $p->slug) }}"
       class="product-card group flex flex-col"
       data-brand-id="{{ $p->brand_id }}"
       data-sales="{{ $p->sales_count ?? 0 }}"
       data-featured="{{ $p->is_featured ? '1' : '0' }}"
       data-has-deal="{{ ($p->compare_price && $p->compare_price > $p->price) ? '1' : '0' }}"
       data-is-new="{{ $p->created_at && $p->created_at->gt(now()->subDays(30)) ? '1' : '0' }}">
      <div class="product-card__media">
        @if($p->badge)<span class="product-card__badge">{{ $p->badge }}</span>@endif
        @if($discount > 0)<span class="product-card__discount">-{{ $discount }}%</span>@endif
        @include('partials.product-cover', ['product' => $p])
        @if($p->isOutOfStock())
          <span class="absolute inset-0 bg-paper/85 backdrop-blur-sm grid place-items-center z-20">
            <span class="bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full">نفد المخزون</span>
          </span>
        @elseif($p->isLowStock())
          <span class="absolute top-3 end-3 bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full z-10">متبقي {{ $p->total_stock }}</span>
        @endif
        <div class="product-card__overlay">
          <span class="product-card__cta">عرض المنتج ←</span>
        </div>
      </div>
      <div class="product-card__body">
        @if($p->brand)
        <div class="product-card__brand">
          @include('partials.brand-avatar', ['brand' => $p->brand, 'size' => 'xs'])
          <span class="truncate">{{ $p->brand->name }}</span>
        </div>
        @endif
        <h3 class="product-card__title">{{ $p->name }}</h3>
        <div class="product-card__meta">★ {{ number_format($p->rating, 1) }} · {{ number_format($p->sales_count) }} مبيعة</div>
        <div class="product-card__price-row">
          <span class="product-card__price">{{ number_format($p->price) }}</span>
          <span class="text-[11px] font-bold text-ink/50">ج.م</span>
          @if($p->compare_price && $p->compare_price > $p->price)
          <span class="product-card__compare">{{ number_format($p->compare_price) }}</span>
          @endif
        </div>
      </div>
    </a>
    @endforeach
  </div>
  <p id="products-filter-empty" class="hidden text-center text-ink/45 text-sm font-semibold py-10 border border-dashed border-line rounded-2xl bg-paper2">
    لا توجد منتجات لهذا المتجر حالياً
  </p>
  @else
  <div class="text-center py-14 border border-dashed border-line rounded-2xl bg-paper2">
    <p class="text-ink/50 font-semibold mb-2">لا توجد منتجات للعرض حالياً</p>
    <p class="text-ink/35 text-sm">فعّل منتجات من لوحة التحكم أو عيّنها كـ «مميّزة»</p>
  </div>
  @endif
</section>
