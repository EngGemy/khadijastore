{{-- Block: categories — circular Souqi-style row --}}
@php
  $data     = $block->data ?? [];
  $items    = $data['items'] ?? [];
  $eyebrow  = $block->subtitle ?? setting('home.categories.eyebrow', 'تسوّق حسب الفئة');
  $title    = $block->title    ?? setting('home.categories.title',   'الأقسام');
@endphp
@if(!empty($items))
<section id="{{ ($assignBrandsAnchor ?? false) ? 'brands' : 'cats' }}" class="home-section bg-paper2/60">
  <div class="max-w-[1180px] mx-auto px-4 sm:px-5">
    <div class="reveal mb-8 text-center sm:text-start">
      <span class="inline-flex items-center gap-2 text-[11px] font-black tracking-[.16em] uppercase text-brand mb-2.5">
        <span class="w-1.5 h-1.5 rounded-full bg-brand"></span>{{ $eyebrow }}
      </span>
      <h2 class="font-extrabold tracking-tight text-ink" style="font-size:clamp(22px,3.2vw,32px)">{{ $title }}</h2>
    </div>

    <div class="flex gap-5 sm:gap-7 overflow-x-auto pb-2 -mx-1 px-1 stagger scrollbar-none" style="scrollbar-width:none;-webkit-overflow-scrolling:touch">
      @foreach($items as $item)
      <a href="{{ !empty($item['link']) && str_starts_with($item['link'], '/') ? url($item['link']) : ($item['link'] ?? route('products.index')) }}"
         class="group flex flex-col items-center gap-2.5 shrink-0 w-[92px] sm:w-[104px] text-center">
        <span class="cat-circle" aria-hidden="true">{{ $item['icon'] ?? '📦' }}</span>
        <span class="font-extrabold text-[13px] leading-tight text-ink truncate w-full">{{ $item['label'] ?? '' }}</span>
        @if(!empty($item['sublabel']))
        <span class="text-[11px] font-semibold text-muted -mt-1">{{ $item['sublabel'] }}</span>
        @endif
      </a>
      @endforeach
    </div>
  </div>
</section>
@endif
