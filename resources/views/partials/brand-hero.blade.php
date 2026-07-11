@props(['brand', 'compact' => false, 'productCount' => 0, 'brandStats' => null, 'showActions' => true])

@php
  $logo = $brand->getFirstMediaUrl('logo', 'thumb');
  $stats = $brandStats ?? ['productCount' => $productCount, 'avgRating' => 0, 'totalSales' => 0];
  $count = $stats['productCount'] ?? $productCount;
  $rating = $stats['avgRating'] ?? 0;
@endphp

<section class="brand-hero {{ $compact ? 'py-5 sm:py-7' : 'py-6 sm:py-10 md:py-12' }}">
  <div class="brand-mesh" aria-hidden="true">
    <div class="brand-blob w-[280px] h-[280px] -top-16 -start-16" style="background:#16a34a"></div>
    <div class="brand-blob brand-blob-2 w-[220px] h-[220px] bottom-0 end-0" style="background:#6366f1"></div>
  </div>
  <div class="brand-hero__grid" aria-hidden="true"></div>

  <div class="max-w-[1180px] mx-auto px-4 sm:px-5 relative z-10">
    {{-- mobile-first: logo + name in one row --}}
    <div class="flex items-center gap-3.5 sm:gap-5">
      <div class="brand-hero__logo-wrap">
        <div class="brand-hero__logo-ring"></div>
        <div class="brand-hero__logo">
          @if($logo)
            <img src="{{ $logo }}" alt="{{ $brand->name }}" class="w-full h-full object-cover">
          @else
            {{ $brand->mark }}
          @endif
        </div>
      </div>
      <div class="min-w-0 flex-1">
        @if($brand->category_label)
          <span class="inline-block text-[10px] font-bold text-emerald-300/90 mb-1">{{ $brand->category_label }}</span>
        @endif
        <h1 class="font-extrabold tracking-tight leading-tight truncate" style="font-size:clamp(18px,4.5vw,36px)">{{ $brand->name }}</h1>
        @unless($compact)
          <p class="text-white/55 text-[12px] sm:text-[14px] mt-1 leading-relaxed line-clamp-2 max-sm:hidden">{{ $brand->description }}</p>
        @endunless
        <div class="flex flex-wrap gap-1.5 mt-2">
          <span class="brand-stat-pill">{{ $count }} منتج</span>
          @if($rating > 0)<span class="brand-stat-pill">{{ number_format($rating, 1) }}★</span>@endif
        </div>
      </div>
    </div>

    @if($showActions && !$compact)
      <div class="flex gap-2 mt-4">
        <a href="{{ route('brand.shop', $brand->slug) }}"
           class="flex-1 sm:flex-none text-center px-5 py-2.5 rounded-xl bg-white text-ink text-[13px] font-extrabold active:scale-[.98] transition-transform">
          تسوّق الآن
        </a>
        @if($brand->whatsapp)
          <a href="https://wa.me/{{ $brand->whatsapp }}" target="_blank" rel="noopener"
             class="px-4 py-2.5 rounded-xl bg-accent text-white text-[13px] font-extrabold active:scale-[.98] transition-transform">
            واتساب
          </a>
        @endif
      </div>
    @endif
  </div>
</section>
