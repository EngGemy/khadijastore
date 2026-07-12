{{-- Block: brands_filter — horizontal scroll store picker (mobile-first) --}}
@php
  $blockBrands = $block->resolvedBrands ?? collect();
@endphp
@if($blockBrands->isNotEmpty())
<section id="store-brands-filter" class="border-b border-line bg-paper/95 backdrop-blur-md sticky top-[68px] z-30 shadow-[0_4px_16px_rgba(0,0,0,.04)]">
  <div class="px-4 py-2.5 flex items-center justify-between gap-2 border-b border-line/40">
    <p class="text-[11px] font-bold text-ink/40">فلتر حسب المتجر</p>
    <span class="text-[10px] font-bold text-ink/30">{{ $blockBrands->count() }} متجر</span>
  </div>
  <div class="flex gap-2 overflow-x-auto overscroll-x-contain py-3 px-4 scroll-smooth"
       style="scrollbar-width:none;-webkit-overflow-scrolling:touch"
       data-home-brand-filter>
    <button type="button"
            class="home-brand-chip shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-full border-[1.5px] border-ink bg-ink text-white text-[13px] font-bold"
            data-brand-id="">
      الكل
    </button>
    @foreach($blockBrands as $b)
    <div class="inline-flex items-center shrink-0 gap-0.5">
      <button type="button"
              class="home-brand-chip shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-full border-[1.5px] border-line bg-paper text-[13px] font-bold text-ink/55"
              data-brand-id="{{ $b->id }}">
        <span class="inline-grid place-items-center w-7 h-7 rounded-lg bg-ink text-white text-[10px] font-extrabold overflow-hidden shrink-0">
          @php $logo = $b->getFirstMediaUrl('logo', 'thumb'); @endphp
          @if($logo)<img src="{{ $logo }}" alt="" class="w-full h-full object-cover" loading="lazy">@else{{ $b->mark }}@endif
        </span>
        {{ $b->name }}
      </button>
      <a href="{{ route('brand.show', $b->slug) }}"
         class="shrink-0 w-8 h-8 rounded-full border border-line bg-paper grid place-items-center text-ink/45 hover:text-accent hover:border-accent transition"
         title="زيارة {{ $b->name }}"
         aria-label="زيارة {{ $b->name }}">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
      </a>
    </div>
    @endforeach
  </div>
</section>
@endif
