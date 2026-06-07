@extends('layouts.app')
@section('title', $seo['title'] ?? ($listing->name . ' · متجر العلامات'))

@section('meta')
@if(!empty($seo['description']))<meta name="description" content="{{ $seo['description'] }}">@endif
<link rel="canonical" href="{{ $seo['url'] ?? url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $seo['title'] ?? $listing->name }}">
<meta property="og:description" content="{{ $seo['description'] ?? '' }}">
<meta property="og:url" content="{{ $seo['url'] ?? url()->current() }}">
@if(!empty($seo['image']))<meta property="og:image" content="{{ $seo['image'] }}">@endif
<script type="application/ld+json">{!! $jsonLd !!}</script>
@endsection

@section('content')

@include('partials.strip')

{{-- ═══ NAV ═══════════════════════════════════════════════════════════════ --}}
@include('partials.header')

{{-- Breadcrumb --}}
<div class="sticky top-[70px] z-30 bg-paper/85 backdrop-blur-2xl border-b border-line">
  <div class="max-w-[1180px] mx-auto px-5 h-12 flex items-center gap-3">
    <a href="{{ route('directory.index', $type) }}"
       class="flex items-center gap-2 text-[13px] font-semibold text-ink/55 hover:text-ink transition">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
      {{ $type === 'doctor' ? 'دليل الأطباء' : 'دليل الحضانات' }}
    </a>
    <span class="text-ink/20">/</span>
    <span class="text-[13px] font-semibold truncate">{{ $listing->name }}</span>
  </div>
</div>

@if($type === 'doctor')
{{-- ════════════════════════════════════════════════════════════════════════ --}}
{{-- ────────────────  صفحة الطبيب  ─────────────────────────────────────── --}}
{{-- ════════════════════════════════════════════════════════════════════════ --}}
@php
  $cover = $listing->getFirstMediaUrl('cover', 'large');
  $data  = $listing->data ?? [];
@endphp

