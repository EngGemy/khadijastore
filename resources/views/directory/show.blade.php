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
@include('partials.header')

@php
  $cover = $listing->getFirstMediaUrl('cover', 'large');
  $data  = $listing->data ?? [];
  $isDoctor = $type === 'doctor';
  $related = $related ?? collect();
@endphp

{{-- Breadcrumb --}}
<div class="sticky top-[72px] z-30 bg-paper/90 backdrop-blur-xl border-b border-line">
  <div class="max-w-[1180px] mx-auto px-5 h-12 flex items-center gap-3 text-[13px] font-semibold">
    <a href="{{ route('home') }}" class="text-ink/40 hover:text-ink transition hidden sm:inline">الرئيسية</a>
    <span class="text-ink/20 hidden sm:inline">/</span>
    <a href="{{ route('directory.index', $type) }}"
       class="flex items-center gap-2 text-ink/55 hover:text-brand transition">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
      {{ $isDoctor ? 'دليل الأطباء' : 'دليل الحضانات' }}
    </a>
    <span class="text-ink/20">/</span>
    <span class="text-ink truncate">{{ $listing->name }}</span>
  </div>
</div>

@if($isDoctor)
{{-- ═══════════════════════ صفحة الطبيب ═══════════════════════ --}}
<section class="bg-paper2">
  <div class="max-w-[1180px] mx-auto px-5 py-10 lg:py-14">
    <div class="grid lg:grid-cols-[1fr_360px] gap-8 items-start">

      <div class="flex flex-col gap-6">
        <div class="bg-paper border border-line rounded-[24px] overflow-hidden shadow-[0_8px_30px_-18px_rgba(11,29,54,.12)]">
          <div class="grid md:grid-cols-[280px_1fr]">
            <div class="relative aspect-square md:aspect-auto md:min-h-[280px] bg-paper3 overflow-hidden">
              @if($cover)
              <img src="{{ $cover }}" alt="{{ $listing->name }}"
                   class="w-full h-full object-cover" width="600" height="600">
              @else
              <div class="w-full h-full flex items-center justify-center text-7xl font-extrabold text-ink/12">
                {{ mb_substr($listing->name, 0, 1) }}
              </div>
              @endif
              @if($listing->is_featured)
              <span class="absolute top-4 start-4 bg-brand text-white text-[11px] font-bold rounded-full px-3 py-1">مميّز</span>
              @endif
            </div>

            <div class="p-6 md:p-8 flex flex-col gap-3">
              @if($listing->serviceCategory)
              <span class="self-start bg-paper2 border border-line text-[11px] font-bold rounded-full px-3 py-1 text-ink/55">
                {{ $listing->serviceCategory->name }}
              </span>
              @endif

              <h1 class="font-extrabold tracking-tight text-ink" style="font-size:clamp(24px,3.5vw,36px);line-height:1.15">
                @if(!empty($data['title']))<span class="text-ink/50 text-[0.72em] font-bold block mb-1">{{ $data['title'] }}</span>@endif
                {{ $listing->name }}
              </h1>

              @if($listing->name_en)
              <p class="text-ink/35 text-[14px] font-medium en">{{ $listing->name_en }}</p>
              @endif

              @if(!empty($data['specialty']))
              <p class="text-[16px] font-bold text-brand">
                {{ $data['specialty'] }}
                @if(!empty($data['specialty_en']))
                <span class="en font-semibold text-ink/45 text-[14px]"> · {{ $data['specialty_en'] }}</span>
                @endif
              </p>
              @endif

              @if($listing->rating > 0)
              <p class="text-[13px] font-bold text-ink/55">{{ $listing->rating }}★ تقييم</p>
              @endif

              @if($listing->summary)
              <p class="text-ink/65 text-[14px] leading-relaxed mt-1">{{ $listing->summary }}</p>
              @endif
            </div>
          </div>
        </div>

        {{-- حقائق --}}
        <div class="grid sm:grid-cols-2 gap-3">
          @if(!empty($data['clinic_name']))
          <div class="bg-paper border border-line rounded-2xl p-4">
            <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">العيادة</div>
            <div class="font-bold text-[14px]">{{ $data['clinic_name'] }}</div>
          </div>
          @endif
          @if(!empty($data['experience_years']))
          <div class="bg-paper border border-line rounded-2xl p-4">
            <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">الخبرة</div>
            <div class="font-bold text-[14px]">{{ $data['experience_years'] }} سنة</div>
          </div>
          @endif
          @if(!empty($data['consultation_fee']))
          <div class="bg-paper border border-line rounded-2xl p-4">
            <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">الكشف (تقريبي)</div>
            <div class="font-bold text-[14px]">{{ number_format($data['consultation_fee']) }} ج.م</div>
            <div class="text-[10px] text-ink/40 mt-0.5">عرض توضيحي — لا حجز أونلاين</div>
          </div>
          @endif
          @if(!empty($data['working_hours']))
          <div class="bg-paper border border-line rounded-2xl p-4">
            <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">المواعيد</div>
            <div class="font-bold text-[14px]">{{ $data['working_hours'] }}</div>
          </div>
          @endif
        </div>

        @if($listing->address)
        <div class="bg-paper border border-line rounded-2xl p-5 flex items-start gap-3">
          <div class="w-10 h-10 rounded-xl bg-brandSoft text-brand grid place-items-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <div>
            <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">العنوان</div>
            <p class="text-[14px] text-ink/75 font-semibold">{{ $listing->address }}</p>
            @if($listing->governorate)<p class="text-[12px] text-ink/45 mt-0.5">{{ $listing->governorate }}</p>@endif
          </div>
        </div>
        @endif

        @if(!empty($data['languages']))
        <div class="bg-paper border border-line rounded-2xl p-5">
          <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-3">اللغات</div>
          <div class="flex flex-wrap gap-2">
            @foreach((array)$data['languages'] as $lang)
            <span class="bg-paper2 border border-line text-[12px] font-semibold rounded-lg px-3 py-1.5">{{ $lang }}</span>
            @endforeach
          </div>
        </div>
        @endif

        @if(!empty($data['services']))
        <div class="bg-paper border border-line rounded-2xl p-5">
          <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-3">الخدمات</div>
          <div class="flex flex-wrap gap-2">
            @foreach((array)$data['services'] as $svc)
            <span class="bg-ink text-paper text-[12px] font-semibold rounded-lg px-3 py-1.5">{{ $svc }}</span>
            @endforeach
          </div>
        </div>
        @endif

        @if($listing->description)
        <div class="bg-paper border border-line rounded-2xl p-6 md:p-8">
          <h2 class="font-extrabold text-lg mb-4 text-ink">نبذة تفصيلية</h2>
          <div class="text-ink/70 leading-relaxed prose prose-ink max-w-none">{!! $listing->description !!}</div>
        </div>
        @endif
      </div>

      {{-- Sidebar تواصل --}}
      <aside class="lg:sticky lg:top-28">
        <div class="bg-paper border border-line rounded-[24px] p-6 flex flex-col gap-3 shadow-[0_12px_40px_-20px_rgba(11,29,54,.18)]">
          <h3 class="font-extrabold text-lg text-ink mb-1">تواصل مباشر</h3>
          <p class="text-[12px] text-ink/45 -mt-1 mb-2">بدون حجز أونلاين — تواصل فوري</p>

          @if($listing->whatsapp_url)
          <a href="{{ $listing->whatsapp_url }}" target="_blank" rel="noopener"
             class="flex items-center justify-center gap-2 font-bold rounded-2xl py-3.5 text-[15px] text-white hover:-translate-y-0.5 transition-all shadow-cta"
             style="background:#25D366">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
            واتساب
          </a>
          @endif

          @if($listing->phone)
          <a href="tel:{{ $listing->phone }}"
             class="flex items-center justify-center gap-2 border-2 border-ink text-ink font-bold rounded-2xl hover:bg-ink hover:text-paper transition-all py-3.5 text-[15px]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            اتصال
          </a>
          @endif

          @if($listing->map_url)
          <a href="{{ $listing->map_url }}" target="_blank" rel="noopener"
             class="flex items-center justify-center gap-2 border border-line text-ink/70 font-semibold rounded-2xl hover:border-brand hover:text-brand transition-all py-3 text-[14px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
            فتح في الخريطة
          </a>
          @endif

          @if($listing->email)
          <a href="mailto:{{ $listing->email }}"
             class="flex items-center justify-center gap-2 border border-line text-ink/70 font-semibold rounded-2xl hover:border-ink hover:text-ink transition-all py-3 text-[13px]">
            {{ $listing->email }}
          </a>
          @endif
        </div>
      </aside>
    </div>
  </div>
