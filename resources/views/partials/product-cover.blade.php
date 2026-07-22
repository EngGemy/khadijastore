{{--
  Recommended product cover sizes:
  - Upload: 1000×1000px (1:1) JPG/WebP
  - Thumb conversion: 400×400 · Large: 1000×1000
--}}
@props([
    'product',
    'class' => '',
    'imgClass' => 'w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-110',
])

@php
  $cover = product_cover_url($product, true);
  $fallback = $product->mark ?? mb_substr($product->name, 0, 1);
@endphp

<div {{ $attributes->merge([
  'class' => "product-cover relative w-full h-full grid place-items-center bg-gradient-to-br from-paper2 via-paper to-paper3 {$class}",
  'data-rec-size' => '1000x1000',
  'title' => 'صورة المنتج الموصى بها: 1000×1000px (1:1)',
]) }}>
  @if($cover)
    <img src="{{ $cover }}" alt="{{ $product->name }}" width="400" height="400" class="{{ $imgClass }}" loading="lazy" decoding="async"
         onerror="this.hidden=true;this.nextElementSibling.hidden=false">
    <span class="product-cover__fallback font-extrabold text-3xl text-ink/10 select-none" hidden>{{ $fallback }}</span>
  @else
    <span class="product-cover__fallback font-extrabold text-3xl text-ink/10 select-none">{{ $fallback }}</span>
  @endif
</div>
