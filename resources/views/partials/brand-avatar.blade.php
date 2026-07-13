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
  $box = $sizes[$size] ?? $sizes['md'];
@endphp

<span {{ $attributes->merge(['class' => "brand-avatar inline-grid place-items-center shrink-0 bg-gradient-to-br from-ink to-ink2 text-white font-extrabold overflow-hidden ring-1 ring-black/5 {$box} {$class}"]) }}>
  @if($logo)
    <img src="{{ $logo }}" alt="{{ $brand->name }}" class="w-full h-full object-contain bg-white p-0.5" loading="lazy" decoding="async"
         onerror="this.hidden=true;this.nextElementSibling.hidden=false">
    <span class="brand-avatar__mark" hidden>{{ $brand->mark }}</span>
  @else
    <span class="brand-avatar__mark">{{ $brand->mark }}</span>
  @endif
</span>