</section>

@else
{{-- ═══════════════════════ صفحة الحضانة ═══════════════════════ --}}
@php
  $programs    = (array) ($data['programs'] ?? []);
  $programsEn  = (array) ($data['programs_en'] ?? []);
  $facilities  = (array) ($data['facilities'] ?? []);
@endphp

<section class="relative bg-ink overflow-hidden" style="height:min(56vh,480px)">
  @if(!empty($gallery))
  <div id="nursery-gallery" class="absolute inset-0">
    @foreach($gallery as $i => $img)
    <img src="{{ $img['url'] }}"
         alt="{{ $listing->name }} — صورة {{ $i + 1 }}"
         class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}"
         data-slide="{{ $i }}"
         loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
         width="1000" height="600">
    @endforeach
    @if(count($gallery) > 1)
    <button id="prev-slide" class="absolute start-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-paper/20 backdrop-blur text-paper flex items-center justify-center hover:bg-paper/40 transition" aria-label="السابق">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button id="next-slide" class="absolute end-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-paper/20 backdrop-blur text-paper flex items-center justify-center hover:bg-paper/40 transition" aria-label="التالي">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
    <div class="absolute bottom-4 inset-x-0 flex justify-center gap-2 z-10">
      @foreach($gallery as $i => $img)
      <button class="slide-dot w-2 h-2 rounded-full transition-all {{ $i === 0 ? 'bg-brand w-5' : 'bg-paper/40' }}" data-idx="{{ $i }}" aria-label="انتقل للصورة {{ $i + 1 }}"></button>
      @endforeach
    </div>
    @endif
  </div>
  @elseif($cover)
  <img src="{{ $cover }}" alt="{{ $listing->name }}" class="absolute inset-0 w-full h-full object-cover" width="1000" height="600">
  @endif
  <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/40 to-transparent"></div>

  <div class="absolute bottom-0 inset-x-0 p-6 sm:p-8 z-10">
    <div class="max-w-[1180px] mx-auto">
      @if($listing->serviceCategory)
      <span class="inline-block bg-white/12 border border-white/20 text-[11px] font-bold rounded-full mb-3 text-paper px-3 py-1">{{ $listing->serviceCategory->name }}</span>
      @endif
      <h1 class="font-extrabold text-paper tracking-tight" style="font-size:clamp(26px,4.5vw,48px);line-height:1.1">{{ $listing->name }}</h1>
      @if($listing->name_en)
      <p class="text-paper/50 mt-1 font-medium en text-[14px]">{{ $listing->name_en }}</p>
      @endif
    </div>
  </div>
