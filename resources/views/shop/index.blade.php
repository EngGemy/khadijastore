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

      <h1 class="font-extrabold tracking-tight text-white" style="font-size:clamp(34px,5.6vw,56px);line-height:1.2;letter-spacing:-.03em;margin:20px 0 0">
        <span class="hl-line overflow-hidden block pb-1">
          <span style="animation-delay:.06s">{{ $hero['title_line1'] }}</span>
          <span class="relative ms-[.2em] text-brand" style="animation-delay:.15s">{{ $hero['title_highlight'] }}</span>
        </span>
        <span class="hl-line overflow-hidden block">
          <span style="animation-delay:.24s">{{ $hero['title_line2'] }}</span>
        </span>
      </h1>

      <p class="text-white/72 leading-[1.75] animate-blurReveal" style="font-size:clamp(15px,1.7vw,17px);max-width:420px;margin:18px 0 0;animation-delay:.4s">{{ $hero['paragraph'] }}</p>

      <div class="flex gap-3 flex-wrap animate-heroFade" style="margin-top:28px;animation-delay:.56s">
        <a href="{{ $primaryHref }}"
           class="shine bg-white text-ink font-extrabold rounded-2xl shadow-cta hover:bg-brandSoft hover:-translate-y-1 transition-all"
           style="padding:15px 28px;font-size:15px">{{ $hero['primary_btn_text'] }}</a>
        <a href="{{ $secondaryHref }}"
           class="border-[1.5px] border-white/55 text-white font-bold rounded-2xl hover:bg-white hover:text-ink hover:-translate-y-1 transition-all backdrop-blur-sm"
           style="padding:14px 26px;font-size:15px">{{ $hero['secondary_btn_text'] }}</a>
      </div>

      @if(!empty($stats))
      <div class="grid grid-cols-3 gap-2 sm:flex sm:flex-wrap animate-heroFade" style="margin-top:28px;padding-top:20px;border-top:1px solid rgba(255,255,255,.18);animation-delay:.7s">
        @foreach($stats as $i => $stat)
        <div class="{{ $i > 0 ? 'sm:border-s sm:border-white/20 sm:ps-6' : '' }} text-center sm:text-start pe-0 sm:pe-6">
          <div class="font-extrabold tracking-tight leading-none text-white" style="font-size:clamp(20px,3vw,30px)">{{ $stat['value'] }}</div>
          <div class="text-[11px] sm:text-[12px] text-white/50 mt-1.5 font-semibold leading-tight">{{ $stat['label'] }}</div>
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

