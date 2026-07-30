{{-- Sticky one-tap order / WhatsApp bar on brand pages --}}
@props([
  'brand',
  'context' => 'home', // home | shop | manufacturers
])

@php
  $wa = preg_replace('/\D/', '', (string) ($brand->whatsapp ?? ''));
  $shopUrl = route('brand.shop', $brand->slug);
  $pageLabel = match ($context) {
    'shop' => 'صفحة المنتجات',
    'manufacturers' => 'صفحة البراندات المرتبطة',
    default => 'المتجر',
  };
  $waMsg = rawurlencode("السلام عليكم، أريد الطلب من متجر {$brand->name} ({$pageLabel})");
  $waHref = $wa !== '' ? "https://wa.me/{$wa}?text={$waMsg}" : null;
  $showShop = $context !== 'shop';
@endphp
@if($waHref || $showShop)
<div class="brand-order-bar md:hidden" role="region" aria-label="اطلب بسرعة">
  @if($showShop)
  <a href="{{ $shopUrl }}" class="brand-order-bar__shop {{ $waHref ? '' : 'brand-order-bar__shop--solo' }}">
    تصفّح المنتجات
  </a>
  @endif
  @if($waHref)
  <a href="{{ $waHref }}" target="_blank" rel="noopener" class="brand-order-bar__wa {{ $showShop ? '' : 'brand-order-bar__wa--solo' }}">
    <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
    واتساب
  </a>
  @elseif($showShop)
  <a href="{{ $shopUrl }}" class="brand-order-bar__wa">اطلب الآن</a>
  @endif
</div>
@endif
