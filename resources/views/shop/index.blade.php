@extends('layouts.app')
@section('title', $home['seo']['title'] ?? ($storeName . ' · BRANDS'))

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
@endpush

@section('meta')
@php
  $seoTitle = $home['seo']['title'] ?? ($storeName . ' · اختار الأفضل بكل ثقة');
  $seoDesc  = $home['seo']['description'] ?? 'اكتشف منتجات أصلية من أشهر البراندات العالمية والمحلية. توصيل سريع، دفع عند الاستلام، وضمان رضا تام.';
  $seoImg   = $home['seo']['image'] ?? '';
  $seoUrl   = url('/');
  $storeSocialArr = is_array($storeSocial ?? []) ? ($storeSocial ?? []) : [];
@endphp
<meta name="description" content="{{ $seoDesc }}">
<link rel="canonical" href="{{ $seoUrl }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDesc }}">
<meta property="og:url" content="{{ $seoUrl }}">
@if($seoImg)<meta property="og:image" content="{{ $seoImg }}">@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDesc }}">
@if($seoImg)<meta name="twitter:image" content="{{ $seoImg }}">@endif
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Store",
  "name": "{{ e($storeName) }}",
  "url": "{{ $seoUrl }}",
  "@@id": "{{ $seoUrl }}",
  "description": "{{ e($seoDesc) }}",
  "image": "{{ e($seoImg) }}"
  @if(!empty($storeSocialArr)),"sameAs": {{ json_encode(array_values($storeSocialArr)) }}@endif
  @if(setting('store.support_phone')),"contactPoint":{"@@type":"ContactPoint","telephone":"{{ e(setting('store.support_phone')) }}","contactType":"customer support"}@endif
}
</script>
@endsection

@section('content')
@include('partials.strip')

{{-- ═══ NAV ═══════════════════════════════════════════════════════════════ --}}
@include('partials.header')

{{-- ═══ HERO — video background, Souqi identity ═══════════════════════════ --}}
@php
  $hero        = $home['hero'];
  $stats       = $hero['stats'] ?? [];
  $primaryHref = $hero['primary_btn_link'] ?? '';
  $secondaryHref = $hero['secondary_btn_link'] ?? '';
  if ($primaryHref === '' || $primaryHref === '#' || $primaryHref === '#products') {
    $primaryHref = route('products.index');
  } elseif (str_starts_with($primaryHref, '#')) {
    $primaryHref = url('/'.$primaryHref);
  } elseif (str_starts_with($primaryHref, '/')) {
    $primaryHref = url($primaryHref);
  }
  if ($secondaryHref === '' || $secondaryHref === '#' || $secondaryHref === '#brands') {
    $secondaryHref = route('brands.index');
  } elseif (str_starts_with($secondaryHref, '#')) {
    $secondaryHref = url('/'.$secondaryHref);
  } elseif (str_starts_with($secondaryHref, '/')) {
    $secondaryHref = url($secondaryHref);
  }