</section>

<main class="bg-paper2">
  <div class="max-w-[1180px] mx-auto px-5 py-10 lg:py-12">
    <div class="grid lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 flex flex-col gap-5">
        @if($listing->summary)
        <p class="text-[15px] text-ink/70 leading-relaxed bg-paper border border-line rounded-2xl p-5">{{ $listing->summary }}</p>
        @endif

        <div class="grid sm:grid-cols-2 gap-3">
          @if($listing->age_range_text)
          <div class="bg-paper border border-line rounded-2xl p-5">
            <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">الفئة العمرية</div>
            <div class="font-extrabold text-[18px]">{{ $listing->age_range_text }}</div>
          </div>
          @endif
          @if($listing->fees_range_text)
          <div class="bg-paper border border-line rounded-2xl p-5">
            <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">الرسوم الشهرية</div>
            <div class="font-extrabold text-[18px]">{{ $listing->fees_range_text }}</div>
          </div>
          @endif
          @if(!empty($data['capacity']))
          <div class="bg-paper border border-line rounded-2xl p-5">
            <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">الطاقة الاستيعابية</div>
            <div class="font-extrabold text-[18px]">{{ $data['capacity'] }} طفل</div>
          </div>
          @endif
          @if(!empty($data['working_hours']))
          <div class="bg-paper border border-line rounded-2xl p-5">
            <div class="text-[11px] font-bold text-ink/40 uppercase tracking-wider mb-1">مواعيد العمل</div>
            <div class="font-bold text-[15px]">{{ $data['working_hours'] }}</div>
            @if(!empty($data['working_days']))
            <div class="text-[12px] text-ink/50 mt-0.5">{{ $data['working_days'] }}</div>
            @endif
          </div>
          @endif
        </div>

        @if(!empty($programs))
        <div class="bg-paper border border-line rounded-2xl p-5">
          <h2 class="font-extrabold text-lg mb-3">البرامج التعليمية</h2>
          <div class="flex flex-wrap gap-2">
            @foreach($programs as $i => $prog)
            <span class="bg-ink text-paper text-[13px] font-semibold rounded-xl px-4 py-2">
              {{ $prog }}
              @if(isset($programsEn[$i]))<span class="en text-paper/50 text-[11px] ms-1">· {{ $programsEn[$i] }}</span>@endif
            </span>
            @endforeach
          </div>
        </div>
        @endif

        @if(!empty($facilities))
        <div class="bg-paper border border-line rounded-2xl p-5">
          <h2 class="font-extrabold text-lg mb-3">المرافق والخدمات</h2>
          <div class="grid sm:grid-cols-2 gap-3">
            @foreach($facilities as $fac)
            <div class="flex items-center gap-3 bg-paper2 border border-line rounded-xl p-3">
              <div class="w-8 h-8 rounded-lg bg-brand text-white grid place-items-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              </div>
              <span class="text-[14px] font-semibold">{{ $fac }}</span>
            </div>
            @endforeach
          </div>
        </div>
        @endif

        @if($listing->description)
        <div class="bg-paper border border-line rounded-2xl p-6">
          <h2 class="font-extrabold text-lg mb-4">نبذة تفصيلية</h2>
          <div class="text-ink/70 leading-relaxed">{!! $listing->description !!}</div>
        </div>
        @endif
      </div>

      <aside class="flex flex-col gap-4">
        <div class="bg-paper border border-line rounded-[24px] p-6 sticky top-28 flex flex-col gap-3 shadow-[0_12px_40px_-20px_rgba(11,29,54,.18)]">
          <h3 class="font-extrabold text-lg">تواصل معنا</h3>

          @if($listing->whatsapp_url)
          <a href="{{ $listing->whatsapp_url }}" target="_blank" rel="noopener"
             class="flex items-center justify-center gap-2 font-bold rounded-2xl py-3.5 text-[15px] text-white hover:-translate-y-0.5 transition-all"
             style="background:#25D366">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
            واتساب
          </a>
          @endif

          @if($listing->phone)
          <a href="tel:{{ $listing->phone }}"
             class="flex items-center justify-center gap-2 border-2 border-ink text-ink font-bold rounded-2xl hover:bg-ink hover:text-paper transition-all py-3.5 text-[15px]">
            {{ $listing->phone }}
          </a>
          @endif

          @if($listing->email)
          <a href="mailto:{{ $listing->email }}"
             class="flex items-center justify-center gap-2 border border-line text-ink/70 font-semibold rounded-2xl hover:border-ink hover:text-ink transition-all py-3 text-[13px]">
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
             class="flex items-center justify-center gap-2 border border-line text-ink/70 font-semibold rounded-2xl hover:border-brand hover:text-brand transition-all py-3 text-[14px]">
            فتح في الخريطة
          </a>
          @endif
        </div>
      </aside>
    </div>
  </div>
