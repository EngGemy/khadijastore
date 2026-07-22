{{-- ═══ SHARED HEADER — Souqi-style ═══════════════════════════════════ --}}
@php
  $navBrands = $navBrands ?? collect();
  $navDirectory = $navDirectory ?? nav_directory_counts();
  $doctorsUrl = nav_home_section_url('doctors', route('directory.index', 'doctor'));
  $nurseriesUrl = nav_home_section_url('nurseries', route('directory.index', 'nursery'));
  $brandsUrl = route('brands.index');
  $productsUrl = route('products.index');
  $searchAction = route('products.index');
@endphp
<header id="hdr" class="sticky top-0 z-40 bg-white/92 backdrop-blur-xl border-b border-transparent transition-all duration-300">
  <div class="max-w-[1180px] mx-auto px-4 sm:px-5 h-[72px] flex items-center gap-3 sm:gap-4">
    <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-extrabold tracking-tight shrink-0 min-w-0 max-w-[36%] sm:max-w-none">
      @include('partials.store-logo', [
        'showName' => !($storeLogo ?? store_logo_url()),
        'imgClass' => 'h-9 w-auto max-w-[120px] max-h-9 object-contain object-center rounded-md shrink-0',
        'fallbackClass' => 'w-9 h-9 rounded-xl bg-ink text-paper grid place-items-center font-extrabold text-sm shrink-0',
      ])
    </a>

    {{-- Search — Souqi style --}}
    <form action="{{ $searchAction }}" method="GET" class="souqi-search hidden md:flex flex-1 mx-2" role="search">
      <svg class="w-4 h-4 text-ink/35 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      <input type="search" name="q" value="{{ request('q') }}" placeholder="ابحث عن منتج أو براند…" autocomplete="off" aria-label="بحث">
      <button type="submit" class="text-[12px] font-extrabold text-ink/55 hover:text-ink transition shrink-0">بحث</button>
    </form>

    <nav class="hidden xl:flex items-center gap-0.5 text-[13px] font-bold text-ink/50 shrink-0">
      <a href="{{ route('home') }}" class="px-3 py-2 rounded-full hover:bg-paper2 hover:text-ink transition">الرئيسية</a>
      <a href="{{ $brandsUrl }}" class="px-3 py-2 rounded-full hover:bg-paper2 hover:text-ink transition">البراندات</a>
      <a href="{{ $productsUrl }}" class="px-3 py-2 rounded-full hover:bg-paper2 hover:text-ink transition">المنتجات</a>
      <a href="{{ $doctorsUrl }}" class="px-3 py-2 rounded-full hover:bg-paper2 hover:text-ink transition">الأطباء</a>
      <a href="{{ $nurseriesUrl }}" class="px-3 py-2 rounded-full hover:bg-paper2 hover:text-ink transition">الحضانات</a>
    </nav>

    <div class="flex items-center gap-1.5 ms-auto shrink-0">
      {{-- Mobile search shortcut --}}
      <a href="{{ $productsUrl }}" class="hdr-icon md:hidden" aria-label="بحث المنتجات" title="بحث">
        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      </a>

      @if($storeSupportWhatsapp ?? false)
      <a href="https://wa.me/{{ preg_replace('/\D/', '', $storeSupportWhatsapp) }}" target="_blank" rel="noopener"
         class="hdr-icon" aria-label="تواصل واتساب" title="واتساب">
        <svg class="w-[18px] h-[18px] fill-current text-[#25D366]" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
      </a>
      @endif

      {{-- Account / cart: no storefront auth or cart routes — omit gracefully --}}

      <button id="mob-btn" class="xl:hidden hdr-icon" aria-label="القائمة">
        <span class="flex flex-col items-center justify-center gap-[5px] w-5">
          <span id="hb1" class="block w-5 h-[2px] bg-ink rounded-full transition-all duration-300"></span>
          <span id="hb2" class="block w-5 h-[2px] bg-ink rounded-full transition-all duration-300"></span>
          <span id="hb3" class="block w-3 h-[2px] bg-ink rounded-full ms-auto transition-all duration-300" style="margin-inline-end:0;margin-inline-start:auto;width:12px"></span>
        </span>
      </button>
    </div>
  </div>
</header>

