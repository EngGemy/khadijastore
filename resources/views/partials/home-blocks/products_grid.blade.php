{{-- Block: products_grid — bestsellers + optional side promos --}}
@php
  $blockProducts = $block->resolvedProducts ?? collect();
  $eyebrow       = $block->subtitle ?? setting('home.products.eyebrow', 'الأكثر مبيعاً');
  $title         = $block->title    ?? setting('home.products.title',   'منتجات يحبها عملاؤنا');
  $promoBrands   = ($navBrands ?? collect())->take(2);
@endphp
<section id="products" class="home-section max-w-[1180px] mx-auto px-4 sm:px-5">
  <div class="reveal flex items-end justify-between gap-5 mb-8">
    <div>
      <span class="inline-flex items-center gap-2 text-[11px] font-black tracking-[.16em] uppercase text-brand mb-2.5">
        <span class="w-1.5 h-1.5 rounded-full bg-brand"></span>{{ $eyebrow }}
      </span>
      <h2 class="font-extrabold tracking-tight text-ink" style="font-size:clamp(22px,3.2vw,32px)">{{ $title }}</h2>
    </div>
    <a href="{{ route('products.index') }}" class="text-sm font-bold text-ink inline-flex items-center gap-1.5 hover:gap-2.5 transition-all whitespace-nowrap hover:text-brand">عرض الكل <span>←</span></a>
  </div>

  @if($blockProducts->isNotEmpty())
  <div class="grid lg:grid-cols-[1fr_220px] gap-5 items-start">
    <div>
      <div id="products-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3.5 sm:gap-5">
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
              <span class="absolute top-3 end-3 bg-brand text-white text-[10px] font-bold px-2 py-0.5 rounded-full z-10">متبقي {{ $p->total_stock }}</span>
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
    </div>

    @if($promoBrands->isNotEmpty())
    <aside class="hidden lg:flex flex-col gap-4 sticky top-[96px]">
      @foreach($promoBrands as $i => $promo)
      <a href="{{ route('brand.show', $promo->slug) }}"
         class="relative overflow-hidden rounded-[18px] border border-line bg-ink text-paper p-5 min-h-[168px] flex flex-col justify-end group hover:-translate-y-1 transition-all duration-300 shadow-card"
         style="{{ $i === 1 ? 'background:linear-gradient(160deg,#E85D04,#c24a03)' : '' }}">
        <div class="absolute top-4 start-4 opacity-30">
          @include('partials.brand-avatar', ['brand' => $promo, 'size' => 'md', 'class' => 'ring-0'])
        </div>
        <span class="text-[10px] font-black tracking-[.14em] uppercase opacity-70 mb-1">{{ $i === 0 ? 'براند مميز' : 'عرض خاص' }}</span>
        <span class="font-extrabold text-[17px] leading-tight">{{ $promo->name }}</span>
        <span class="text-[12px] font-semibold opacity-70 mt-1">{{ $promo->category_label ?: 'تسوّق الآن' }}</span>
      </a>
      @endforeach
    </aside>
    @endif
  </div>
  @else
  <div class="text-center py-14 border border-dashed border-line rounded-2xl bg-paper2">
    <p class="text-ink/50 font-semibold mb-2">لا توجد منتجات للعرض حالياً</p>
    <p class="text-ink/35 text-sm">فعّل منتجات من لوحة التحكم أو عيّنها كـ «مميّزة»</p>
  </div>
  @endif
</section>