{{-- ═══ DOCTORS DIRECTORY ══════════════════════════════════════════════════ --}}
@if(($directory['doctorCount'] ?? 0) > 0)
<section id="doctors" class="relative overflow-hidden bg-ink text-paper" style="padding:88px 0">

  {{-- ديكور خلفي --}}
  <div class="absolute -top-1/3 -end-[8%] w-[560px] h-[560px] pointer-events-none animate-spinSlow"
       style="background:radial-gradient(circle,rgba(22,163,74,.14),transparent 65%)"></div>
  <div class="absolute bottom-0 -start-[5%] w-[400px] h-[400px] pointer-events-none animate-glowPulse"
       style="background:radial-gradient(circle,rgba(22,163,74,.10),transparent 70%)"></div>
  <div class="absolute inset-0 opacity-[.035]"
       style="background-image:radial-gradient(circle at 1px 1px,#fff 1px,transparent 0);background-size:28px 28px"></div>

  {{-- صليب طبي زخرفي --}}
  <div class="absolute start-[5%] top-1/2 -translate-y-1/2 w-52 h-52 opacity-[.04] animate-floaty pointer-events-none" style="animation-duration:9s">
    <svg viewBox="0 0 100 100" fill="white" class="w-full h-full">
      <path d="M38 5 H62 V38 H95 V62 H62 V95 H38 V62 H5 V38 H38 Z"/>
    </svg>
  </div>

  <div class="max-w-[1180px] mx-auto px-5 relative z-10">

    {{-- رأس القسم --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 reveal">
      <div>
        <span class="text-xs font-bold tracking-[.16em] uppercase text-accent block mb-4 en">MEDICAL DIRECTORY · {{ $directory['doctorCount'] }} DOCTOR{{ $directory['doctorCount'] > 1 ? 'S' : '' }}</span>
        <h2 class="font-extrabold tracking-tight hl-line" style="font-size:clamp(28px,4.5vw,50px);line-height:1.1">
          <span>دليل</span>
          <span class="text-accent"> الأطباء</span>
          <span> المتخصصين</span>
        </h2>
        <p class="text-paper/50 text-[15px] mt-3 max-w-[440px] leading-relaxed">تواصل مباشر مع الطبيب — بدون حجز أون‑لاين، بلا وسيط</p>
      </div>
      <a href="{{ route('directory.index', 'doctor') }}"
         class="shine inline-flex items-center gap-2.5 border border-paper/25 text-paper font-bold rounded-2xl hover:bg-paper hover:text-ink transition-all whitespace-nowrap self-start md:self-auto"
         style="padding:13px 24px">
        عرض جميع الأطباء
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </a>
    </div>

    {{-- إحصاءات --}}
    <div class="flex flex-wrap gap-0 mb-10 stagger" id="doc-stats">
      <div style="padding-inline-end:28px">
        <div class="font-extrabold leading-none text-paper" style="font-size:clamp(28px,4vw,44px)" data-count="{{ $directory['doctorCount'] }}">0</div>
        <div class="text-paper/40 text-[11px] mt-2 font-semibold uppercase tracking-wider">طبيب مسجّل</div>
      </div>
      @if(($directory['specialties'] ?? 0) > 0)
      <div class="border-s border-paper/15" style="padding-inline-start:28px;padding-inline-end:28px">
        <div class="font-extrabold leading-none text-paper" style="font-size:clamp(28px,4vw,44px)" data-count="{{ $directory['specialties'] }}">0</div>
        <div class="text-paper/40 text-[11px] mt-2 font-semibold uppercase tracking-wider">تخصص</div>
      </div>
      @endif
      @if(($directory['govCount'] ?? 0) > 0)
      <div class="border-s border-paper/15" style="padding-inline-start:28px">
        <div class="font-extrabold leading-none text-paper" style="font-size:clamp(28px,4vw,44px)" data-count="{{ $directory['govCount'] }}">0</div>
        <div class="text-paper/40 text-[11px] mt-2 font-semibold uppercase tracking-wider">محافظة</div>
      </div>
      @endif
    </div>

    {{-- فلتر سريع — مستوحى من تجربة vezeeta --}}
    <div class="reveal mb-10">
      <div class="border border-paper/15 rounded-2xl p-1 flex flex-col sm:flex-row gap-1" style="background:rgba(255,255,255,.06);backdrop-filter:blur(10px)">
        {{-- بحث بالاسم --}}
        <div class="flex items-center gap-2.5 flex-1 px-4 py-3 rounded-xl" style="background:rgba(255,255,255,.08)">
          <svg class="w-4 h-4 text-paper/40 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input id="doc-home-search" type="text" placeholder="اسم الطبيب أو التخصص…"
                 class="flex-1 bg-transparent text-paper placeholder-paper/35 text-[14px] font-medium outline-none">
        </div>
        {{-- محافظة --}}
        <div class="flex items-center gap-2 px-4 py-3 rounded-xl sm:w-[160px]" style="background:rgba(255,255,255,.08)">
          <svg class="w-4 h-4 text-paper/40 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
          <select id="doc-home-gov" class="flex-1 bg-transparent text-paper text-[13px] font-medium outline-none appearance-none">
            <option value="" class="text-ink">المحافظة</option>
          </select>
        </div>
        {{-- زر بحث --}}
        <a id="doc-home-btn" href="{{ route('directory.index', 'doctor') }}"
           class="shine flex items-center justify-center gap-2 bg-accent text-white font-bold rounded-xl px-6 py-3 text-[14px] hover:bg-accentDark transition whitespace-nowrap">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          ابحث عن طبيب
        </a>
      </div>
      <p class="text-paper/28 text-[11px] mt-2 px-1">اضغط «ابحث» للوصول لدليل الأطباء الكامل مع فلاتر متقدمة</p>
    </div>

    {{-- ── موبايل: Swiper كاروسيل ────────────────────────────────────────── --}}
    <div class="swiper doctors-swiper md:hidden" style="padding-bottom:40px;overflow:visible">
      <div class="swiper-wrapper">
        @foreach($directory['featuredDoctors'] as $doc)
        @php $docThumb = $doc->getFirstMediaUrl('cover', 'thumb'); @endphp
        <div class="swiper-slide" style="width:78vw;max-width:300px">
          <a href="{{ route('directory.show', ['doctor', $doc->slug]) }}"
             class="group card-shine border border-paper/12 rounded-[22px] overflow-hidden flex flex-col h-full"
             style="background:rgba(255,255,255,.07);backdrop-filter:blur(8px)">
            <div class="relative overflow-hidden bg-paper/5" style="aspect-ratio:4/3">
              @if($doc->is_featured)<span class="absolute top-3 start-3 z-10 bg-accent text-paper text-[10px] font-bold rounded-full px-2.5 py-0.5">مميّز</span>@endif
              @if($docThumb)<img src="{{ $docThumb }}" alt="{{ $doc->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy" width="300" height="225">@else<div class="w-full h-full flex items-center justify-center text-5xl font-extrabold text-paper/10">{{ mb_substr($doc->name,0,1) }}</div>@endif
              <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,.4),transparent 55%)"></div>
            </div>
            <div class="flex flex-col gap-2 p-4 flex-1">
              <h3 class="font-extrabold text-[15px] leading-tight text-paper">{{ $doc->name }}</h3>
              @if(!empty($doc->data['specialty']))<p class="text-accent text-[12px] font-bold">{{ $doc->data['specialty'] }}</p>@endif
              @if($doc->governorate)<p class="text-paper/40 text-[11px] flex items-center gap-1 mt-auto"><svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>{{ $doc->governorate }}</p>@endif
              <div class="mt-3 pt-3 border-t border-paper/10 flex gap-2">
                @if($doc->whatsapp_url)<a href="{{ $doc->whatsapp_url }}" target="_blank" onclick="event.stopPropagation()" class="flex-1 text-center text-[11px] font-bold bg-accent text-white rounded-lg py-2 hover:bg-accentDark transition">واتساب</a>@endif
                @if($doc->phone)<a href="tel:{{ $doc->phone }}" onclick="event.stopPropagation()" class="flex-1 text-center text-[11px] font-bold border border-paper/20 text-paper rounded-lg py-2 hover:bg-paper/10 transition">اتصال</a>@endif
              </div>
            </div>
          </a>
        </div>
        @endforeach
      </div>
      <div class="swiper-pagination doctors-pagination" style="bottom:4px"></div>
    </div>

    {{-- ── ديسك: Grid 3 أعمدة ──────────────────────────────────────────── --}}
    <div class="hidden md:grid md:grid-cols-3 gap-5 stagger">
      @foreach($directory['featuredDoctors'] as $doc)
      @php $docThumb = $doc->getFirstMediaUrl('cover', 'thumb'); @endphp
      <a href="{{ route('directory.show', ['doctor', $doc->slug]) }}"
         class="group card-shine border border-paper/12 rounded-[22px] overflow-hidden flex flex-col hover:-translate-y-1.5 hover:shadow-lg2 transition-all duration-500 reveal-scale"
         style="background:rgba(255,255,255,.07);backdrop-filter:blur(8px)">
        <div class="relative overflow-hidden bg-paper/5" style="aspect-ratio:4/3">
          @if($doc->is_featured)<span class="absolute top-3 start-3 z-10 bg-accent text-paper text-[10px] font-bold rounded-full px-2.5 py-0.5">مميّز</span>@endif
          @if($docThumb)<img src="{{ $docThumb }}" alt="{{ $doc->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 animate-imgZoom" loading="lazy" width="400" height="300">@else<div class="w-full h-full flex items-center justify-center text-5xl font-extrabold text-paper/10 animate-floaty" style="animation-duration:7s">{{ mb_substr($doc->name,0,1) }}</div>@endif
          <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,.4),transparent 50%)"></div>
        </div>
        <div class="flex flex-col gap-2 p-5 flex-1">
          <h3 class="font-extrabold text-[16px] leading-tight text-paper">{{ $doc->name }}</h3>
          @if(!empty($doc->data['specialty']))<p class="text-accent text-[13px] font-bold">{{ $doc->data['specialty'] }}</p>@endif
          @if(!empty($doc->data['title']))<p class="text-paper/45 text-[12px] font-semibold">{{ $doc->data['title'] }}</p>@endif
          @if($doc->governorate)<p class="text-paper/40 text-[12px] flex items-center gap-1 mt-auto"><svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>{{ $doc->governorate }}</p>@endif
          @if($doc->rating > 0)<p class="text-paper/55 text-[12px] font-bold">{{ $doc->rating }}★</p>@endif
          <div class="mt-3 pt-3 border-t border-paper/10 flex gap-2">
            @if($doc->whatsapp_url)<a href="{{ $doc->whatsapp_url }}" target="_blank" onclick="event.stopPropagation()" class="flex-1 text-center text-[12px] font-bold bg-accent text-white rounded-xl py-2.5 hover:bg-accentDark transition">واتساب</a>@endif
            @if($doc->phone)<a href="tel:{{ $doc->phone }}" onclick="event.stopPropagation()" class="flex-1 text-center text-[12px] font-bold border border-paper/20 text-paper rounded-xl py-2.5 hover:bg-paper/10 transition">اتصال</a>@endif
          </div>
        </div>
      </a>
      @endforeach
    </div>

    {{-- CTA أسفل --}}
    <div class="mt-12 text-center reveal">
      <a href="{{ route('directory.index', 'doctor') }}"
         class="inline-flex items-center gap-3 bg-accent text-white font-bold rounded-2xl shadow-cta animate-ring hover:bg-accentDark hover:-translate-y-0.5 transition-all"
         style="padding:16px 36px;font-size:15px">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        ابحث عن طبيب في منطقتك
      </a>
    </div>

  </div>
