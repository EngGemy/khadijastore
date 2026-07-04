{{-- ═══ SHARED HEADER ═══════════════════════════════════════════════════ --}}
<header id="hdr" class="sticky top-0 z-40 bg-paper/70 backdrop-blur-2xl border-b border-transparent transition-all duration-300">
  <div class="max-w-[1180px] mx-auto px-5 h-[70px] flex items-center justify-between gap-4">
    <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-extrabold text-lg tracking-tight shrink-0 min-w-0">
      @include('partials.store-logo', ['showName' => !($storeLogo ?? store_logo_url()), 'imgClass' => 'h-10 w-auto max-w-[160px] object-contain'])
    </a>

    <nav class="hidden md:flex gap-6 text-[14px] font-semibold text-ink/55">
      <a href="{{ route('home') }}" class="hover:text-ink transition">الرئيسية</a>
      <a href="{{ route('home') }}#brands" class="hover:text-ink transition">البراندات</a>
      <a href="{{ route('home') }}#products" class="hover:text-ink transition">المنتجات</a>
      <span class="text-ink/20 select-none">|</span>
      <a href="{{ route('directory.index', 'doctor') }}" class="hover:text-ink transition flex items-center gap-1.5">
        <span class="w-1.5 h-1.5 rounded-full bg-accent inline-block"></span>الأطباء
      </a>
      <a href="{{ route('directory.index', 'nursery') }}" class="hover:text-ink transition flex items-center gap-1.5">
        <span class="w-1.5 h-1.5 rounded-full bg-accent inline-block"></span>الحضانات
      </a>
    </nav>

    <div class="flex items-center gap-3">
      @if($storeSupportWhatsapp ?? false)
      <a href="https://wa.me/{{ preg_replace('/\D/', '', $storeSupportWhatsapp) }}" target="_blank"
         class="hidden md:inline-flex items-center gap-2 bg-accent text-white text-sm font-bold rounded-full shadow-cta hover:bg-accentDark hover:-translate-y-0.5 transition-all"
         style="padding:10px 18px">
        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
        واتساب
      </a>
      @endif

      <button id="mob-btn" class="md:hidden w-10 h-10 rounded-xl hover:bg-paper2 transition flex flex-col items-center justify-center gap-[5px]" aria-label="القائمة">
        <span id="hb1" class="block w-5 h-[2px] bg-ink rounded-full transition-all duration-300"></span>
        <span id="hb2" class="block w-5 h-[2px] bg-ink rounded-full transition-all duration-300"></span>
        <span id="hb3" class="block w-3 h-[2px] bg-ink rounded-full ms-auto transition-all duration-300" style="margin-inline-end:0;margin-inline-start:auto;width:12px"></span>
      </button>
    </div>
  </div>
</header>

{{-- ═══ MOBILE MENU ═══════════════════════════════════════════════════════ --}}
<div id="mob-menu" class="fixed inset-0 z-[60] pointer-events-none" aria-hidden="true">
  <div id="mob-bg" class="absolute inset-0 bg-ink/55 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
  <div id="mob-panel" class="absolute top-0 right-0 bottom-0 w-[78vw] max-w-[300px] bg-paper flex flex-col shadow-lg2" style="transform:translateX(105%);transition:transform .38s cubic-bezier(.16,1,.3,1)">
    {{-- رأس الدرج --}}
    <div class="flex items-center justify-between px-5 h-[70px] border-b border-line shrink-0">
      <div class="flex items-center gap-2.5 font-extrabold tracking-tight min-w-0">
        @include('partials.store-logo', ['showName' => !($storeLogo ?? store_logo_url()), 'imgClass' => 'h-9 w-auto max-w-[140px] object-contain', 'fallbackClass' => 'w-9 h-9 rounded-xl bg-ink text-paper grid place-items-center font-extrabold text-base shrink-0', 'nameClass' => 'text-[15px]'])
      </div>
      <button id="mob-close" class="w-8 h-8 rounded-lg bg-paper2 grid place-items-center hover:bg-paper3 transition" aria-label="إغلاق">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    {{-- روابط --}}
    <nav class="flex-1 overflow-y-auto px-5 py-5 flex flex-col gap-0">
      <p class="text-[10px] font-black tracking-[.2em] uppercase text-ink/30 mb-3">المتجر</p>
      <a href="{{ route('home') }}" class="mob-link flex items-center justify-between py-3.5 border-b border-line text-[15px] font-semibold hover:text-accent transition group">
        <span>الرئيسية</span>
        <svg class="w-4 h-4 text-ink/20 group-hover:text-accent group-hover:-translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </a>
      <a href="{{ route('home') }}#brands" class="mob-link flex items-center justify-between py-3.5 border-b border-line text-[15px] font-semibold hover:text-accent transition group">
        <span>البراندات</span>
        <svg class="w-4 h-4 text-ink/20 group-hover:text-accent group-hover:-translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </a>
      <a href="{{ route('home') }}#products" class="mob-link flex items-center justify-between py-3.5 border-b border-line text-[15px] font-semibold hover:text-accent transition group">
        <span>المنتجات</span>
        <svg class="w-4 h-4 text-ink/20 group-hover:text-accent group-hover:-translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </a>

      <p class="text-[10px] font-black tracking-[.2em] uppercase text-ink/30 mb-3 mt-6">الدليل</p>
      <a href="{{ route('directory.index', 'doctor') }}" class="mob-link flex items-center justify-between py-3.5 border-b border-line text-[15px] font-semibold hover:text-accent transition group">
        <span class="flex items-center gap-2.5"><span class="w-2 h-2 rounded-full bg-accent inline-block"></span>أطباء</span>
        <svg class="w-4 h-4 text-ink/20 group-hover:text-accent group-hover:-translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </a>
      <a href="{{ route('directory.index', 'nursery') }}" class="mob-link flex items-center justify-between py-3.5 text-[15px] font-semibold hover:text-accent transition group">
        <span class="flex items-center gap-2.5"><span class="w-2 h-2 rounded-full bg-accent inline-block"></span>حضانات</span>
        <svg class="w-4 h-4 text-ink/20 group-hover:text-accent group-hover:-translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </a>
    </nav>

    {{-- زر الواتساب أسفل الدرج --}}
    @if($storeSupportWhatsapp ?? false)
    <div class="px-5 py-5 border-t border-line shrink-0">
      <a href="https://wa.me/{{ preg_replace('/\D/', '', $storeSupportWhatsapp) }}" target="_blank"
         class="flex items-center justify-center gap-2.5 bg-accent text-white font-bold rounded-2xl py-3.5 text-[15px] hover:bg-accentDark transition">
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
    menu.classList.remove('pointer-events-none');
    bg.style.opacity = '1';
    panel.style.transform = 'translateX(0)';
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
    menu.classList.add('pointer-events-none');
    bg.style.opacity = '0';
    panel.style.transform = 'translateX(105%)';
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

// header scroll shadow
const hdr = document.getElementById('hdr');
if (hdr) {
  addEventListener('scroll', () => {
    hdr.classList.toggle('border-line', scrollY > 16);
    hdr.classList.toggle('shadow-soft', scrollY > 16);
  }, { passive: true });
}
</script>
@endpush
@endonce
