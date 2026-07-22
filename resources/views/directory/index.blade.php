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
@include('partials.header')

@php
  $isDoctor = $type === 'doctor';
  $activeFilters = collect([
    request('search'),
    request('category'),
    request('governorate'),
    request('specialty'),
  ])->filter()->isNotEmpty();
@endphp

{{-- ═══ HERO ════════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-ink text-paper" style="padding:64px 0 48px">
  <div class="absolute -top-1/3 -end-[8%] w-[520px] h-[520px] pointer-events-none"
       style="background:radial-gradient(circle,rgba(249,115,22,.18),transparent 65%)"></div>
  <div class="absolute bottom-0 -start-[6%] w-[380px] h-[380px] pointer-events-none"
       style="background:radial-gradient(circle,rgba(232,93,4,.12),transparent 70%)"></div>
  <div class="absolute inset-0 opacity-[.04]"
       style="background-image:radial-gradient(circle at 1px 1px,#fff 1px,transparent 0);background-size:28px 28px"></div>

  <div class="max-w-[1180px] mx-auto px-5 relative z-10">
    <nav class="flex items-center gap-2 text-[12px] font-semibold text-paper/45 mb-6">
      <a href="{{ route('home') }}" class="hover:text-brand transition">الرئيسية</a>
      <span class="text-paper/25">/</span>
      <span class="text-paper/80">{{ $isDoctor ? 'دليل الأطباء' : 'دليل الحضانات' }}</span>
    </nav>

    <span class="text-[11px] font-black tracking-[.16em] uppercase text-brand block mb-3 en">
      {{ $isDoctor ? 'MEDICAL DIRECTORY' : 'NURSERIES DIRECTORY' }}
    </span>
    <h1 class="font-extrabold tracking-tight" style="font-size:clamp(30px,5.5vw,54px);line-height:1.12">
      <span>{{ $isDoctor ? 'دليل' : 'دليل' }}</span>
      <span class="text-brand ms-2">{{ $isDoctor ? 'الأطباء' : 'الحضانات' }}</span>
    </h1>
    <p class="text-paper/65 text-[15px] mt-4 max-w-[520px] leading-relaxed">
      {{ $isDoctor
          ? 'ابحث بالتخصص أو المحافظة وتواصل مباشرة مع الطبيب — بدون حجز أون‑لاين.'
          : 'اكتشف الحضانات المناسبة لطفلك بالمنطقة والعمر، مع تواصل فوري.' }}
    </p>

    <div class="flex flex-wrap gap-8 mt-8" id="dir-stats">
      <div>
        <div class="font-extrabold text-[28px] leading-none text-paper" data-count="{{ $stats['count'] }}">0</div>
        <div class="text-paper/45 text-[11px] mt-1.5 font-semibold">{{ $isDoctor ? 'طبيب مسجّل' : 'حضانة مسجّلة' }}</div>
      </div>
      <div>
        <div class="font-extrabold text-[28px] leading-none text-paper" data-count="{{ count($stats['governorates']) }}">0</div>
        <div class="text-paper/45 text-[11px] mt-1.5 font-semibold">محافظة</div>
      </div>
      @if($isDoctor && isset($stats['specialties']))
      <div>
        <div class="font-extrabold text-[28px] leading-none text-paper" data-count="{{ count($stats['specialties']) }}">0</div>
        <div class="text-paper/45 text-[11px] mt-1.5 font-semibold">تخصص</div>
      </div>
      @endif
    </div>
  </div>
</section>

{{-- ═══ FILTERS ═════════════════════════════════════════════════════════════ --}}
<section class="bg-paper border-b border-line sticky top-[72px] z-30 shadow-[0_8px_24px_-16px_rgba(11,29,54,.12)]">
  <div class="max-w-[1180px] mx-auto px-5 py-4 flex flex-col gap-3">
    <div class="flex flex-wrap gap-3 items-center">
      <div class="relative flex-1 min-w-[220px]">
        <svg class="absolute start-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-ink/35 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input id="dir-search" type="search"
               placeholder="{{ $isDoctor ? 'ابحث بالاسم أو التخصص…' : 'ابحث باسم الحضانة أو المنطقة…' }}"
               value="{{ request('search') }}"
               class="w-full bg-paper2 border border-line rounded-xl ps-10 pe-4 py-2.5 text-[14px] font-medium focus:outline-none focus:ring-2 focus:ring-brand/25 focus:border-brand/40 transition">
      </div>

      @if($categories->isNotEmpty())
      <select id="dir-category" class="bg-paper2 border border-line rounded-xl px-4 py-2.5 text-[14px] font-semibold focus:outline-none focus:ring-2 focus:ring-brand/25 transition min-w-[140px]">
        <option value="">كل الأقسام</option>
        @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
      </select>
      @endif

      @if($stats['governorates']->isNotEmpty())
      <select id="dir-gov" class="bg-paper2 border border-line rounded-xl px-4 py-2.5 text-[14px] font-semibold focus:outline-none focus:ring-2 focus:ring-brand/25 transition min-w-[140px]">
        <option value="">كل المحافظات</option>
        @foreach($stats['governorates'] as $gov)
        <option value="{{ $gov }}" {{ request('governorate') == $gov ? 'selected' : '' }}>{{ $gov }}</option>
        @endforeach
      </select>
      @endif

      @if($isDoctor && ($stats['specialties'] ?? collect())->isNotEmpty())
      <select id="dir-specialty" class="bg-paper2 border border-line rounded-xl px-4 py-2.5 text-[14px] font-semibold focus:outline-none focus:ring-2 focus:ring-brand/25 transition min-w-[150px]">
        <option value="">كل التخصصات</option>
        @foreach($stats['specialties'] as $spec)
        <option value="{{ $spec }}" {{ request('specialty') == $spec ? 'selected' : '' }}>{{ $spec }}</option>
        @endforeach
      </select>
      @endif

      @if($activeFilters)
      <button type="button" id="dir-clear"
              class="text-[13px] font-bold text-ink/45 hover:text-brand transition px-2 py-2">
        مسح الفلاتر
      </button>
      @endif
    </div>

    <div class="flex items-center justify-between gap-3 text-[12px] font-semibold text-ink/40">
      <span id="dir-result-count">{{ $listings->total() }} نتيجة</span>
      <a href="{{ route('home') }}" class="hover:text-ink transition hidden sm:inline">العودة للمتجر</a>
    </div>
  </div>
</section>

{{-- ═══ GRID ════════════════════════════════════════════════════════════════ --}}
<main class="bg-paper2 min-h-[50vh]">
  <div class="max-w-[1180px] mx-auto px-5 py-12">
    <div id="dir-grid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 stagger">
      @forelse($listings as $listing)
        @include('directory._card', ['listing' => $listing, 'type' => $type])
      @empty
        <div class="col-span-full">
          @include('directory._empty', ['type' => $type])
        </div>
      @endforelse
    </div>

    @if($listings->hasPages())
    <div class="mt-10 flex justify-center">
      {{ $listings->links() }}
    </div>
    @endif
  </div>
</main>

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

function animateCount(el) {
  const target = parseInt(el.dataset.count, 10) || 0;
  if (!target) { el.textContent = '0'; return; }
  let start = 0;
  const step = Math.max(1, Math.ceil(target / 40));
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

const grid    = document.getElementById('dir-grid');
const search  = document.getElementById('dir-search');
const catSel  = document.getElementById('dir-category');
const govSel  = document.getElementById('dir-gov');
const specSel = document.getElementById('dir-specialty');
const clearBtn = document.getElementById('dir-clear');
const countEl = document.getElementById('dir-result-count');
const apiBase = @json(route('directory.api', $type));
const showUrl = @json(route('directory.show', [$type, '__SLUG__']));
const typeLabel = @json($isDoctor ? 'أطباء' : 'حضانات');
let debounce;

function emptyHtml() {
  return `<div class="col-span-full text-center py-16 px-6 rounded-[24px] border border-dashed border-line bg-paper">
    <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-ink/5 grid place-items-center text-ink/30">
      <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    <p class="text-[16px] font-extrabold text-ink mb-1">لا توجد نتائج</p>
    <p class="text-[13px] text-ink/45 font-medium max-w-sm mx-auto">جرّب تغيير كلمات البحث أو المحافظة أو التخصص.</p>
  </div>`;
}

function cardHtml(l) {
  const featBadge = l.is_featured
    ? `<span class="absolute top-3 start-3 z-10 bg-ink text-paper text-[10px] font-bold rounded-full px-2.5 py-0.5 shadow-sm">مميّز</span>`
    : '';
  const cover = l.cover_thumb
    ? `<img src="${l.cover_thumb}" alt="${l.name}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy" width="400" height="300">`
    : `<div class="w-full h-full bg-gradient-to-br from-paper2 to-paper3 flex items-center justify-center text-4xl font-extrabold text-ink/15">${l.name.charAt(0)}</div>`;
  const sub = l.specialty || l.age_range || '';
  const ratingStr = l.rating > 0
    ? `<span class="inline-flex items-center gap-0.5 text-[11px] font-bold text-brand bg-brandSoft rounded-full px-2 py-0.5 shrink-0">${l.rating}★</span>`
    : '';
  const href = showUrl.replace('__SLUG__', l.slug);
  const fees = l.fees_range ? `<p class="text-[12px] text-ink/50 font-medium">${l.fees_range}</p>` : '';

  return `
  <a href="${href}" class="group dir-card flex flex-col reveal">
    <div class="relative aspect-[4/3] overflow-hidden bg-paper3">${featBadge}${cover}
      <div class="absolute inset-0 pointer-events-none" style="background:linear-gradient(to top,rgba(11,29,54,.35),transparent 50%)"></div>
    </div>
    <div class="flex flex-col gap-2 p-5 flex-1">
      <div class="flex items-start justify-between gap-2">
        <h3 class="font-extrabold text-[16px] leading-tight tracking-tight text-ink group-hover:text-brand transition-colors">${l.name}</h3>
        ${ratingStr}
      </div>
      ${sub ? `<p class="text-[13px] text-brand font-bold">${sub}</p>` : ''}
      ${l.governorate ? `<p class="text-[12px] text-ink/40 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>${l.governorate}</p>` : ''}
      ${fees}
      <div class="mt-auto pt-3 border-t border-line flex gap-2">
        ${l.whatsapp_url ? `<span data-href="${l.whatsapp_url}" class="dir-cta-wa flex-1 text-center text-[12px] font-bold rounded-xl py-2.5 transition">واتساب</span>` : ''}
        ${l.phone ? `<span data-tel="${l.phone}" class="dir-cta-tel flex-1 text-center text-[12px] font-bold border border-line rounded-xl py-2.5 hover:bg-paper2 transition">اتصال</span>` : ''}
        <span class="flex-1 text-center text-[12px] font-bold border border-ink/15 text-ink rounded-xl py-2.5 group-hover:bg-ink group-hover:text-paper transition">التفاصيل</span>
      </div>
    </div>
  </a>`;
}

function currentParams() {
  const params = new URLSearchParams();
  if (search && search.value.trim()) params.set('search', search.value.trim());
  if (catSel && catSel.value) params.set('category', catSel.value);
  if (govSel && govSel.value) params.set('governorate', govSel.value);
  if (specSel && specSel.value) params.set('specialty', specSel.value);
  return params;
}

function doFilter() {
  const params = currentParams();
  const newUrl = params.toString() ? `?${params}` : window.location.pathname;
  history.replaceState(null, '', newUrl);

  fetch(`${apiBase}?${params}`)
    .then(r => r.json())
    .then(({ data }) => {
      if (countEl) countEl.textContent = `${data.length} نتيجة`;
      if (!data.length) {
        grid.innerHTML = emptyHtml();
        return;
      }
      grid.innerHTML = data.map(cardHtml).join('');
      grid.querySelectorAll('.reveal').forEach(el => el.classList.add('in'));
    })
    .catch(() => {});
}

if (search)  search.addEventListener('input', () => { clearTimeout(debounce); debounce = setTimeout(doFilter, 380); });
if (catSel)  catSel.addEventListener('change', doFilter);
if (govSel)  govSel.addEventListener('change', doFilter);
if (specSel) specSel.addEventListener('change', doFilter);
if (clearBtn) clearBtn.addEventListener('click', () => {
  if (search) search.value = '';
  if (catSel) catSel.value = '';
  if (govSel) govSel.value = '';
  if (specSel) specSel.value = '';
  doFilter();
});

grid?.addEventListener('click', (e) => {
  const wa = e.target.closest('.dir-cta-wa');
  if (wa?.dataset.href) { e.preventDefault(); e.stopPropagation(); window.open(wa.dataset.href, '_blank'); return; }
  const tel = e.target.closest('.dir-cta-tel');
  if (tel?.dataset.tel) { e.preventDefault(); e.stopPropagation(); window.location.href = 'tel:' + tel.dataset.tel; }
});
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
.dir-cta-wa{background:#25D366;color:#fff}
.dir-cta-wa:hover{filter:brightness(.95)}
</style>
@endpush
@endsection