</section>
@endif

{{-- ═══ NURSERIES DIRECTORY ════════════════════════════════════════════════ --}}
@if(($directory['nurseryCount'] ?? 0) > 0)
<section id="nurseries" class="relative overflow-hidden bg-paper2" style="padding:88px 0">

  {{-- ديكور خلفي --}}
  <div class="absolute -bottom-1/3 -end-[5%] w-[500px] h-[500px] pointer-events-none opacity-40 animate-glowPulse" style="animation-delay:1s;background:radial-gradient(circle,rgba(22,163,74,.08),transparent 65%)"></div>
  <div class="absolute top-0 inset-x-0 h-px" style="background:linear-gradient(90deg,transparent,rgba(10,10,10,.10) 30%,rgba(10,10,10,.10) 70%,transparent)"></div>

  {{-- زخرفة طائرة --}}
  <div class="absolute -start-10 top-1/3 w-40 h-40 opacity-[.05] animate-floaty pointer-events-none" style="animation-duration:10s;animation-delay:2s">
    <svg viewBox="0 0 100 100" fill="currentColor" class="text-ink w-full h-full">
      <circle cx="50" cy="50" r="45"/>
      <path d="M30 50 Q50 20 70 50 Q50 80 30 50Z" fill="white"/>
    </svg>
  </div>

  <div class="max-w-[1180px] mx-auto px-5 relative z-10">

    {{-- رأس القسم -- layout أسلوب مختلف عن الأطباء --}}
    <div class="grid lg:grid-cols-[1fr_auto] gap-8 items-end mb-12">
      <div class="blur-in">
        <span class="text-xs font-bold tracking-[.16em] uppercase text-accentDark block mb-4 en">NURSERIES DIRECTORY · {{ $directory['nurseryCount'] }} LISTED</span>
        <h2 class="font-extrabold tracking-tight" style="font-size:clamp(28px,4.5vw,50px);line-height:1.1">
          <span class="block">الحضانة</span>
          <span class="block text-accent">المثالية لطفلك</span>
        </h2>
        <p class="text-ink/52 text-[15px] mt-3 max-w-[440px] leading-relaxed">بيئات تعليمية آمنة وموثوقة — اكتشف وتواصل مباشرة</p>
      </div>
      <a href="{{ route('directory.index', 'nursery') }}"
         class="shine inline-flex items-center gap-2.5 border-[1.5px] border-ink text-ink font-bold rounded-2xl hover:bg-ink hover:text-paper transition-all whitespace-nowrap self-start"
         style="padding:13px 24px">
        عرض جميع الحضانات
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </a>
    </div>

    {{-- شبكة الحضانات: البطاقة الأولى كبيرة + اثنتان على اليمين --}}
    @php
      $nurs1 = $directory['featuredNurseries']->get(0);
      $nurs2 = $directory['featuredNurseries']->get(1);
      $nurs3 = $directory['featuredNurseries']->get(2);
    @endphp

    {{-- ── موبايل: Swiper كاروسيل ─────────────────────────────────────── --}}
    <div class="swiper nurseries-swiper md:hidden" style="padding-bottom:40px;overflow:visible">
      <div class="swiper-wrapper">
        @foreach($directory['featuredNurseries'] as $nurs)
        @php $nThumbM = $nurs->getFirstMediaUrl('cover', 'thumb'); @endphp
        <div class="swiper-slide" style="width:82vw;max-width:320px">
          <a href="{{ route('directory.show', ['nursery', $nurs->slug]) }}"
             class="group card-shine border border-line rounded-[22px] overflow-hidden flex flex-col bg-paper h-full">
            <div class="relative overflow-hidden bg-paper3" style="aspect-ratio:4/3">
              @if($nurs->is_featured)<span class="absolute top-3 start-3 z-10 bg-ink text-paper text-[10px] font-bold rounded-full px-2.5 py-0.5">مميّز</span>@endif
              @if($nThumbM)<img src="{{ $nThumbM }}" alt="{{ $nurs->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" width="320" height="240">@else<div class="w-full h-full flex items-center justify-center text-5xl font-extrabold text-ink/10">{{ mb_substr($nurs->name,0,1) }}</div>@endif
              <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,.35),transparent 55%)"></div>
              @if($nurs->governorate)<div class="absolute bottom-3 start-4 text-paper text-[11px] font-semibold">{{ $nurs->governorate }}</div>@endif
            </div>
            <div class="p-4 flex flex-col gap-1.5 flex-1">
              <h3 class="font-extrabold text-[15px] leading-tight">{{ $nurs->name }}</h3>
              @if($nurs->age_range_text)<p class="text-[12px] text-ink/55 font-semibold">{{ $nurs->age_range_text }}</p>@endif
              @if($nurs->fees_range_text)<p class="text-[12px] text-ink/50 font-medium">{{ $nurs->fees_range_text }}</p>@endif
              <div class="mt-auto pt-3 border-t border-line flex gap-2">
                @if($nurs->whatsapp_url)<a href="{{ $nurs->whatsapp_url }}" target="_blank" onclick="event.stopPropagation()" class="flex-1 text-center text-[11px] font-bold bg-accent text-white rounded-lg py-2 hover:bg-accentDark transition">واتساب</a>@endif
                @if($nurs->phone)<a href="tel:{{ $nurs->phone }}" onclick="event.stopPropagation()" class="flex-1 text-center text-[11px] font-bold border border-line rounded-lg py-2 hover:bg-paper2 transition">اتصال</a>@endif
              </div>
            </div>
          </a>
        </div>
        @endforeach
      </div>
      <div class="swiper-pagination nurseries-pagination" style="bottom:4px"></div>
    </div>

    {{-- ── ديسك: Layout 2 أعمدة ──────────────────────────────────────── --}}
    <div class="hidden md:grid md:grid-cols-[1.4fr_1fr] gap-5">

      {{-- البطاقة الكبيرة --}}
      @if($nurs1)
      @php $n1Thumb = $nurs1->getFirstMediaUrl('cover', 'large'); @endphp
      <a href="{{ route('directory.show', ['nursery', $nurs1->slug]) }}"
         class="group card-shine border border-line rounded-[24px] overflow-hidden flex flex-col bg-paper hover:-translate-y-1.5 hover:shadow-lg2 transition-all duration-500 reveal animate-heroSlideL">
        <div class="relative overflow-hidden bg-paper3" style="aspect-ratio:16/9">
          @if($nurs1->is_featured)
          <span class="absolute top-3 start-3 z-10 bg-ink text-paper text-[10px] font-bold rounded-full px-2.5 py-0.5">مميّز</span>
          @endif
          @if($n1Thumb)
          <img src="{{ $n1Thumb }}" alt="{{ $nurs1->name }}"
               class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 animate-imgZoom"
               loading="lazy" width="800" height="450">
          @else
          <div class="w-full h-full flex items-center justify-center text-7xl font-extrabold text-ink/10">
            {{ mb_substr($nurs1->name, 0, 1) }}
          </div>
          @endif
          <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,.45) 0%,transparent 55%)"></div>
          @if($nurs1->governorate)
          <div class="absolute bottom-4 start-5 text-paper text-[12px] font-semibold flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
            {{ $nurs1->governorate }}
          </div>
          @endif
        </div>
        <div class="p-6 flex flex-col gap-2 flex-1">
          <h3 class="font-extrabold text-[20px] leading-tight tracking-tight">{{ $nurs1->name }}</h3>
          @if($nurs1->name_en)<p class="text-ink/40 text-[13px] en font-medium">{{ $nurs1->name_en }}</p>@endif
          @if($nurs1->summary)<p class="text-ink/60 text-[14px] leading-relaxed mt-1">{{ Str::limit($nurs1->summary, 100) }}</p>@endif
          <div class="flex flex-wrap gap-3 mt-3">
            @if($nurs1->age_range_text)
            <span class="bg-paper2 border border-line text-[12px] font-bold rounded-lg px-3 py-1.5">{{ $nurs1->age_range_text }}</span>
            @endif
            @if($nurs1->fees_range_text)
            <span class="bg-paper2 border border-line text-[12px] font-bold rounded-lg px-3 py-1.5">{{ $nurs1->fees_range_text }}</span>
            @endif
          </div>
          <div class="mt-auto pt-4 border-t border-line flex gap-2">
            @if($nurs1->whatsapp_url)
            <span onclick="event.preventDefault();window.open('{{ $nurs1->whatsapp_url }}','_blank')"
                  class="flex-1 text-center text-[13px] font-bold bg-accent text-white rounded-xl py-2.5 cursor-pointer hover:bg-accentDark transition">واتساب</span>
            @endif
            @if($nurs1->phone)
            <span onclick="event.preventDefault();window.location.href='tel:{{ $nurs1->phone }}'"
                  class="flex-1 text-center text-[13px] font-bold border border-line rounded-xl py-2.5 cursor-pointer hover:bg-paper2 transition">اتصال</span>
            @endif
          </div>
        </div>
      </a>
      @endif

      {{-- بطاقتان صغيرتان مكدّستان --}}
      <div class="flex flex-col gap-5">
        @foreach([$nurs2, $nurs3] as $nurs)
        @if($nurs)
        @php $nThumb = $nurs->getFirstMediaUrl('cover', 'thumb'); @endphp
        <a href="{{ route('directory.show', ['nursery', $nurs->slug]) }}"
           class="group card-shine border border-line rounded-[20px] overflow-hidden flex bg-paper hover:-translate-y-1 hover:shadow-lg2 transition-all duration-500 reveal-scale animate-heroSlideR">
          <div class="relative overflow-hidden bg-paper3 shrink-0" style="width:130px">
            @if($nThumb)
            <img src="{{ $nThumb }}" alt="{{ $nurs->name }}"
                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                 loading="lazy" width="130" height="130">
            @else
            <div class="w-full h-full flex items-center justify-center text-3xl font-extrabold text-ink/15">
              {{ mb_substr($nurs->name, 0, 1) }}
            </div>
            @endif
          </div>
          <div class="flex flex-col gap-1.5 p-4 flex-1 min-w-0">
            <h3 class="font-extrabold text-[15px] leading-tight">{{ $nurs->name }}</h3>
            @if($nurs->governorate)
            <p class="text-ink/45 text-[12px] flex items-center gap-1">
              <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
              {{ $nurs->governorate }}
            </p>
            @endif
            @if($nurs->age_range_text)
            <p class="text-[12px] text-ink/55 font-semibold">{{ $nurs->age_range_text }}</p>
            @endif
            @if($nurs->fees_range_text)
            <p class="text-[12px] text-ink/55 font-medium">{{ $nurs->fees_range_text }}</p>
            @endif
            @if($nurs->rating > 0)
            <p class="text-[12px] text-ink/45 font-bold mt-auto">{{ $nurs->rating }}★</p>
            @endif
          </div>
        </a>
        @endif
        @endforeach

        {{-- CTA اكتشف المزيد --}}
        <a href="{{ route('directory.index', 'nursery') }}"
           class="reveal group border-2 border-dashed border-line rounded-[20px] p-5 flex items-center justify-center gap-3 font-bold text-[14px] text-ink/52 hover:border-ink hover:text-ink hover:bg-paper2 transition-all duration-400" style="min-height:80px">
          <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          اكتشف المزيد من الحضانات
        </a>
      </div>

    </div>
  </div>
