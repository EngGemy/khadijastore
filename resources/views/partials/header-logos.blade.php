@php
  $headerBrand = $headerBrand ?? null;
  $hasBrandLogo = $headerBrand instanceof \App\Models\Brand;
  $brandLogo = $hasBrandLogo ? brand_logo_url($headerBrand, true) : null;
  $dual = $hasBrandLogo;
@endphp

<div class="hdr-logos {{ $dual ? 'hdr-logos--dual' : '' }}">
  <a href="{{ route('home') }}" class="hdr-logos__store" title="{{ $storeName ?? setting('store.name', 'متجر العلامات') }}">
    @include('partials.store-logo', [
      'showName' => ! $dual && !($storeLogo ?? store_logo_url()),
      'imgClass' => $dual
        ? 'h-7 md:h-9 w-auto max-w-[72px] md:max-w-[120px] max-h-7 md:max-h-9 object-contain object-center rounded-md shrink-0'
        : 'h-8 md:h-9 w-auto max-w-[110px] md:max-w-[120px] max-h-8 md:max-h-9 object-contain object-center rounded-md shrink-0',
      'fallbackClass' => $dual
        ? 'w-7 h-7 md:w-9 md:h-9 rounded-lg bg-ink text-paper grid place-items-center font-extrabold text-[11px] md:text-sm shrink-0'
        : 'w-8 h-8 md:w-9 md:h-9 rounded-xl bg-ink text-paper grid place-items-center font-extrabold text-sm shrink-0',
    ])
  </a>

  @if($dual)
  <span class="hdr-logos__divider" aria-hidden="true"></span>
  <a href="{{ route('brand.show', $headerBrand->slug) }}" class="hdr-logos__brand" title="{{ $headerBrand->name }}">
    @if($brandLogo)
      <img src="{{ $brandLogo }}" alt="{{ $headerBrand->name }}" class="hdr-logos__brand-img" width="56" height="32" loading="eager">
    @else
      <span class="hdr-logos__brand-mark">{{ $headerBrand->mark ?: mb_substr($headerBrand->name, 0, 1) }}</span>
    @endif
    <span class="hdr-logos__brand-name">{{ $headerBrand->name }}</span>
  </a>
  @endif
</div>