<section class="max-w-[1180px] mx-auto px-5 py-14">
  <div class="grid lg:grid-cols-2 gap-12 items-start">

    {{-- نص — heroSlideL --}}
    <div class="animate-heroSlideL" style="animation-delay:.05s">
      @if($listing->serviceCategory)
      <span class="inline-block bg-paper3 border border-line text-xs font-bold rounded-full mb-4 tracking-wide" style="padding:5px 14px">
        {{ $listing->serviceCategory->name }}
      </span>
      @endif

      <h1 class="font-extrabold tracking-tight hl-line" style="font-size:clamp(26px,4vw,44px);line-height:1.15">
        <span>{{ $data['title'] ?? '' }}</span>
        @if($data['title'] ?? false)<span> </span>@endif
        <span>{{ $listing->name }}</span>
      </h1>

      @if($listing->name_en)
      <p class="text-ink/40 text-[15px] font-medium mt-1 en">{{ $listing->name_en }}</p>
      @endif

      @if($data['specialty'] ?? false)
      <p class="text-[17px] font-bold text-accentDark mt-3">{{ $data['specialty'] }}@if($data['specialty_en'] ?? false) · <span class="en font-semibold text-ink/55">{{ $data['specialty_en'] }}</span>@endif</p>
      @endif

      @if($listing->summary)
      <p class="text-ink/65 text-[15px] mt-4 leading-relaxed blur-in">{{ $listing->summary }}</p>
      @endif

      {{-- معلومات شريطية --}}
      <div class="grid grid-cols-2 gap-3 mt-7 stagger">
        @if($data['clinic_name'] ?? false)
        <div class="bg-paper2 rounded-xl p-4 border border-line">
          <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">العيادة</div>
          <div class="font-bold text-[14px]">{{ $data['clinic_name'] }}</div>
        </div>
        @endif
        @if($data['experience_years'] ?? false)
        <div class="bg-paper2 rounded-xl p-4 border border-line">
          <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">الخبرة</div>
          <div class="font-bold text-[14px]">{{ $data['experience_years'] }} سنة</div>
        </div>
        @endif
        @if($data['consultation_fee'] ?? false)
        <div class="bg-paper2 rounded-xl p-4 border border-line">
          <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">الكشف (تقريبي)</div>
          <div class="font-bold text-[14px]">{{ number_format($data['consultation_fee']) }} ج.م</div>
          <div class="text-[10px] text-ink/40 mt-0.5">عرض توضيحي فقط — لا حجز</div>
        </div>
        @endif
        @if($data['working_hours'] ?? false)
        <div class="bg-paper2 rounded-xl p-4 border border-line">
          <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">المواعيد</div>
          <div class="font-bold text-[14px]">{{ $data['working_hours'] }}</div>
        </div>
        @endif
      </div>

      {{-- العنوان --}}
      @if($listing->address)
      <div class="mt-5 flex items-start gap-2 reveal">
        <svg class="w-4 h-4 text-ink/40 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <div>
          <p class="text-[14px] text-ink/70">{{ $listing->address }}</p>
          @if($listing->governorate)<p class="text-[12px] text-ink/45 mt-0.5">{{ $listing->governorate }}</p>@endif
        </div>
      </div>
      @endif

      {{-- اللغات --}}
      @if(!empty($data['languages']))
      <div class="mt-5 reveal">
        <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-2">اللغات</div>
        <div class="flex flex-wrap gap-2">
          @foreach((array)$data['languages'] as $lang)
          <span class="bg-paper3 border border-line text-[12px] font-semibold rounded-lg px-3 py-1">{{ $lang }}</span>
          @endforeach
        </div>
      </div>
      @endif

      {{-- الخدمات --}}
      @if(!empty($data['services']))
      <div class="mt-5 reveal">
        <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-2">الخدمات</div>
        <div class="flex flex-wrap gap-2 stagger">
          @foreach((array)$data['services'] as $svc)
          <span class="bg-ink text-paper text-[12px] font-semibold rounded-lg px-3 py-1">{{ $svc }}</span>
          @endforeach
        </div>
      </div>
      @endif

      {{-- أزرار تواصل --}}
      <div class="flex flex-wrap gap-3 mt-8 reveal">
        @if($listing->whatsapp_url)
        <a href="{{ $listing->whatsapp_url }}" target="_blank"
           class="inline-flex items-center gap-2 bg-accent text-white font-bold rounded-2xl shadow-cta animate-ring hover:bg-accentDark hover:-translate-y-0.5 transition-all"
           style="padding:14px 26px">
          <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
          واتساب
        </a>
        @endif
        @if($listing->phone)
        <a href="tel:{{ $listing->phone }}"
           class="inline-flex items-center gap-2 border-2 border-ink text-ink font-bold rounded-2xl hover:bg-ink hover:text-paper transition-all"
           style="padding:14px 26px">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
          اتصال
        </a>
        @endif
        @if($listing->map_url)
        <a href="{{ $listing->map_url }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 border border-line text-ink/70 font-semibold rounded-2xl hover:border-ink hover:text-ink transition-all"
           style="padding:14px 26px">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
          الخريطة
        </a>
        @endif
      </div>
    </div>

    {{-- صورة — heroImg --}}
    <div class="hero-3d animate-heroImg" style="animation-delay:.2s">
      <div class="card-3d rounded-[24px] overflow-hidden animate-glowPulse" style="aspect-ratio:1">
        @if($cover)
        <img src="{{ $cover }}" alt="{{ $listing->name }}"
             class="card-img w-full h-full object-cover animate-imgZoom"
             width="600" height="600">
        @else
        <div class="w-full h-full bg-paper3 flex items-center justify-center text-7xl font-extrabold text-ink/15">
          {{ mb_substr($listing->name, 0, 1) }}
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- وصف كامل --}}
  @if($listing->description)
  <div class="mt-14 reveal prose prose-ink max-w-none border-t border-line pt-10">
    <h2 class="font-extrabold text-xl mb-4">نبذة تفصيلية</h2>
    <div class="text-ink/70 leading-relaxed">{!! $listing->description !!}</div>
  </div>
  @endif

</section>

@else
{{-- ════════════════════════════════════════════════════════════════════════ --}}
{{-- ────────────────  صفحة الحضانة  ────────────────────────────────────── --}}
{{-- ════════════════════════════════════════════════════════════════════════ --}}
@php
  $cover   = $listing->getFirstMediaUrl('cover', 'large');
  $data    = $listing->data ?? [];
  $programs    = (array) ($data['programs'] ?? []);
  $programsEn  = (array) ($data['programs_en'] ?? []);
  $facilities  = (array) ($data['facilities'] ?? []);
@endphp

