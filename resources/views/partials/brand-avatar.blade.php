{{--
  Recommended brand logo sizes (documented for merchants/admin):
  - Upload: 512×512px PNG/WebP with transparent background (max ~6MB)
  - Display thumb conversion: 200×200 (contain)
  - UI sizes: xs 24px · sm 32px · md 44px · lg 56px
--}}
@props([
    'brand',
    'size' => 'md',
    'class' => '',
])

@php
  $logo = brand_logo_url($brand, true);
  $sizes = [
    'xs' => 'w-6 h-6 text-[9px] rounded-md',
    'sm' => 'w-8 h-8 text-[10px] rounded-lg',
    'md' => 'w-11 h-11 text-sm rounded-xl',
    'lg' => 'w-14 h-14 text-lg rounded-2xl',
  ];
  $px = ['xs' => 24, 'sm' => 32, 'md' => 44, 'lg' => 56];
  $box = $sizes[$size] ?? $sizes['md'];
  $dim = $px[$size] ?? 44;
@endphp

<span {{ $attributes->merge([
  'class' => "brand-avatar inline-grid place-items-center shrink-0 bg-gradient-to-br from-ink to-ink2 text-white font-extrabold overflow-hidden ring-1 ring-black/5 {$box} {$class}",
  'data-rec-size' => '512x512',
  'title' => 'شعار موصى به: 512×512px (PNG/WebP شفاف)',
]) }}>
  @if($logo)
    <img src="{{ $logo }}" alt="{{ $brand->name }}" width="{{ $dim }}" height="{{ $dim }}"
         class="w-full h-full object-contain bg-white p-0.5" loading="lazy" decoding="async"
         onerror="this.hidden=true;this.nextElementSibling.hidden=false">
    <span class="brand-avatar__mark" hidden>{{ $brand->mark }}</span>
  @else
    <span class="brand-avatar__mark">{{ $brand->mark }}</span>
  @endif
</span>