@endphp
<section id="hero" class="relative overflow-hidden min-h-[min(78vh,640px)] flex items-center">
  <div class="hero-video-wrap" aria-hidden="true">
    <video class="hero-bg-video" autoplay muted loop playsinline preload="metadata">
      <source src="{{ asset('videos/hero-logo.mp4') }}" type="video/mp4">
    </video>
    <div class="hero-video-overlay"></div>
  </div>

  <div class="max-w-[1180px] mx-auto px-4 sm:px-5 relative z-10 w-full py-16 md:py-20 lg:py-24">
    <div class="max-w-[560px]">
      <div class="overflow-hidden">
        <span class="inline-flex items-center gap-2 bg-brand text-white text-[11px] font-black tracking-[.12em] uppercase rounded-full px-3.5 py-1.5 shadow-lg animate-heroFade">
          <span class="w-1.5 h-1.5 rounded-full bg-white animate-blink shrink-0"></span>
          {{ $hero['eyebrow'] }}
        </span>
      </div>

      <h1 class="font-extrabold tracking-tight text-white drop-shadow-sm" style="font-size:clamp(34px,5.6vw,56px);line-height:1.2;letter-spacing:-.03em;margin:20px 0 0">
        <span class="hl-line overflow-hidden block pb-1">
          <span style="animation-delay:.06s">{{ $hero['title_line1'] }}</span>
          <span class="relative ms-[.2em] text-brand" style="animation-delay:.15s">{{ $hero['title_highlight'] }}</span>
        </span>
        <span class="hl-line overflow-hidden block">
          <span style="animation-delay:.24s">{{ $hero['title_line2'] }}</span>
        </span>
      </h1>

      <p class="text-white leading-[1.8] animate-blurReveal" style="font-size:clamp(15px,1.7vw,17px);max-width:420px;margin:18px 0 0;animation-delay:.4s;color:rgba(255,255,255,.92);text-shadow:0 1px 12px rgba(6,18,36,.45)">{{ $hero['paragraph'] }}</p>

      <div class="flex gap-3 flex-wrap animate-heroFade" style="margin-top:28px;animation-delay:.56s">
        <a href="{{ $primaryHref }}"
           class="shine bg-brand text-white font-extrabold rounded-2xl shadow-cta hover:bg-accent hover:-translate-y-1 transition-all"
           style="padding:15px 28px;font-size:15px">{{ $hero['primary_btn_text'] }}</a>
        <a href="{{ $secondaryHref }}"
           class="border-[1.5px] border-white/70 text-white font-bold rounded-2xl hover:bg-white hover:text-ink hover:-translate-y-1 transition-all backdrop-blur-sm"
           style="padding:14px 26px;font-size:15px;background:rgba(255,255,255,.08)">{{ $hero['secondary_btn_text'] }}</a>
      </div>

      @if(!empty($stats))
      <div class="grid grid-cols-3 gap-2 sm:flex sm:flex-wrap animate-heroFade" style="margin-top:28px;padding-top:20px;border-top:1px solid rgba(255,255,255,.22);animation-delay:.7s">
        @foreach($stats as $i => $stat)
        <div class="{{ $i > 0 ? 'sm:border-s sm:border-white/25 sm:ps-6' : '' }} text-center sm:text-start pe-0 sm:pe-6">
          <div class="font-extrabold tracking-tight leading-none text-white" style="font-size:clamp(20px,3vw,30px)">{{ $stat['value'] }}</div>
          <div class="text-[11px] sm:text-[12px] mt-1.5 font-semibold leading-tight" style="color:rgba(255,255,255,.72)">{{ $stat['label'] }}</div>
        </div>
        @endforeach
      </div>
      @endif
    </div>

    <div class="flex items-center gap-2 mt-10 animate-heroFade" style="animation-delay:.8s" aria-hidden="true">
      <span class="w-6 h-1.5 rounded-full bg-brand"></span>
      <span class="w-1.5 h-1.5 rounded-full bg-white/35"></span>
      <span class="w-1.5 h-1.5 rounded-full bg-white/35"></span>
    </div>
  </div>
</section>

{{-- ═══ FEATURES STRIP ════════════════════════════════════════════════════ --}}
<section id="features" class="relative z-10 -mt-6 mb-2 px-4 sm:px-5">
  <div class="max-w-[1180px] mx-auto grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    <div class="feature-pill reveal">
      <span class="feature-pill__icon" aria-hidden="true">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </span>
      <div>
        <p class="font-extrabold text-[13px] text-ink leading-tight">دعم 24/7</p>
        <p class="text-[11px] font-semibold text-muted mt-0.5">مساعدة في أي وقت</p>
      </div>
    </div>
    <div class="feature-pill reveal">
      <span class="feature-pill__icon" aria-hidden="true">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
      </span>
      <div>
        <p class="font-extrabold text-[13px] text-ink leading-tight">استرجاع سهل</p>
        <p class="text-[11px] font-semibold text-muted mt-0.5">خلال أيام الاستلام</p>
      </div>
    </div>
    <div class="feature-pill reveal">
      <span class="feature-pill__icon" aria-hidden="true">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
      </span>
      <div>
        <p class="font-extrabold text-[13px] text-ink leading-tight">دفع مرن</p>
        <p class="text-[11px] font-semibold text-muted mt-0.5">عند الاستلام / تحويل</p>
      </div>
    </div>
    <div class="feature-pill reveal">
      <span class="feature-pill__icon" aria-hidden="true">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      </span>
      <div>
        <p class="font-extrabold text-[13px] text-ink leading-tight">تسوق آمن</p>
        <p class="text-[11px] font-semibold text-muted mt-0.5">منتجات أصلية 100%</p>
      </div>
    </div>
  </div>