{{-- Hero معرض صور سينمائي --}}
<section class="relative bg-ink overflow-hidden" style="height:min(60vh,520px)">
  @if(!empty($gallery))
  <div id="nursery-gallery" class="absolute inset-0">
    @foreach($gallery as $i => $img)
    <img src="{{ $img['url'] }}"
         alt="{{ $listing->name }} — صورة {{ $i + 1 }}"
         class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 animate-imgZoom {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}"
         data-slide="{{ $i }}"
         loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
         width="1000" height="600">
    @endforeach
    {{-- تحكّم المعرض --}}
    @if(count($gallery) > 1)
    <button id="prev-slide"
            class="absolute start-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-paper/20 backdrop-blur text-paper flex items-center justify-center hover:bg-paper/40 transition"
            aria-label="السابق">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button id="next-slide"
            class="absolute end-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-paper/20 backdrop-blur text-paper flex items-center justify-center hover:bg-paper/40 transition"
            aria-label="التالي">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
    {{-- مصغّرات --}}
    <div class="absolute bottom-4 inset-x-0 flex justify-center gap-2 z-10">
      @foreach($gallery as $i => $img)
      <button class="slide-dot w-2 h-2 rounded-full transition-all {{ $i === 0 ? 'bg-paper w-5' : 'bg-paper/40' }}"
              data-idx="{{ $i }}" aria-label="انتقل للصورة {{ $i + 1 }}"></button>
      @endforeach
    </div>
    @endif
  </div>
  @elseif($cover)
  <img src="{{ $cover }}" alt="{{ $listing->name }}"
       class="absolute inset-0 w-full h-full object-cover animate-imgZoom"
       width="1000" height="600">
  @endif
  <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-ink/20 to-transparent"></div>

  <div class="absolute bottom-0 inset-x-0 p-8 z-10">
    <div class="max-w-[1180px] mx-auto">
      @if($listing->serviceCategory)
      <span class="inline-block bg-white/15 border border-white/20 text-xs font-bold rounded-full mb-3 text-paper tracking-wide" style="padding:4px 12px">{{ $listing->serviceCategory->name }}</span>
      @endif
      <h1 class="font-extrabold text-paper tracking-tight blur-in" style="font-size:clamp(28px,5vw,52px);line-height:1.1">
        {{ $listing->name }}
      </h1>
      @if($listing->name_en)
      <p class="text-paper/50 mt-1 font-medium en blur-in" style="animation-delay:.1s">{{ $listing->name_en }}</p>
      @endif
    </div>
  </div>
</section>

