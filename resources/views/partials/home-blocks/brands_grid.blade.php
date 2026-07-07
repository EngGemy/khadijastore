{{-- Block: brands_grid — brand cards grid --}}
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
  <div class="grid md:grid-cols-3 gap-5 stagger">
    @foreach($blockBrands as $b)
    <a href="{{ route('brand.show', $b->slug) }}"
       class="group border border-line rounded-[18px] flex flex-col gap-4 relative overflow-hidden bg-paper hover:-translate-y-1.5 hover:shadow-lg2 transition-all duration-500"
       style="padding:26px">
      <div class="flex items-center gap-3.5">
        <span class="rounded-2xl bg-ink text-white grid place-items-center font-extrabold text-2xl group-hover:-rotate-6 group-hover:scale-105 transition-transform duration-500 overflow-hidden"
              style="width:52px;height:52px">
          @php $logo = $b->getFirstMediaUrl('logo', 'thumb'); @endphp
          @if($logo)<img src="{{ $logo }}" alt="{{ $b->name }}" class="w-full h-full object-cover" loading="lazy">@else{{ $b->mark }}@endif
        </span>
        <div>
          <h3 class="font-extrabold text-lg tracking-tight">{{ $b->name }}</h3>
          <div class="text-xs text-ink/52 font-semibold">{{ $b->category_label }}</div>
        </div>
      </div>
      <p class="text-sm text-ink/52 leading-relaxed">{{ $b->description }}</p>
      <div class="flex items-center justify-between mt-auto pt-4 border-t border-line">
        <span class="text-[13px] text-ink/52 font-semibold">{{ $b->products_count ?? 0 }} منتج</span>
        <span class="text-sm font-bold text-accentDark inline-flex gap-1.5 group-hover:gap-2.5 transition-all">زيارة المتجر <span>←</span></span>
      </div>
    </a>
    @endforeach
  </div>
</section>
@endif
