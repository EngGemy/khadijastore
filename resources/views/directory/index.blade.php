@extends('layouts.app')
@section('title', $seo['title'] ?? ($typeLabel . ' · متجر العلامات'))

@section('meta')
<meta name="description" content="{{ $seo['description'] ?? '' }}">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $seo['title'] ?? $typeLabel }}">
<meta property="og:description" content="{{ $seo['description'] ?? '' }}">
<meta property="og:url" content="{{ url()->current() }}">
@endsection

@section('content')

@include('partials.strip')

{{-- ═══ NAV ═══════════════════════════════════════════════════════════════ --}}
@include('partials.header')

{{-- ═══ HERO ════════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-ink text-paper" style="padding:72px 0 56px">
  <div class="absolute -top-1/3 -end-[8%] w-[500px] h-[500px] animate-spinSlow" style="background:radial-gradient(circle,rgba(22,163,74,.15),transparent 65%)"></div>
  <div class="absolute inset-0 opacity-[.04]" style="background-image:radial-gradient(circle at 1px 1px,#fff 1px,transparent 0);background-size:28px 28px"></div>

  <div class="max-w-[1180px] mx-auto px-5 relative z-10">
    {{-- عنوان متحرك --}}
    <div class="mb-3">
      <span class="text-xs font-bold tracking-[.14em] uppercase text-accent/80 block mb-4 en">DIRECTORY · {{ strtoupper($type) }}</span>
    </div>
    <h1 class="font-extrabold tracking-tight hl-line" style="font-size:clamp(32px,6vw,60px);line-height:1.1">
      <span>{{ $type === 'doctor' ? 'دليل' : 'دليل' }}</span>
      <span class="ms-3 text-accent">{{ $type === 'doctor' ? 'الأطباء' : 'الحضانات' }}</span>
    </h1>
    <p class="text-paper/60 text-[15px] mt-4 max-w-[500px] leading-relaxed blur-in">
      {{ $type === 'doctor'
          ? 'تواصل مباشر مع أفضل الأطباء في منطقتك — بدون حجز أون‑لاين مسبق'
          : 'اكتشف الحضانات المعتمدة لطفلك — معلومات مفصّلة وتواصل فوري' }}
    </p>

    {{-- عدّادات --}}
    <div class="flex gap-10 mt-8 stagger" id="dir-stats">
      <div>
        <div class="font-extrabold text-[28px] leading-none" data-count="{{ $stats['count'] }}">0</div>
        <div class="text-paper/45 text-xs mt-1 font-semibold">{{ $type === 'doctor' ? 'طبيب مسجّل' : 'حضانة مسجّلة' }}</div>
      </div>
      <div>
        <div class="font-extrabold text-[28px] leading-none" data-count="{{ count($stats['governorates']) }}">0</div>
        <div class="text-paper/45 text-xs mt-1 font-semibold">محافظة</div>
      </div>
      @if($type === 'doctor' && isset($stats['specialties']))
      <div>
        <div class="font-extrabold text-[28px] leading-none" data-count="{{ count($stats['specialties']) }}">0</div>
        <div class="text-paper/45 text-xs mt-1 font-semibold">تخصص</div>
      </div>
      @endif
    </div>
  </div>
</section>

{{-- ═══ FILTERS ═════════════════════════════════════════════════════════════ --}}
<section class="bg-paper2 border-b border-line sticky top-[70px] z-30">
  <div class="max-w-[1180px] mx-auto px-5 py-4 flex flex-wrap gap-3 items-center">
    {{-- البحث --}}
    <div class="relative flex-1 min-w-[200px]">
      <svg class="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink/40 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      <input id="dir-search" type="search" placeholder="{{ $type === 'doctor' ? 'ابحث عن طبيب أو تخصص…' : 'ابحث عن حضانة…' }}"
             value="{{ request('search') }}"
             class="w-full bg-paper border border-line rounded-xl ps-9 pe-4 py-2.5 text-[14px] focus:outline-none focus:ring-2 focus:ring-ink/20 transition">
    </div>
    {{-- قسم --}}
    @if($categories->isNotEmpty())
    <select id="dir-category" class="bg-paper border border-line rounded-xl px-4 py-2.5 text-[14px] focus:outline-none focus:ring-2 focus:ring-ink/20 transition">
      <option value="">كل الأقسام</option>
      @foreach($categories as $cat)
      <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
      @endforeach
    </select>
    @endif
    {{-- محافظة --}}
    @if($stats['governorates']->isNotEmpty())
    <select id="dir-gov" class="bg-paper border border-line rounded-xl px-4 py-2.5 text-[14px] focus:outline-none focus:ring-2 focus:ring-ink/20 transition">
      <option value="">كل المحافظات</option>
      @foreach($stats['governorates'] as $gov)
      <option value="{{ $gov }}" {{ request('governorate') == $gov ? 'selected' : '' }}>{{ $gov }}</option>
      @endforeach
    </select>
    @endif
  </div>
</section>

{{-- ═══ GRID ════════════════════════════════════════════════════════════════ --}}
<main class="max-w-[1180px] mx-auto px-5 py-14">

  {{-- شبكة مباشرة (SSR) --}}
  <div id="dir-grid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 stagger">
    @forelse($listings as $listing)
    @include('directory._card', ['listing' => $listing, 'type' => $type])
    @empty
    <div class="col-span-3 text-center py-20 text-ink/40">
      <svg class="w-12 h-12 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <p class="text-[15px] font-semibold">لا توجد نتائج بالفلاتر الحالية</p>
    </div>
    @endforelse
  </div>

  {{-- Pagination --}}
  @if($listings->hasPages())
  <div class="mt-10 flex justify-center">
    {{ $listings->links() }}
  </div>
  @endif
</main>

@push('scripts')
<script>
document.documentElement.classList.add('js');

// ── كشف التمرير ────────────────────────────────────────────────────────────
const io = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
  });
}, { threshold: .12, rootMargin: '0px 0px -40px 0px' });
document.querySelectorAll('.reveal, .reveal-scale, .stagger, .blur-in').forEach(el => io.observe(el));