{{-- ═══ MOBILE MENU ═══════════════════════════════════════════════════════ --}}
<div id="mob-menu" class="mob-menu" aria-hidden="true">
  <div id="mob-bg" class="mob-menu__bg"></div>
  <div id="mob-panel" class="mob-menu__panel shadow-lg2">
    <div class="flex items-center justify-between px-5 h-[72px] border-b border-line shrink-0">
      <div class="flex items-center gap-2.5 font-extrabold tracking-tight min-w-0">
        @include('partials.store-logo', ['showName' => !($storeLogo ?? store_logo_url()), 'imgClass' => 'h-8 w-auto max-w-[110px] object-contain rounded-md shrink-0', 'fallbackClass' => 'w-8 h-8 rounded-lg bg-ink text-paper grid place-items-center font-extrabold text-sm shrink-0', 'nameClass' => 'text-[15px] truncate'])
      </div>
      <button id="mob-close" class="w-8 h-8 rounded-lg bg-paper2 grid place-items-center hover:bg-paper3 transition" aria-label="إغلاق">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <form action="{{ $searchAction }}" method="GET" class="souqi-search mx-5 mt-4" role="search">
      <svg class="w-4 h-4 text-ink/35 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      <input type="search" name="q" placeholder="ابحث…" autocomplete="off" aria-label="بحث">
    </form>

    <nav class="flex-1 overflow-y-auto px-5 py-5 flex flex-col gap-0">
      <p class="text-[10px] font-black tracking-[.2em] uppercase text-ink/30 mb-3">المتجر</p>
      <a href="{{ route('home') }}" class="mob-link flex items-center justify-between py-3.5 border-b border-line text-[15px] font-semibold hover:text-brand transition group">
        <span>الرئيسية</span>
        <svg class="w-4 h-4 text-ink/20 group-hover:text-brand group-hover:-translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </a>
      <a href="{{ $brandsUrl }}" class="mob-link flex items-center justify-between py-3.5 border-b border-line text-[15px] font-semibold hover:text-brand transition group">
        <span>البراندات</span>
        <svg class="w-4 h-4 text-ink/20 group-hover:text-brand group-hover:-translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </a>
      @if($navBrands->isNotEmpty())
      <div class="py-2 border-b border-line flex flex-col gap-0.5">
        @foreach($navBrands as $brand)
        <a href="{{ route('brand.show', $brand->slug) }}" class="mob-link flex items-center gap-2.5 py-2.5 ps-3 text-[14px] font-semibold text-ink/55 hover:text-brand transition rounded-xl hover:bg-paper2">
          <span class="w-7 h-7 rounded-lg bg-ink text-white text-[10px] font-extrabold grid place-items-center shrink-0">{{ $brand->mark }}</span>
          {{ $brand->name }}
        </a>
        @endforeach
      </div>
      @endif
      <a href="{{ $productsUrl }}" class="mob-link flex items-center justify-between py-3.5 border-b border-line text-[15px] font-semibold hover:text-brand transition group">
        <span>المنتجات</span>
        <svg class="w-4 h-4 text-ink/20 group-hover:text-brand group-hover:-translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </a>

      <p class="text-[10px] font-black tracking-[.2em] uppercase text-ink/30 mb-3 mt-6">الدليل</p>
      <a href="{{ $doctorsUrl }}" class="mob-link flex items-center justify-between py-3.5 border-b border-line text-[15px] font-semibold hover:text-brand transition group">
        <span class="flex items-center gap-2.5"><span class="w-2 h-2 rounded-full bg-brand inline-block"></span>أطباء</span>
        <svg class="w-4 h-4 text-ink/20 group-hover:text-brand group-hover:-translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </a>
      <a href="{{ $nurseriesUrl }}" class="mob-link flex items-center justify-between py-3.5 text-[15px] font-semibold hover:text-brand transition group">
        <span class="flex items-center gap-2.5"><span class="w-2 h-2 rounded-full bg-brand inline-block"></span>حضانات</span>
        <svg class="w-4 h-4 text-ink/20 group-hover:text-brand group-hover:-translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </a>
    </nav>

    @if($storeSupportWhatsapp ?? false)
    <div class="px-5 py-5 border-t border-line shrink-0">
      <a href="https://wa.me/{{ preg_replace('/\D/', '', $storeSupportWhatsapp) }}" target="_blank" rel="noopener"
         class="flex items-center justify-center gap-2.5 bg-ink text-white font-bold rounded-2xl py-3.5 text-[15px] hover:bg-ink2 transition">
        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
        تواصل عبر واتساب
      </a>
    </div>
    @endif
  </div>
</div>

@once
@push('scripts')
<script>
(function () {
  const btn   = document.getElementById('mob-btn');
  const menu  = document.getElementById('mob-menu');
  const panel = document.getElementById('mob-panel');
  const bg    = document.getElementById('mob-bg');
  const close = document.getElementById('mob-close');
  const hb1   = document.getElementById('hb1');
  const hb2   = document.getElementById('hb2');
  const hb3   = document.getElementById('hb3');
  if (!btn || !panel) return;
  let open = false;

  function openMenu() {
    open = true;
    menu.classList.add('is-open');
    menu.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    hb1.style.transform = 'translateY(7px) rotate(45deg)';
    hb2.style.opacity = '0';
    hb3.style.transform = 'translateY(-7px) rotate(-45deg)';
    hb3.style.width = '20px';
    hb3.style.marginInlineStart = '0';
    hb3.style.marginInlineEnd = '0';
  }

  function closeMenu() {
    open = false;
    menu.classList.remove('is-open');
    menu.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    hb1.style.transform = '';
    hb2.style.opacity = '1';
    hb3.style.transform = '';
    hb3.style.width = '12px';
    hb3.style.marginInlineStart = 'auto';
  }

  btn.addEventListener('click', () => open ? closeMenu() : openMenu());
  if (close) close.addEventListener('click', closeMenu);
  bg.addEventListener('click', closeMenu);
  menu.querySelectorAll('.mob-link').forEach(l => l.addEventListener('click', closeMenu));
})();

const hdr = document.getElementById('hdr');
if (hdr) {
  addEventListener('scroll', () => {
    hdr.classList.toggle('border-line', scrollY > 16);
    hdr.classList.toggle('shadow-soft', scrollY > 16);
  }, { passive: true });
}

(function () {
  function scrollToHash(hash) {
    if (!hash || hash === '#') return;
    const el = document.querySelector(hash);
    if (!el) return;
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  if (location.hash) {
    requestAnimationFrame(() => scrollToHash(location.hash));
  }

  document.querySelectorAll('a[href*="#"]').forEach(a => {
    a.addEventListener('click', e => {
      let url;
      try { url = new URL(a.href); } catch (_) { return; }
      if (!url.hash || url.hash === '#') return;
      if (url.pathname !== location.pathname) return;
      const target = document.querySelector(url.hash);
      if (!target) return;
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      history.pushState(null, '', url.hash);
    });
  });
})();
</script>
@endpush
@endonce
