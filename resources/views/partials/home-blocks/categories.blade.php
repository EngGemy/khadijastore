{{-- Block: categories — 4-card category grid --}}
@php
  $data     = $block->data ?? [];
  $items    = $data['items'] ?? [];
  $eyebrow  = $block->subtitle ?? setting('home.categories.eyebrow', 'تسوّق حسب الفئة · CATEGORIES');
  $title    = $block->title    ?? setting('home.categories.title',   'كل ما تحتاجه، مصنّف بعناية');

  $bgMap = [
    'bg-ink'          => 'background:none',
    'gradient-dark'   => 'background:linear-gradient(155deg,#1c1c1c,#333)',
    'gradient-darker' => 'background:linear-gradient(155deg,#0a0a0a,#262626)',
    'gradient-mixed'  => 'background:linear-gradient(155deg,#242424,#0d0d0d)',
  ];
@endphp
@if(!empty($items))
<section id="{{ ($assignBrandsAnchor ?? false) ? 'brands' : 'cats' }}" class="home-section max-w-[1180px] mx-auto px-4 sm:px-5">
  <div class="reveal mb-9">
    <span class="text-xs font-bold tracking-[.14em] uppercase text-accentDark block mb-2.5">{{ $eyebrow }}</span>
    <h2 class="font-extrabold tracking-tight" style="font-size:clamp(24px,3.5vw,36px)">{{ $title }}</h2>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 stagger">
    @foreach($items as $item)
      @php
        $bgStyle = $bgMap[$item['bg_style'] ?? 'bg-ink'] ?? 'background:none';
        $baseClass = ($item['bg_style'] ?? 'bg-ink') === 'bg-ink' ? 'bg-ink' : '';
      @endphp
      <a href="{{ !empty($item['link']) && str_starts_with($item['link'], '/') ? url($item['link']) : ($item['link'] ?? '#') }}"
         class="group relative rounded-[28px] overflow-hidden aspect-[.82] flex flex-col justify-end p-6 text-white {{ $baseClass }} hover:-translate-y-1.5 hover:shadow-lg2 transition-all duration-500"
         style="{{ $bgStyle }}">
        <span class="absolute rounded-full border border-white/25 grid place-items-center group-hover:bg-white group-hover:text-ink group-hover:-rotate-45 transition-all"
              style="top:22px;inset-inline-start:22px;width:34px;height:34px">↗</span>
        <span class="text-[34px] mb-auto opacity-90 group-hover:scale-110 transition-transform duration-500">{{ $item['icon'] ?? '📦' }}</span>
        <span class="font-extrabold text-lg tracking-tight leading-tight">{{ $item['label'] ?? '' }}</span>
        <span class="text-[11px] font-semibold opacity-50 tracking-widest mt-1 en">{{ $item['sublabel'] ?? '' }}</span>
      </a>
    @endforeach
  </div>
</section>
@endif