</section>
@endif

{{-- ═══ المساعد الذكي CTA ══════════════════════════════════════════════════ --}}
@if(config('ai.enabled', true))
<section class="bg-paper py-16 reveal-scale" id="ai-assistant">
  <div class="max-w-[1180px] mx-auto px-5">
    <div class="rounded-2xl bg-ink text-paper overflow-hidden relative" style="padding:52px 40px">
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 start-0 w-72 h-72 rounded-full animate-spinSlow opacity-10"
             style="background:conic-gradient(from 0deg,transparent 80%,rgba(22,163,74,.6) 100%);filter:blur(50px);transform-origin:center"></div>
        <div class="absolute bottom-0 end-0 w-56 h-56 rounded-full animate-glowPulse"
             style="background:radial-gradient(circle,rgba(22,163,74,.1),transparent 70%)"></div>
      </div>
      <div class="relative z-10 flex flex-col md:flex-row items-center gap-8 text-center md:text-start">
        <div class="w-16 h-16 rounded-2xl bg-accent/20 flex-shrink-0 flex items-center justify-center animate-ring">
          <svg class="w-8 h-8 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            <circle cx="12" cy="10" r="1" fill="currentColor"/><circle cx="8" cy="10" r="1" fill="currentColor"/><circle cx="16" cy="10" r="1" fill="currentColor"/>
          </svg>
        </div>
        <div class="flex-1">
          <p class="text-accent font-black tracking-[.2em] text-xs mb-2">AI ASSISTANT · مدعوم بـ Gemini</p>
          <h2 class="font-extrabold text-[clamp(20px,3vw,28px)] leading-tight mb-2">اسأل المساعد الذكي</h2>
          <p class="text-white/60 text-sm max-w-md">رشّح لي منتجاً، قارن بين اثنين، اسأل عن السعر — المساعد يقرأ بيانات المتجر الحقيقية ويجيبك فوراً.</p>
        </div>
        <a href="{{ route('assistant.page') }}"
           class="shine flex-shrink-0 inline-flex items-center gap-2.5 bg-accent text-white font-extrabold rounded-xl px-7 py-3.5 hover:bg-accentDark transition shadow-cta text-[15px]">
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
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.documentElement.classList.add('js');

