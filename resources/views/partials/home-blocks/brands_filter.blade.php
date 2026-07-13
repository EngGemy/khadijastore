{{-- Block: brands_filter — premium horizontal store picker --}}
@php
  $blockBrands = $block->resolvedBrands ?? collect();
@endphp
@if($blockBrands->isNotEmpty())
<section id="store-brands-filter" class="sticky top-[68px] z-30 backdrop-blur-xl">
  <div class="store-filter-head">
    <p class="text-[11px] font-black tracking-[.12em] uppercase text-ink/40">فلتر حسب المتجر</p>
    <span class="text-[10px] font-bold text-ink/30 bg-paper2 px-2 py-0.5 rounded-full">{{ $blockBrands->count() }} متجر</span>
  </div>
  <div class="store-filter-track" data-home-brand-filter>
    <div class="store-chip-group store-chip-group--solo">
      <button type="button"
              class="home-brand-chip is-active"
              data-brand-id=""
              aria-pressed="true">
        <span class="inline-grid place-items-center w-8 h-8 rounded-lg bg-ink text-white text-[10px] font-extrabold">★</span>
        <span>الكل</span>
      </button>
    </div>
    @foreach($blockBrands as $b)
    <div class="store-chip-group">
      <button type="button"
              class="home-brand-chip"
              data-brand-id="{{ $b->id }}"
              aria-pressed="false">
        @include('partials.brand-avatar', ['brand' => $b, 'size' => 'sm'])
        <span>{{ $b->name }}</span>
      </button>
      <a href="{{ route('brand.show', $b->slug) }}"
         class="store-chip-link"
         title="زيارة {{ $b->name }}"
         aria-label="زيارة متجر {{ $b->name }}">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
      </a>
    </div>
    @endforeach
  </div>
</section>
@endif
