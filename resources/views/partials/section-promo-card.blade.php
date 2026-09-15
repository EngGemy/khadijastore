@php
  $banner = $banner ?? null;
  $solo = $solo ?? false;
@endphp
@if($banner)
@php
  $img = category_banner_url($banner, (bool) $solo) ?: ($banner->banner_image ?? null);
  $headline = $banner->promo_headline ?: $banner->name;
  $cta = $banner->promoCtaText();
  $href = $banner->promoCtaUrl();
  $kicker = $banner->brand?->name ?? 'عرض القسم';
@endphp
<a href="{{ $href }}" class="section-promo-card {{ $solo ? 'section-promo-card--solo' : '' }}" role="listitem">
  <div class="section-promo-card__media" aria-hidden="true">
    @if($img)
      <img src="{{ $img }}" alt="" loading="lazy">
    @endif
    <span class="section-promo-card__shade"></span>
    @unless($img)
      <span class="section-promo-card__orb"></span>
    @endunless
  </div>
  <div class="section-promo-card__body">
    <span class="section-promo-card__kicker">{{ $kicker }}</span>
    <h3 class="section-promo-card__title">{{ $headline }}</h3>
    <span class="section-promo-card__cta">{{ $cta }} <span aria-hidden="true">←</span></span>
  </div>
</a>
@endif