// ── Swiper كاروسيل الأطباء (موبايل فقط) ───────────────────────────────────
if (window.innerWidth < 768) {
  new Swiper('.doctors-swiper', {
    slidesPerView: 'auto',
    spaceBetween: 14,
    freeMode: true,
    grabCursor: true,
    pagination: { el: '.doctors-pagination', clickable: true, dynamicBullets: true },
    rtl: true,
  });
}

// ── Swiper كاروسيل الحضانات (موبايل فقط) ──────────────────────────────────
if (window.innerWidth < 768) {
  new Swiper('.nurseries-swiper', {
    slidesPerView: 'auto',
    spaceBetween: 14,
    freeMode: true,
    grabCursor: true,
    pagination: { el: '.nurseries-pagination', clickable: true, dynamicBullets: true },
    rtl: true,
  });
}

// ── وصل الفلتر بصفحة الدليل (صفحة الرئيسة فقط) ─────────────────────────────
(function () {
  const docSearch = document.getElementById('doc-home-search');
  const docGov    = document.getElementById('doc-home-gov');
  const docBtn    = document.getElementById('doc-home-btn');

  // تحميل المحافظات من API
  fetch('/api/v1/directory/doctor?category=all')
    .then(r => r.json())
    .then(({ data }) => {
      const govs = [...new Set(data.map(d => d.governorate).filter(Boolean))].sort();
      govs.forEach(g => {
        const opt = document.createElement('option');
        opt.value = g;
        opt.textContent = g;
        opt.className = 'text-ink';
        docGov && docGov.appendChild(opt);
      });
    })
    .catch(() => {});

  // تحديث رابط البحث ديناميكياً
  function updateDocBtn() {
    if (!docBtn) return;
    const params = new URLSearchParams();
    const s = docSearch ? docSearch.value.trim() : '';
    const g = docGov ? docGov.value : '';
    if (s) params.set('search', s);
    if (g) params.set('governorate', g);
    docBtn.href = '/directory/doctor' + (params.toString() ? '?' + params : '');
  }

  if (docSearch) docSearch.addEventListener('input', updateDocBtn);
  if (docGov)    docGov.addEventListener('change', updateDocBtn);

  // Enter key on search
  if (docSearch) docSearch.addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); updateDocBtn(); docBtn && docBtn.click(); }
  });
})();

// ── Swiper CSS للـ dots بألوان التصميم ────────────────────────────────────
const swiperStyle = document.createElement('style');
swiperStyle.textContent = `.swiper-pagination-bullet{background:rgba(11,29,54,.25)}.swiper-pagination-bullet-active{background:#0B1D36}.dir-doctors .swiper-pagination-bullet-active{background:#E85D04}`;
document.head.appendChild(swiperStyle);

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

// ── عدّادات تصاعدية للدليل ─────────────────────────────────────────────────
function animateCounter(el) {
  const target = parseInt(el.dataset.count, 10) || 0;
  if (!target) return;
  let val = 0;
  const step = Math.max(1, Math.ceil(target / 50));
  const t = setInterval(() => {
    val = Math.min(val + step, target);
    el.textContent = val.toLocaleString('ar-EG');
    if (val >= target) clearInterval(t);
  }, 28);
}

['doc-stats'].forEach(id => {
  const sec = document.getElementById(id);
  if (!sec) return;
  const cntObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.querySelectorAll('[data-count]').forEach(animateCounter);
        cntObs.unobserve(e.target);
      }
    });
  }, { threshold: .4 });
  cntObs.observe(sec);
});

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