</section>

{{-- ═══ OFFERS / العروض ═══════════════════════════════════════════════════ --}}
@include('partials.home-blocks.offers', ['offerProducts' => $offerProducts ?? collect()])

{{-- ═══ ALPHABET / حروف ═══════════════════════════════════════════════════ --}}
@include('partials.home-blocks.brands_alphabet', ['alphabetBrands' => $alphabetBrands ?? collect()])

{{-- ═══ FLEXIBLE HOME BLOCKS (براندات ومنتجات أولاً) ══════════════════════ --}}
@php
  $hasProductsBlock = $homeBlocks->contains(fn ($b) => $b->type === 'products_grid');
  $hasBrandsAnchor = $homeBlocks->contains(fn ($b) => $b->type === 'brands_grid');
  $brandsAnchorAssigned = $hasBrandsAnchor;
@endphp
@foreach($homeBlocks as $block)
  @php
    $assignBrandsAnchor = ! $brandsAnchorAssigned && in_array($block->type, ['brands_marquee', 'categories'], true);
    if ($assignBrandsAnchor) {
      $brandsAnchorAssigned = true;
    }
  @endphp
  @include('partials.home-blocks.' . $block->type, ['block' => $block, 'assignBrandsAnchor' => $assignBrandsAnchor])
@endforeach

{{-- احتياط: فلتر بدون بلوك منتجات (حالة السيرفر الحالية) --}}
@if(! $hasProductsBlock && ($homeProducts ?? collect())->isNotEmpty())
  @include('partials.home-blocks.products_grid', [
    'block' => (object) ['resolvedProducts' => $homeProducts],
  ])
@endif

