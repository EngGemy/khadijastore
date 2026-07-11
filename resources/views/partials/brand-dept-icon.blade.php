@props(['dept', 'brand', 'index' => 0])

@php
  $colors = ['#16a34a','#2563eb','#7c3aed','#db2777','#ea580c','#0891b2','#4f46e5','#059669'];
  $color = $colors[$index % count($colors)];
  $initial = mb_substr($dept->name, 0, 1);
@endphp

<a href="{{ route('brand.shop', [$brand->slug, 'dept' => $dept->id]) }}"
   class="brand-cat-chip snap-start flex-shrink-0 group">
  <div class="brand-cat-chip__icon" style="--chip-color:{{ $color }}">
    @if($dept->cover_image)
      <img src="{{ $dept->cover_image }}" alt="{{ $dept->name }}" class="w-full h-full object-cover rounded-full" loading="lazy">
    @else
      <span>{{ $initial }}</span>
    @endif
    <span class="brand-cat-chip__ring"></span>
  </div>
  <span class="brand-cat-chip__label">{{ $dept->name }}</span>
  <span class="brand-cat-chip__count">{{ $dept->display_count }}</span>
</a>
