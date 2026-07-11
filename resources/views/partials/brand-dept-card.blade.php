@props(['dept', 'brand', 'index' => 0])

@php
  $colors = ['#16a34a','#2563eb','#7c3aed','#db2777','#ea580c','#0891b2','#4f46e5','#059669'];
  $color = $colors[$index % count($colors)];
  $initial = mb_substr($dept->name, 0, 1);
@endphp

<a href="{{ route('brand.shop', [$brand->slug, 'dept' => $dept->id]) }}" class="brand-dept group card-shine">
  <div class="brand-dept__shine"></div>
  <span class="brand-dept__badge" style="background:{{ $color }}">{{ $initial }}</span>
  <span class="brand-dept__count">{{ $dept->display_count }} منتج</span>

  @if($dept->cover_image)
    <img src="{{ $dept->cover_image }}" alt="{{ $dept->name }}" class="brand-dept__img" loading="lazy">
  @else
    <div class="brand-dept__img" style="background:linear-gradient(145deg,{{ $color }}33,{{ $color }}88)"></div>
  @endif

  <div class="brand-dept__overlay"></div>
  <div class="brand-dept__body">
    <h3 class="font-extrabold text-[16px] sm:text-[17px] leading-snug">{{ $dept->name }}</h3>
    <p class="text-[12px] text-white/65 font-semibold mt-1.5 inline-flex items-center gap-1.5 group-hover:gap-2.5 transition-all">
      تصفّح القسم <span aria-hidden="true">←</span>
    </p>
  </div>
</a>
