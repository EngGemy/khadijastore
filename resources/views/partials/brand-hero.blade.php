@props(['brand', 'compact' => false, 'productCount' => 0, 'brandStats' => null, 'showActions' => true])

@php
  $logo = brand_logo_url($brand);
  $stats = $brandStats ?? ['productCount' => $productCount, 'avgRating' => 0, 'totalSales' => 0];
  $count = $stats['productCount'] ?? $productCount;
  $rating = $stats['avgRating'] ?? 0;
@endphp

<section class="brand-hero {{ $compact ? 'brand-hero--compact' : '' }}" aria-label="{{ $brand->name }}">
  <div class="brand-hero__bg" aria-hidden="true">
    <div class="brand-hero__orb brand-hero__orb--1"></div>
    <div class="brand-hero__orb brand-hero__orb--2"></div>
    <div class="brand-hero__orb brand-hero__orb--3"></div>
    <div class="brand-hero__shine"></div>
    <div class="brand-hero__grid"></div>
  </div>

  <div class="brand-hero__inner">
    <div class="brand-hero__glass brand-hero-enter">
      <div class="brand-hero__profile">
        <div class="brand-hero__logo-stage brand-hero-enter brand-hero-enter--2">
          <div class="brand-hero__logo-glow"></div>
          <div class="brand-hero__logo-frame">
            @if($logo)
              <img src="{{ $logo }}" alt="{{ $brand->name }}" class="brand-hero__logo-img" loading="eager" decoding="async"
                   onerror="this.hidden=true;this.nextElementSibling.hidden=false">
              <span class="brand-hero__mark" hidden>{{ $brand->mark }}</span>
            @else
              <span class="brand-hero__mark">{{ $brand->mark }}</span>
            @endif
          </div>
        </div>

        <div class="brand-hero__info brand-hero-enter brand-hero-enter--3">
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
        <div class="brand-hero__actions brand-hero-enter brand-hero-enter--4">
          <a href="{{ route('brand.shop', $brand->slug) }}" class="brand-hero__btn brand-hero__btn--primary">
            <span>تسوّق الآن</span>
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </a>
          @if($brand->whatsapp)
            <a href="https://wa.me/{{ $brand->whatsapp }}" target="_blank" rel="noopener" class="brand-hero__btn brand-hero__btn--wa">
              <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
              <span>واتساب</span>
            </a>
          @endif
        </div>
      @endif
    </div>
  </div>
</section>
