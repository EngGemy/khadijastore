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
      <span class="inline-flex items-center gap-2 text-[11px] font-black tracking-[.16em] uppercase text-brand mb-2.5">
        <span class="w-1.5 h-1.5 rounded-full bg-brand"></span>{{ $eyebrow }}
      </span>
      <h2 class="font-extrabold tracking-tight text-ink" style="font-size:clamp(22px,3.2vw,32px)">{{ $title }}</h2>
    </div>
    <a href="{{ route('brands.index') }}" class="text-sm font-bold text-ink inline-flex items-center gap-1.5 hover:gap-2.5 transition-all whitespace-nowrap hover:text-brand">عرض الكل <span>←</span></a>
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