<main class="max-w-[1180px] mx-auto px-5 py-12">
  <div class="grid lg:grid-cols-3 gap-10">

    {{-- تفاصيل + تواصل --}}
    <div class="lg:col-span-2 flex flex-col gap-8">

      @if($listing->summary)
      <p class="text-[16px] text-ink/70 leading-relaxed reveal">{{ $listing->summary }}</p>
      @endif

      {{-- حقائق الحضانة --}}
      <div class="grid sm:grid-cols-2 gap-4 stagger">
        @if($listing->age_range_text)
        <div class="bg-paper2 border border-line rounded-2xl p-5">
          <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">الفئة العمرية</div>
          <div class="font-extrabold text-[20px]">{{ $listing->age_range_text }}</div>
        </div>
        @endif
        @if($listing->fees_range_text)
        <div class="bg-paper2 border border-line rounded-2xl p-5">
          <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">الرسوم الشهرية</div>
          <div class="font-extrabold text-[20px]">{{ $listing->fees_range_text }}</div>
        </div>
        @endif
        @if($data['capacity'] ?? false)
        <div class="bg-paper2 border border-line rounded-2xl p-5">
          <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">الطاقة الاستيعابية</div>
          <div class="font-extrabold text-[20px]">{{ $data['capacity'] }} طفل</div>
        </div>
        @endif
        @if($data['working_hours'] ?? false)
        <div class="bg-paper2 border border-line rounded-2xl p-5">
          <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">مواعيد العمل</div>
          <div class="font-bold text-[16px]">{{ $data['working_hours'] }}</div>
          @if($data['working_days'] ?? false)
          <div class="text-[12px] text-ink/50 mt-0.5">{{ $data['working_days'] }}</div>
          @endif
        </div>
        @endif
      </div>

      {{-- البرامج --}}
      @if(!empty($programs))
      <div class="reveal">
        <h2 class="font-extrabold text-lg mb-3">البرامج التعليمية</h2>
        <div class="flex flex-wrap gap-2 stagger">
          @foreach($programs as $i => $prog)
          <span class="bg-ink text-paper text-[13px] font-semibold rounded-xl px-4 py-2">
            {{ $prog }}
            @if(isset($programsEn[$i]))<span class="en text-paper/50 text-[11px] ms-1">· {{ $programsEn[$i] }}</span>@endif
          </span>
          @endforeach
        </div>
      </div>
      @endif

      {{-- المرافق --}}
      @if(!empty($facilities))
      <div class="reveal">
        <h2 class="font-extrabold text-lg mb-3">المرافق والخدمات</h2>
        <div class="grid sm:grid-cols-2 gap-3 stagger">
          @foreach($facilities as $fac)
          <div class="flex items-center gap-3 bg-paper2 border border-line rounded-xl p-3">
            <div class="w-8 h-8 rounded-lg bg-ink text-paper grid place-items-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
            <span class="text-[14px] font-semibold">{{ $fac }}</span>
          </div>
          @endforeach
        </div>
      </div>
      @endif

      {{-- وصف كامل --}}
      @if($listing->description)
      <div class="reveal border-t border-line pt-8">
        <h2 class="font-extrabold text-lg mb-4">نبذة تفصيلية</h2>
        <div class="text-ink/70 leading-relaxed">{!! $listing->description !!}</div>
      </div>
      @endif

    </div>

    {{-- Sidebar التواصل --}}
    <div class="flex flex-col gap-4">
      <div class="bg-paper2 border border-line rounded-2xl p-6 sticky top-24 flex flex-col gap-4 reveal">
        <h3 class="font-extrabold text-lg">تواصل معنا</h3>

        @if($listing->whatsapp_url)
        <a href="{{ $listing->whatsapp_url }}" target="_blank"
           class="flex items-center justify-center gap-2 bg-accent text-white font-bold rounded-2xl shadow-cta animate-ring hover:bg-accentDark hover:-translate-y-0.5 transition-all py-3.5 text-[15px]">
          <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
          تواصل عبر واتساب
        </a>
        @endif

        @if($listing->phone)
        <a href="tel:{{ $listing->phone }}"
           class="flex items-center justify-center gap-2 border-2 border-ink text-ink font-bold rounded-2xl hover:bg-ink hover:text-paper transition-all py-3.5 text-[15px]">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
          {{ $listing->phone }}
        </a>
        @endif

        @if($listing->email)
        <a href="mailto:{{ $listing->email }}"
           class="flex items-center justify-center gap-2 border border-line text-ink/70 font-semibold rounded-2xl hover:border-ink hover:text-ink transition-all py-3 text-[14px]">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          {{ $listing->email }}
        </a>
        @endif

        @if($listing->address)
        <div class="text-[13px] text-ink/55 pt-2 border-t border-line flex items-start gap-2">
          <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          <div>
            <p>{{ $listing->address }}</p>
            @if($listing->governorate)<p class="text-ink/40 mt-0.5">{{ $listing->governorate }}</p>@endif
          </div>
        </div>
        @endif

        @if($listing->map_url)
        <a href="{{ $listing->map_url }}" target="_blank" rel="noopener"
           class="flex items-center justify-center gap-2 border border-line text-ink/70 font-semibold rounded-2xl hover:border-ink hover:text-ink transition-all py-3 text-[14px]">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
          فتح في الخريطة
        </a>
        @endif
      </div>
    </div>

  </div>
</main>
@endif

@push('scripts')
<script>
document.documentElement.classList.add('js');

// كشف التمرير
const io = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
  });
}, { threshold: .12, rootMargin: '0px 0px -40px 0px' });
document.querySelectorAll('.reveal, .reveal-scale, .stagger, .blur-in').forEach(el => io.observe(el));

// معرض صور الحضانة
@if($type === 'nursery' && count($gallery) > 1)
(function() {
  const slides = document.querySelectorAll('#nursery-gallery img[data-slide]');
  const dots   = document.querySelectorAll('.slide-dot');
  let current  = 0;
  let auto;

  function goTo(idx) {
    slides[current].classList.replace('opacity-100', 'opacity-0');
    dots[current].classList.remove('bg-paper', 'w-5');
    dots[current].classList.add('bg-paper/40', 'w-2');
    current = (idx + slides.length) % slides.length;
    slides[current].classList.replace('opacity-0', 'opacity-100');
    dots[current].classList.add('bg-paper', 'w-5');
    dots[current].classList.remove('bg-paper/40', 'w-2');
  }

  function resetAuto() { clearInterval(auto); auto = setInterval(() => goTo(current + 1), 4000); }

  const prev = document.getElementById('prev-slide');
  const next = document.getElementById('next-slide');
  if (prev) prev.addEventListener('click', () => { goTo(current - 1); resetAuto(); });
  if (next) next.addEventListener('click', () => { goTo(current + 1); resetAuto(); });
  dots.forEach(d => d.addEventListener('click', () => { goTo(parseInt(d.dataset.idx)); resetAuto(); }));

  resetAuto();
})();
@endif
</script>
@endpush
@endsection