</main>
@endif

{{-- Related --}}
@if($related->isNotEmpty())
<section class="bg-paper border-t border-line py-12">
  <div class="max-w-[1180px] mx-auto px-5">
    <div class="flex items-end justify-between gap-4 mb-7">
      <div>
        <span class="text-[11px] font-black tracking-[.14em] uppercase text-brand block mb-2 en">RELATED</span>
        <h2 class="font-extrabold text-[22px] text-ink">
          {{ $isDoctor ? 'أطباء ذوو صلة' : 'حضانات مشابهة' }}
        </h2>
      </div>
      <a href="{{ route('directory.index', $type) }}"
         class="text-[13px] font-bold text-brand hover:text-accentDark transition whitespace-nowrap">
        عرض الكل
      </a>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
      @foreach($related as $item)
        @include('directory._card', ['listing' => $item, 'type' => $type])
      @endforeach
    </div>
  </div>
</section>
@endif

@include('partials.footer')

@push('scripts')
<script>
document.documentElement.classList.add('js');
const io = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
  });
}, { threshold: .12, rootMargin: '0px 0px -40px 0px' });
document.querySelectorAll('.reveal, .reveal-scale, .stagger, .blur-in').forEach(el => io.observe(el));

@if(!$isDoctor && count($gallery) > 1)
(function() {
  const slides = document.querySelectorAll('#nursery-gallery img[data-slide]');
  const dots   = document.querySelectorAll('.slide-dot');
  let current  = 0;
  let auto;

  function goTo(idx) {
    slides[current].classList.replace('opacity-100', 'opacity-0');
    dots[current].classList.remove('bg-brand', 'w-5');
    dots[current].classList.add('bg-paper/40', 'w-2');
    current = (idx + slides.length) % slides.length;
    slides[current].classList.replace('opacity-0', 'opacity-100');
    dots[current].classList.add('bg-brand', 'w-5');
    dots[current].classList.remove('bg-paper/40', 'w-2');
  }

  function resetAuto() { clearInterval(auto); auto = setInterval(() => goTo(current + 1), 4000); }

  document.getElementById('prev-slide')?.addEventListener('click', () => { goTo(current - 1); resetAuto(); });
  document.getElementById('next-slide')?.addEventListener('click', () => { goTo(current + 1); resetAuto(); });
  dots.forEach(d => d.addEventListener('click', () => { goTo(parseInt(d.dataset.idx)); resetAuto(); }));
  resetAuto();
})();
@endif
</script>
<style>
.dir-card{
  border:1px solid rgba(11,29,54,.08);border-radius:20px;overflow:hidden;background:#fff;
  box-shadow:0 4px 18px rgba(11,29,54,.05);
  transition:transform .4s cubic-bezier(.16,1,.3,1),box-shadow .4s ease,border-color .3s ease;
}
.dir-card:hover{
  transform:translateY(-6px);
  box-shadow:0 20px 48px -16px rgba(11,29,54,.14);
  border-color:rgba(232,93,4,.28);
}
</style>
@endpush
@endsection