{{-- ═══ DIRECTORY TEASERS — روابط لصفحات الدليل المنفصلة ═══════════════════ --}}
@if(($directory['doctorCount'] ?? 0) > 0 || ($directory['nurseryCount'] ?? 0) > 0)
<section class="bg-paper2 py-14 sm:py-16" id="directory-teasers">
  <div class="max-w-[1180px] mx-auto px-5">
    <div class="text-center mb-8 reveal">
      <span class="text-[11px] font-black tracking-[.16em] uppercase text-brand block mb-2 en">DIRECTORY</span>
      <h2 class="font-extrabold text-ink tracking-tight" style="font-size:clamp(22px,3.5vw,32px)">دليل الخدمات</h2>
      <p class="text-ink/50 text-[14px] mt-2 max-w-md mx-auto">صفحات مستقلة للبحث والتفاصيل — الأطباء والحضانات</p>
    </div>

    <div class="grid sm:grid-cols-2 gap-4 sm:gap-5">
      @if(($directory['doctorCount'] ?? 0) > 0)
      <a href="{{ route('directory.index', 'doctor') }}"
         class="group relative overflow-hidden rounded-[22px] bg-ink text-paper p-6 sm:p-7 flex flex-col gap-4 hover:-translate-y-1 transition-all duration-400 shadow-[0_16px_40px_-20px_rgba(11,29,54,.45)] reveal">
        <div class="absolute -top-16 -end-10 w-48 h-48 rounded-full pointer-events-none"
             style="background:radial-gradient(circle,rgba(249,115,22,.22),transparent 70%)"></div>
        <div class="relative z-10 flex items-start justify-between gap-3">
          <div>
            <span class="text-[11px] font-bold tracking-[.14em] uppercase text-brand en">DOCTORS</span>
            <h3 class="font-extrabold text-[22px] mt-1.5 leading-tight">دليل الأطباء</h3>
            <p class="text-paper/55 text-[13px] mt-2 leading-relaxed max-w-[280px]">ابحث بالتخصص والمحافظة وتواصل مباشرة</p>
          </div>
          <span class="w-11 h-11 rounded-2xl bg-brand/20 text-brand grid place-items-center shrink-0 group-hover:bg-brand group-hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </span>
        </div>
        <div class="relative z-10 flex items-center justify-between gap-3 pt-3 border-t border-paper/10 mt-auto">
          <span class="text-[13px] font-bold text-paper/70">{{ $directory['doctorCount'] }} طبيب مسجّل</span>
          <span class="text-[13px] font-extrabold text-brand group-hover:underline">عرض الكل</span>
        </div>
      </a>
      @endif

      @if(($directory['nurseryCount'] ?? 0) > 0)
      <a href="{{ route('directory.index', 'nursery') }}"
         class="group relative overflow-hidden rounded-[22px] bg-paper border border-line p-6 sm:p-7 flex flex-col gap-4 hover:-translate-y-1 hover:border-brand/30 transition-all duration-400 shadow-[0_12px_32px_-18px_rgba(11,29,54,.12)] reveal">
        <div class="absolute -bottom-16 -start-10 w-48 h-48 rounded-full pointer-events-none"
             style="background:radial-gradient(circle,rgba(232,93,4,.10),transparent 70%)"></div>
        <div class="relative z-10 flex items-start justify-between gap-3">
          <div>
            <span class="text-[11px] font-bold tracking-[.14em] uppercase text-brand en">NURSERIES</span>
            <h3 class="font-extrabold text-[22px] mt-1.5 leading-tight text-ink">دليل الحضانات</h3>
            <p class="text-ink/50 text-[13px] mt-2 leading-relaxed max-w-[280px]">اكتشف الحضانات المناسبة لطفلك حسب المنطقة والعمر</p>
          </div>
          <span class="w-11 h-11 rounded-2xl bg-ink text-paper grid place-items-center shrink-0 group-hover:bg-brand transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </span>
        </div>
        <div class="relative z-10 flex items-center justify-between gap-3 pt-3 border-t border-line mt-auto">
          <span class="text-[13px] font-bold text-ink/55">{{ $directory['nurseryCount'] }} حضانة مسجّلة</span>
          <span class="text-[13px] font-extrabold text-brand group-hover:underline">عرض الكل</span>
        </div>
      </a>
      @endif
    </div>
  </div>
</section>
@endif

{{-- ═══ المساعد الذكي CTA ══════════════════════════════════════════════════ --}}
@if(config('ai.enabled', true))
<section class="bg-paper2 py-16 reveal-scale" id="ai-assistant">
  <div class="max-w-[1180px] mx-auto px-5">
    <div class="rounded-2xl bg-ink text-paper overflow-hidden relative" style="padding:52px 40px">
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 start-0 w-72 h-72 rounded-full animate-spinSlow opacity-10"
             style="background:conic-gradient(from 0deg,transparent 80%,rgba(249,115,22,.7) 100%);filter:blur(50px);transform-origin:center"></div>
        <div class="absolute bottom-0 end-0 w-56 h-56 rounded-full animate-glowPulse"
             style="background:radial-gradient(circle,rgba(232,93,4,.14),transparent 70%)"></div>
      </div>
      <div class="relative z-10 flex flex-col md:flex-row items-center gap-8 text-center md:text-start">
        <div class="w-16 h-16 rounded-2xl bg-brand/20 flex-shrink-0 flex items-center justify-center animate-ring">
          <svg class="w-8 h-8 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            <circle cx="12" cy="10" r="1" fill="currentColor"/><circle cx="8" cy="10" r="1" fill="currentColor"/><circle cx="16" cy="10" r="1" fill="currentColor"/>
          </svg>
        </div>
        <div class="flex-1">
          <p class="text-brand font-black tracking-[.2em] text-xs mb-2">AI ASSISTANT · مدعوم بـ Gemini</p>
          <h2 class="font-extrabold text-[clamp(20px,3vw,28px)] leading-tight mb-2">اسأل المساعد الذكي</h2>
          <p class="text-white/70 text-sm max-w-md">رشّح لي منتجاً، قارن بين اثنين، اسأل عن السعر — المساعد يقرأ بيانات المتجر الحقيقية ويجيبك فوراً.</p>
        </div>
        <a href="{{ route('assistant.page') }}"
           class="shine flex-shrink-0 inline-flex items-center gap-2.5 bg-brand text-white font-extrabold rounded-xl px-7 py-3.5 hover:bg-accent transition shadow-cta text-[15px]">
          ابدأ الآن
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
          </svg>
        </a>
      </div>
    </div>
  </div>
