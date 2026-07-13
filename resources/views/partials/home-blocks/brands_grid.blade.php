{{-- Block: brands_grid — premium brand store cards --}}
@php
  $blockBrands = $block->resolvedBrands ?? collect();
  $eyebrow     = $block->subtitle ?? setting('home.brands.eyebrow', 'شركاؤنا · OUR BRANDS');
  $title       = $block->title    ?? setting('home.brands.title',   'براندات تثق بها');
@endphp
@if($blockBrands->isNotEmpty())
<section id="brands" class="home-section max-w-[1180px] mx-auto px-4 sm:px-5">
  <div class="reveal flex items-end justify-between gap-5 mb-8">
    <div>
      <span class="text-xs font-bold tracking-[.14em] uppercase text-accentDark block mb-2.5">{{ $eyebrow }}</span>
      <h2 class="font-extrabold tracking-tight" style="font-size:clamp(24px,3.5vw,36px)">{{ $title }}</h2>
    </div>
    <a href="{{ route('home') }}#brands" class="text-sm font-bold text-accentDark inline-flex items-center gap-1.5 hover:gap-2.5 transition-all whitespace-nowrap">عرض الكل <span>←</span></a>
  </div>
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 stagger">
    @foreach($blockBrands as $b)
    <a href="{{ route('brand.show', $b->slug) }}" class="brand-store-card group">
      <div class="flex items-center gap-3.5">
        @include('partials.brand-avatar', ['brand' => $b, 'size' => 'lg', 'class' => 'group-hover:scale-105 transition-transform duration-500'])
        <div class="min-w-0">
          <h3 class="font-extrabold text-lg tracking-tight truncate">{{ $b->name }}</h3>
          <div class="text-xs text-ink/52 font-semibold truncate">{{ $b->category_label }}</div>
        </div>
      </div>
      @if($b->description)
      <p class="text-sm text-ink/52 leading-relaxed line-clamp-2">{{ $b->description }}</p>
      @endif
      <div class="brand-store-card__footer">
        <span class="text-[13px] text-ink/52 font-semibold">{{ $b->products_count ?? 0 }} منتج</span>
        <span class="brand-store-card__cta">زيارة المتجر <span>←</span></span>
      </div>
    </a>
    @endforeach
  </div>
</section>
@endif