// ── عدّادات تصاعدية ────────────────────────────────────────────────────────
function animateCount(el) {
  const target = parseInt(el.dataset.count, 10) || 0;
  if (!target) return;
  let start = 0;
  const step = Math.ceil(target / 40);
  const timer = setInterval(() => {
    start = Math.min(start + step, target);
    el.textContent = start.toLocaleString('ar-EG');
    if (start >= target) clearInterval(timer);
  }, 30);
}
const statsSection = document.getElementById('dir-stats');
if (statsSection) {
  const statsObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.querySelectorAll('[data-count]').forEach(animateCount);
        statsObs.unobserve(e.target);
      }
    });
  }, { threshold: .3 });
  statsObs.observe(statsSection);
}

// ── فلترة حية (fetch → إعادة رسم الشبكة) ─────────────────────────────────
const grid    = document.getElementById('dir-grid');
const search  = document.getElementById('dir-search');
const catSel  = document.getElementById('dir-category');
const govSel  = document.getElementById('dir-gov');
const apiBase = '{{ route("directory.api", $type) }}';
const showUrl = '{{ route("directory.show", [$type, "__SLUG__"]) }}';
let debounce;

function cardHtml(l) {
  const featBadge = l.is_featured
    ? `<span class="absolute top-3 start-3 z-10 bg-ink text-paper text-[10px] font-bold rounded-full px-2 py-0.5">مميّز</span>`
    : '';
  const cover = l.cover_thumb
    ? `<img src="${l.cover_thumb}" alt="${l.name}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 card-img" loading="lazy" width="400" height="400">`
    : `<div class="w-full h-full bg-paper3 flex items-center justify-center text-4xl font-extrabold text-ink/20">${l.name.charAt(0)}</div>`;
  const sub = l.specialty || l.age_range || '';
  const ratingStr = l.rating > 0 ? `<span class="text-xs font-bold text-ink/60">${l.rating}★</span>` : '';
  const href = showUrl.replace('__SLUG__', l.slug);

  return `
  <a href="${href}" class="group card-shine border border-line rounded-[20px] overflow-hidden bg-paper hover:-translate-y-1.5 hover:shadow-lg2 transition-all duration-500 flex flex-col reveal">
    <div class="relative aspect-[4/3] overflow-hidden bg-paper3">${featBadge}${cover}</div>
    <div class="flex flex-col gap-2 p-5 flex-1">
      <div class="flex items-start justify-between gap-2">
        <h3 class="font-extrabold text-[17px] leading-tight tracking-tight">${l.name}</h3>
        ${ratingStr}
      </div>
      ${sub ? `<p class="text-[13px] text-ink/55 font-semibold">${sub}</p>` : ''}
      ${l.governorate ? `<p class="text-[12px] text-ink/40 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>${l.governorate}</p>` : ''}
      <div class="mt-auto pt-3 border-t border-line flex gap-2">
        ${l.whatsapp_url ? `<a href="${l.whatsapp_url}" target="_blank" onclick="event.stopPropagation()" class="flex-1 text-center text-[12px] font-bold bg-accent text-white rounded-lg py-2 hover:bg-accentDark transition">واتساب</a>` : ''}
        ${l.phone ? `<a href="tel:${l.phone}" onclick="event.stopPropagation()" class="flex-1 text-center text-[12px] font-bold border border-line rounded-lg py-2 hover:bg-paper2 transition">اتصال</a>` : ''}
      </div>
    </div>
  </a>`;
}

function doFilter() {
  const params = new URLSearchParams();
  if (search && search.value.trim())  params.set('search', search.value.trim());
  if (catSel && catSel.value)         params.set('category', catSel.value);
  if (govSel && govSel.value)         params.set('governorate', govSel.value);

  // تحديث URL بدون reload (progressive enhancement)
  const newUrl = params.toString() ? `?${params}` : window.location.pathname;
  history.replaceState(null, '', newUrl);

  fetch(`${apiBase}?${params}`)
    .then(r => r.json())
    .then(({ data }) => {
      if (!data.length) {
        grid.innerHTML = `<div class="col-span-3 text-center py-20 text-ink/40 text-[15px] font-semibold">لا توجد نتائج</div>`;
        return;
      }
      grid.innerHTML = data.map(cardHtml).join('');
      // إعادة تفعيل المراقب على البطاقات الجديدة
      grid.querySelectorAll('.reveal').forEach(el => {
        el.classList.add('in'); // ظهور فوري بعد fetch
      });
    })
    .catch(() => {}); // تراجع صامت — الصفحة تعمل بالـ query-string
}

if (search)  search.addEventListener('input', () => { clearTimeout(debounce); debounce = setTimeout(doFilter, 380); });
if (catSel)  catSel.addEventListener('change', doFilter);
if (govSel)  govSel.addEventListener('change', doFilter);
</script>
@endpush
@endsection
