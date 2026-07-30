{{-- Featured brand spotlight — سند للعطارة (elevated, not exclusive) --}}
@php
  $spotlightBrand = $featuredBrand ?? featured_storefront_brand();
  $spotlightProducts = $featuredBrandProducts ?? collect();
  $spotlightLogo = $spotlightBrand ? brand_logo_url($spotlightBrand) : null;
  $spotlightLine = filled($spotlightBrand?->category_label)
    ? $spotlightBrand->category_label
    : 'أعشاب وزيوت طبيعية · جودة موثوقة';
@endphp
@if($spotlightBrand && $spotlightProducts->isNotEmpty())
<section id="sanad-spotlight" class="home-section sanad-spotlight relative overflow-hidden" aria-labelledby="sanad-spotlight-title">
  <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
    <div class="absolute inset-0" style="background:linear-gradient(165deg,#0B1D36 0%,#132a4a 48%,#0B1D36 100%)"></div>
    <div class="absolute -top-28 -end-20 w-[420px] h-[420px] rounded-full opacity-70"
         style="background:radial-gradient(circle,rgba(232,93,4,.28),transparent 68%)"></div>
    <div class="absolute bottom-0 -start-16 w-[320px] h-[320px] rounded-full opacity-40"
         style="background:radial-gradient(circle,rgba(255,255,255,.08),transparent 70%)"></div>
  </div>

  <div class="max-w-[1180px] mx-auto px-4 sm:px-5 relative z-10">
    <div class="reveal flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6 mb-5 sm:mb-7">
      <div class="flex items-center gap-3.5 min-w-0">
        <div class="sanad-spotlight__logo shrink-0">
          @if($spotlightLogo)
            <img src="{{ $spotlightLogo }}" alt="{{ $spotlightBrand->name }}" width="56" height="56" loading="lazy" class="w-full h-full object-contain">
          @else
            <span class="font-extrabold text-[22px] text-[#0B1D36]">{{ $spotlightBrand->mark ?: mb_substr($spotlightBrand->name, 0, 1) }}</span>
          @endif
        </div>
        <div class="min-w-0">
          <span class="text-[10px] sm:text-[11px] font-black tracking-[.16em] uppercase text-[#F97316] block mb-1">متجر مميّز · FEATURED</span>
          <h2 id="sanad-spotlight-title" class="font-extrabold tracking-tight text-white truncate" style="font-size:clamp(20px,3.2vw,30px);line-height:1.2">
            {{ $spotlightBrand->name }}
          </h2>
          <p class="text-white/65 text-[12px] sm:text-[13px] mt-1 font-medium leading-relaxed line-clamp-2">{{ $spotlightLine }}</p>
        </div>
      </div>
      <a href="{{ route('brand.show', $spotlightBrand->slug) }}"
         class="shine inline-flex items-center justify-center gap-2 bg-[#E85D04] text-white font-extrabold rounded-2xl px-5 py-3 text-[13px] hover:bg-[#F97316] hover:-translate-y-0.5 transition-all shadow-[0_12px_28px_-10px_rgba(232,93,4,.55)] whitespace-nowrap self-start sm:self-auto min-h-[48px]">
        تسوّق سند للعطارة
        <span aria-hidden="true">←</span>
      </a>
    </div>

    <div class="flex gap-3 sm:gap-4 overflow-x-auto app-h-scroll pb-1 -mx-1 px-1" style="scrollbar-width:none;-webkit-overflow-scrolling:touch">
      @foreach($spotlightProducts->take(10) as $p)
      @php
        $discount = ($p->compare_price && $p->compare_price > $p->price)
          ? (int) round((1 - $p->price / $p->compare_price) * 100) : 0;
      @endphp
      <a href="{{ route('product.show', $p->slug) }}"
         class="sanad-spotlight__card group w-[42vw] max-w-[180px] sm:w-[160px] shrink-0">
        <div class="sanad-spotlight__media">
          @if($discount > 0)
            <span class="sanad-spotlight__badge">-{{ $discount }}%</span>
          @elseif($p->badge)
            <span class="sanad-spotlight__badge sanad-spotlight__badge--soft">{{ $p->badge }}</span>
          @endif
          @include('partials.product-cover', ['product' => $p])
        </div>
        <div class="sanad-spotlight__body">
          <h3 class="sanad-spotlight__title">{{ $p->name }}</h3>
          <div class="sanad-spotlight__price-row">
            <span class="sanad-spotlight__price">{{ number_format($p->price) }}</span>
            <span class="text-[10px] font-bold text-white/45">ج.م</span>
            @if($discount > 0)
            <span class="sanad-spotlight__compare">{{ number_format($p->compare_price) }}</span>
            @endif
          </div>
        </div>
      </a>
      @endforeach

      <a href="{{ route('brand.show', $spotlightBrand->slug) }}"
         class="sanad-spotlight__more shrink-0 w-[42vw] max-w-[180px] sm:w-[140px] flex flex-col items-center justify-center gap-2 rounded-2xl border border-dashed border-white/25 text-white/80 hover:text-white hover:border-[#F97316]/60 hover:bg-white/5 transition min-h-[160px]">
        <span class="w-10 h-10 rounded-xl bg-[#E85D04]/20 text-[#F97316] grid place-items-center" aria-hidden="true">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </span>
        <span class="text-[12px] font-extrabold text-center px-2">كل منتجات المتجر</span>
      </a>
    </div>
  </div>
</section>
@endif
