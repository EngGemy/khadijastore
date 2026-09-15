{{-- Block: products_grid — رف متجر احترافي (متجر + منتجاته) mobile-first --}}
@php
  $blockProducts = $block->resolvedProducts ?? collect();
  $shelves = isset($storeShelves) && $storeShelves instanceof \Illuminate\Support\Collection && $storeShelves->isNotEmpty()
    ? $storeShelves
    : home_store_shelves($blockProducts, 10);
  $eyebrow = $block->subtitle ?? setting('home.products.eyebrow', 'تسوق حسب المتجر');
  $title = $block->title ?? setting('home.products.title', 'متاجر ومنتجات مختارة');
  $featuredId = featured_storefront_brand()?->id;
@endphp
<section id="products" class="home-section store-shelves-section max-w-[1180px] mx-auto px-4 sm:px-5">
  <div class="reveal flex items-end justify-between gap-3 mb-5 sm:mb-8">
    <div class="min-w-0">
      <span class="sec-eyebrow">{{ $eyebrow }}</span>
      <h2 class="font-extrabold tracking-tight text-ink mt-1.5" style="font-size:clamp(22px,3.2vw,32px)">{{ $title }}</h2>
      <p class="text-[12px] sm:text-[13px] text-ink/45 font-semibold mt-1 leading-relaxed">اسحب المنتجات · اضغط المتجر لزيارة الصفحة الكاملة</p>
    </div>
    <a href="{{ route('brands.index') }}" class="shrink-0 inline-flex items-center gap-1.5 min-h-[44px] px-3 rounded-full text-[13px] font-extrabold text-ink bg-paper2 border border-line hover:border-brand/40 hover:text-brand transition">
      كل المتاجر <span aria-hidden="true">←</span>
    </a>
  </div>

  @if($shelves->isNotEmpty())
  <div id="store-shelves" class="store-shelves" data-store-shelves>
    @foreach($shelves as $shelf)
    @php
      $brand = $shelf->brand;
      $isFeatured = $featuredId && (int) $brand->id === (int) $featuredId;
      $wa = preg_replace('/\D+/', '', (string) ($brand->whatsapp ?? ''));
    @endphp
    <article
      id="store-shelf-{{ $brand->id }}"
      class="store-shelf reveal {{ $isFeatured ? 'store-shelf--featured' : '' }}"
      data-brand-id="{{ $brand->id }}"
      aria-labelledby="store-shelf-title-{{ $brand->id }}"
    >
      <header class="store-shelf__head">
        <a href="{{ route('brand.show', $brand->slug) }}" class="store-shelf__brand">
          @include('partials.brand-avatar', ['brand' => $brand, 'size' => 'md', 'class' => 'store-shelf__avatar'])
          <div class="store-shelf__brand-text">
            <div class="store-shelf__name-row">
              <h3 id="store-shelf-title-{{ $brand->id }}" class="store-shelf__name">{{ $brand->name }}</h3>
              @if($isFeatured)
                <span class="store-shelf__pill">مميز</span>
              @endif
            </div>
            <p class="store-shelf__meta">
              <span>{{ $brand->products_count ?? $shelf->count }} منتج</span>
              @if($brand->category_label)
                <span class="store-shelf__dot" aria-hidden="true">·</span>
                <span class="store-shelf__cat">{{ \Illuminate\Support\Str::before($brand->category_label, '·') }}</span>
              @endif
            </p>
          </div>
        </a>

        <div class="store-shelf__actions">
          @if($wa)
          <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode('مرحباً، أريد الاستفسار عن منتجات '.$brand->name) }}"
             class="store-shelf__wa"
             target="_blank"
             rel="noopener"
             aria-label="واتساب {{ $brand->name }}">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm5.76 14.05c-.24.68-1.4 1.25-1.93 1.33-.49.07-1.12.1-1.81-.11-.42-.13-.95-.31-1.64-.61-2.89-1.25-4.77-4.16-4.92-4.35-.14-.19-1.18-1.57-1.18-3 0-1.42.74-2.12 1-2.41.26-.29.57-.36.76-.36h.55c.18 0 .42-.07.66.5.24.58.82 2 .89 2.15.07.15.12.32.02.52-.1.19-.14.32-.29.49-.14.17-.31.38-.44.51-.14.14-.29.29-.12.56.16.28.72 1.19 1.55 1.93 1.06.95 1.96 1.24 2.24 1.38.28.14.44.12.6-.07.17-.19.7-.82.89-1.1.19-.29.38-.24.64-.14.26.1 1.66.78 1.95.93.28.14.47.21.54.33.07.12.07.7-.17 1.38z"/></svg>
          </a>
          @endif
          <a href="{{ route('brand.show', $brand->slug) }}" class="store-shelf__cta">
            <span>زيارة</span>
            <span aria-hidden="true">←</span>
          </a>
        </div>
      </header>

      <div class="store-shelf__rail" data-shelf-rail>
        @foreach($shelf->products as $p)
        @php
          $discount = ($p->compare_price && $p->compare_price > $p->price)
            ? (int) round((1 - $p->price / $p->compare_price) * 100) : 0;
        @endphp
        <a href="{{ route('product.show', $p->slug) }}"
           class="shelf-card group"
           data-brand-id="{{ $p->brand_id }}"
           data-sales="{{ $p->sales_count ?? 0 }}"
           data-featured="{{ $p->is_featured ? '1' : '0' }}"
           data-has-deal="{{ $discount > 0 ? '1' : '0' }}"
           data-is-new="{{ $p->created_at && $p->created_at->gt(now()->subDays(30)) ? '1' : '0' }}">
          <div class="shelf-card__media">
            @if($p->badge)<span class="shelf-card__badge">{{ $p->badge }}</span>@endif
            @if($discount > 0)<span class="shelf-card__discount">-{{ $discount }}%</span>@endif
            @include('partials.product-cover', ['product' => $p])
            @if(method_exists($p, 'isOutOfStock') && $p->isOutOfStock())
              <span class="shelf-card__oos">نفد</span>
            @endif
          </div>
          <div class="shelf-card__body">
            <h4 class="shelf-card__title">{{ $p->name }}</h4>
            <div class="shelf-card__price-row">
              <span class="shelf-card__price">{{ number_format($p->price) }}<small>ج.م</small></span>
              @if($discount > 0)
              <span class="shelf-card__compare">{{ number_format($p->compare_price) }}</span>
              @endif
            </div>
          </div>
        </a>
        @endforeach

        <a href="{{ route('brand.show', $brand->slug) }}" class="shelf-more" aria-label="كل منتجات {{ $brand->name }}">
          <span class="shelf-more__icon" aria-hidden="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"/></svg>
          </span>
          <span class="shelf-more__label">كل المنتجات</span>
        </a>
      </div>
    </article>
    @endforeach
  </div>
  <p id="products-filter-empty" class="hidden text-center text-ink/45 text-sm font-semibold py-10 border border-dashed border-line rounded-2xl bg-paper2 mt-6">
    لا توجد منتجات لهذا المتجر حالياً
  </p>
  @else
  <div class="text-center py-14 border border-dashed border-line rounded-2xl bg-paper2">
    <p class="text-ink/50 font-semibold mb-2">لا توجد منتجات للعرض حالياً</p>
    <p class="text-ink/35 text-sm">فعّل منتجات من لوحة التحكم أو عيّنها كـ «مميّزة»</p>
  </div>
  @endif
</section>