</section>
@endif

{{-- ═══ FOOTER ════════════════════════════════════════════════════════════ --}}
@include('partials.footer')
@endsection

@push('scripts')
<script>
document.documentElement.classList.add('js');

// ── كشف التمرير الشامل ─────────────────────────────────────────────────────
const io = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('in');
      if (e.target.classList.contains('blur-in')) e.target.classList.add('visible');
      io.unobserve(e.target);
    }
  });
}, { threshold: .12, rootMargin: '0px 0px -40px 0px' });
document.querySelectorAll('.reveal, .reveal-scale, .stagger, .blur-in').forEach(el => io.observe(el));

// ── Alphabet brand filter ─────────────────────────────────────────────────
(function () {
  const cards = Array.from(document.querySelectorAll('#alphabet-brands .alphabet-brand'));
  const empty = document.getElementById('alphabet-empty');
  const grid = document.getElementById('alphabet-brands');
  if (!cards.length) return;

  function apply(letter) {
    let n = 0;
    cards.forEach(c => {
      const ok = !letter || c.dataset.letter === letter;
      c.style.display = ok ? '' : 'none';
      if (ok) n++;
    });
    if (empty) empty.classList.toggle('hidden', n > 0);
    if (grid) grid.classList.toggle('hidden', n === 0);
  }

  document.querySelectorAll('[data-letter-bar] .letter-chip, [data-letter-bar-latin] .letter-chip').forEach(btn => {
    btn.addEventListener('click', () => {
      if (btn.disabled) return;
      document.querySelectorAll('#letters .letter-chip').forEach(b => {
        b.classList.remove('is-active');
        b.setAttribute('aria-pressed', 'false');
      });
      btn.classList.add('is-active');
      btn.setAttribute('aria-pressed', 'true');
      apply(btn.dataset.letter || '');
    });
  });
})();

// Ensure hero video plays (some browsers need explicit play)
(function () {
  const v = document.querySelector('.hero-bg-video');
  if (!v) return;
  v.muted = true;
  const p = v.play();
  if (p && typeof p.catch === 'function') p.catch(() => {});
})();

// ── فلتر البراندات في الرئيسية ───────────────────────────────────────────
(function () {
  const filterBar = document.querySelector('[data-home-brand-filter]');
  const grid = document.getElementById('products-grid');
  const cards = grid ? Array.from(grid.querySelectorAll('.product-card')) : [];
  const emptyMsg = document.getElementById('products-filter-empty');
  if (!filterBar || !cards.length) return;

  function setChipActive(chip, active) {
    chip.classList.toggle('is-active', active);
    chip.setAttribute('aria-pressed', active ? 'true' : 'false');
  }

  function applyBrandFilter(brandId) {
    let visible = 0;
    cards.forEach(card => {
      const match = !brandId || String(card.dataset.brandId) === String(brandId);
      card.style.display = match ? '' : 'none';
      if (match) visible++;
    });
    if (emptyMsg) {
      emptyMsg.classList.toggle('hidden', visible > 0);
      if (grid) grid.classList.toggle('hidden', visible === 0);
    }
  }

  filterBar.querySelectorAll('.home-brand-chip').forEach(chip => {
    chip.addEventListener('click', function () {
      filterBar.querySelectorAll('.home-brand-chip').forEach(c => setChipActive(c, false));
      setChipActive(this, true);
      const brandId = this.dataset.brandId || '';
      applyBrandFilter(brandId);
      const productsSection = document.getElementById('products');
      if (productsSection && brandId) {
        productsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
})();
</script>
@endpush
