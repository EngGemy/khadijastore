@props(['brand', 'compact' => false, 'productCount' => 0, 'brandStats' => null, 'showActions' => true])

@php
  $logo = brand_logo_url($brand);
  $stats = $brandStats ?? ['productCount' => $productCount, 'avgRating' => 0, 'totalSales' => 0];
  $count = $stats['productCount'] ?? $productCount;
  $rating = $stats['avgRating'] ?? 0;
@endphp

<section class="brand-hero {{ $compact ? 'brand-hero--compact' : '' }}">
  <div class="brand-mesh" aria-hidden="true">
    <div class="brand-blob w-[280px] h-[280px] -top-16 -start-16" style="background:#16a34a"></div>
    <div class="brand-blob brand-blob-2 w-[220px] h-[220px] bottom-0 end-0" style="background:#6366f1"></div>
  </div>
  <div class="brand-hero__grid" aria-hidden="true"></div>

  <div class="brand-hero__inner">
    <div class="brand-hero__profile">
      <div class="brand-hero__logo-wrap">
        <div class="brand-hero__logo-ring" aria-hidden="true"></div>
        <div class="brand-hero__logo">
          @if($logo)
            <img src="{{ $logo }}" alt="{{ $brand->name }}" loading="eager" decoding="async">
          @else
            <span class="brand-hero__mark">{{ $brand->mark }}</span>
          @endif
        </div>
      </div>

      <div class="brand-hero__info">
        @if($brand->category_label)
          <span class="brand-hero__cat">{{ $brand->category_label }}</span>
        @endif
        <h1 class="brand-hero__title">{{ $brand->name }}</h1>
        @unless($compact)
          @if(filled($brand->description))
            <p class="brand-hero__desc">{{ $brand->description }}</p>
          @endif
        @endunless
        <div class="brand-hero__stats">
          <span class="brand-stat-pill">{{ $count }} منتج</span>
          @if($rating > 0)<span class="brand-stat-pill">{{ number_format($rating, 1) }}★</span>@endif
        </div>
      </div>
    </div>

    @if($showActions && !$compact)
      <div class="brand-hero__actions">
        <a href="{{ route('brand.shop', $brand->slug) }}" class="brand-hero__btn brand-hero__btn--primary">
          تسوّق الآن
        </a>
        @if($brand->whatsapp)
          <a href="https://wa.me/{{ $brand->whatsapp }}" target="_blank" rel="noopener" class="brand-hero__btn brand-hero__btn--wa">
            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
            واتساب
          </a>
        @endif
      </div>
    @endif
  </div>
</section>
