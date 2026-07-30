{{-- Sticky one-tap order bar on brand pages (COD / WhatsApp) --}}
@props(['brand'])

@php
  $wa = preg_replace('/\D/', '', (string) ($brand->whatsapp ?? ''));
  $shopUrl = route('brand.shop', $brand->slug);
  $waMsg = rawurlencode("السلام عليكم، أريد الطلب من متجر {$brand->name}");
  $waHref = $wa !== '' ? "https://wa.me/{$wa}?text={$waMsg}" : null;
@endphp
<div class="brand-order-bar md:hidden" role="region" aria-label="اطلب بسرعة">
  <a href="{{ $shopUrl }}" class="brand-order-bar__shop">
    تصفّح المنتجات
  </a>
  @if($waHref)
  <a href="{{ $waHref }}" target="_blank" rel="noopener" class="brand-order-bar__wa">
    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
    اطلب بضغطة
  </a>
  @else
  <a href="{{ $shopUrl }}" class="brand-order-bar__wa">اطلب الآن</a>
  @endif
</div>