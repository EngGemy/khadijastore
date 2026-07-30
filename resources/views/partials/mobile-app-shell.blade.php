{{-- ═══ Mobile App Shell — bottom tabs + safe areas (md:hidden) ═══════════ --}}
@php
  $offersUrl = nav_home_section_url('offers', url('/#offers'));
  $isHome = request()->routeIs('home');
  $isProducts = request()->routeIs('products.*', 'product.*');
  $isBrands = request()->routeIs('brands.*', 'brand.*');
  $isDirectory = request()->routeIs('directory.*');
  $isProductPage = request()->routeIs('product.show');
  $hideBottomNav = $isProductPage;
@endphp

<style>
  :root{
    --app-nav-h:64px;
    --app-fab-clear:calc(var(--app-nav-h) + 16px + env(safe-area-inset-bottom,0px));
  }

  /* ── Body shell ───────────────────────────────────────────────────── */
  @media (max-width:767px){
    body.app-shell{
      padding-bottom:calc(var(--app-nav-h) + env(safe-area-inset-bottom,0px));
    }
    body.app-shell.app-shell--no-tabs{
      padding-bottom:env(safe-area-inset-bottom,0px);
    }

    /* Compact sticky app bar */
    #hdr.app-bar{
      background:rgba(255,255,255,.94);
      backdrop-filter:blur(20px) saturate(1.2);
      -webkit-backdrop-filter:blur(20px) saturate(1.2);
    }
    #hdr.app-bar > div{
      height:56px;
      padding-inline:12px;
      gap:.5rem;
    }
    #hdr.app-bar .hdr-icon{
      width:44px;height:44px;border-radius:14px;
      min-width:44px;min-height:44px;
    }

    /* Raise FABs above bottom nav */
    #ai-fab{
      bottom:var(--app-fab-clear)!important;
      inset-inline-start:14px!important;
      width:50px!important;height:50px!important;
      animation:fabPulse 2.8s cubic-bezier(.65,.05,.36,1) infinite;
    }
    #ai-fab:active{transform:scale(.92)!important}
    .brand-wa-fab,#brandWaFloat{
      bottom:var(--app-fab-clear)!important;
    }

    /* Product sticky buy bar stays flush; tabs hidden on product */
    body.app-shell--no-tabs .product-sticky-cta{
      padding-bottom:calc(12px + env(safe-area-inset-bottom,0px));
    }

    /* Footer breathing room above tabs */
    footer{padding-bottom:calc(28px + env(safe-area-inset-bottom,0px))!important}

    /* Strip: thinner on mobile */
    body > .bg-ink.text-paper.text-center{padding-block:6px;font-size:11px}

    /* Typography / spacing polish */
    .home-section{padding-top:clamp(28px,7vw,48px);padding-bottom:clamp(28px,7vw,48px)}
    .feature-pill{padding:.85rem .9rem;gap:.65rem;min-height:64px}
    .feature-pill__icon{width:40px;height:40px;border-radius:12px}
    .product-card{border-radius:14px}
    .product-card__body{padding:.75rem .8rem .85rem;gap:.25rem}
    .product-card__title{font-size:.8125rem}
    .product-card__price{font-size:1.05rem}
    .brand-store-card{padding:1.1rem;border-radius:16px;gap:.75rem}
    .letter-chip{width:44px;height:44px;min-width:44px}

    /* Horizontal swipe rows */
    .app-h-scroll{
      display:flex;gap:.75rem;overflow-x:auto;overscroll-behavior-x:contain;
      scroll-snap-type:x proximity;-webkit-overflow-scrolling:touch;
      scrollbar-width:none;padding-bottom:4px;margin-inline:-4px;padding-inline:4px;
    }
    .app-h-scroll::-webkit-scrollbar{display:none}
    .app-h-scroll > *{scroll-snap-align:start;flex-shrink:0}

    /* Page enter */
    .app-page-enter{
      animation:appPageIn .45s cubic-bezier(.16,1,.3,1) both;
    }
    .app-stagger > *{
      animation:appCardIn .5s cubic-bezier(.16,1,.3,1) both;
    }
    .app-stagger > *:nth-child(1){animation-delay:.04s}
    .app-stagger > *:nth-child(2){animation-delay:.09s}
    .app-stagger > *:nth-child(3){animation-delay:.14s}
    .app-stagger > *:nth-child(4){animation-delay:.19s}
    .app-stagger > *:nth-child(5){animation-delay:.24s}
    .app-stagger > *:nth-child(6){animation-delay:.29s}
    .app-stagger > *:nth-child(7){animation-delay:.34s}
    .app-stagger > *:nth-child(8){animation-delay:.39s}
  }

  /* ── Bottom tab bar ───────────────────────────────────────────────── */
  #app-tabbar{
    display:none;
  }
  @media (max-width:767px){
    #app-tabbar{
      display:flex;
      position:fixed;inset-inline:0;bottom:0;z-index:55;
      height:calc(var(--app-nav-h) + env(safe-area-inset-bottom,0px));
      padding:6px 6px calc(6px + env(safe-area-inset-bottom,0px));
      background:rgba(255,255,255,.94);
      backdrop-filter:blur(22px) saturate(1.25);
      -webkit-backdrop-filter:blur(22px) saturate(1.25);
      border-top:1px solid rgba(11,29,54,.08);
      box-shadow:0 -8px 32px rgba(11,29,54,.08);
      align-items:stretch;justify-content:space-around;gap:2px;
    }
    body.app-shell--no-tabs #app-tabbar{display:none}
  }

  .app-tab{
    flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;
    gap:2px;min-height:44px;min-width:0;padding:4px 2px;
    border-radius:14px;color:rgba(11,29,54,.42);
    font-size:10px;font-weight:800;line-height:1.2;text-decoration:none;
    transition:color .2s ease,background .2s ease,transform .15s ease;
    -webkit-tap-highlight-color:transparent;touch-action:manipulation;
    position:relative;background:transparent;border:none;cursor:pointer;
    font-family:inherit;
  }
  .app-tab svg{width:22px;height:22px;flex-shrink:0;transition:transform .25s cubic-bezier(.16,1,.3,1)}
  .app-tab span{max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
  .app-tab:active{transform:scale(.94)}
  .app-tab.is-active{
    color:var(--navy);
    background:rgba(11,29,54,.06);
  }
  .app-tab.is-active svg{color:var(--orange);transform:translateY(-1px) scale(1.06)}
  .app-tab.is-active::after{
    content:'';position:absolute;top:4px;inset-inline:50%;
    width:16px;height:3px;margin-inline-start:-8px;border-radius:999px;
    background:var(--orange-bright);
    animation:tabIndicator .35s cubic-bezier(.16,1,.3,1) both;
  }
  .app-tab--accent.is-active{color:var(--orange)}

  /* ── More sheet ───────────────────────────────────────────────────── */
  #app-more-sheet{display:none}
  @media (max-width:767px){
    #app-more-sheet{
      display:block;position:fixed;inset:0;z-index:70;
      visibility:hidden;pointer-events:none;
    }
    #app-more-sheet.is-open{visibility:visible;pointer-events:auto}
    #app-more-sheet .app-more-bg{
      position:absolute;inset:0;background:rgba(11,29,54,.45);
      backdrop-filter:blur(3px);opacity:0;transition:opacity .28s ease;
    }
    #app-more-sheet.is-open .app-more-bg{opacity:1}
    #app-more-sheet .app-more-panel{
      position:absolute;inset-inline:0;bottom:0;
      background:#fff;border-radius:22px 22px 0 0;
      padding:12px 16px calc(20px + env(safe-area-inset-bottom,0px));
      box-shadow:0 -16px 48px rgba(11,29,54,.16);
      transform:translateY(110%);
      transition:transform .38s cubic-bezier(.16,1,.3,1);
    }
    #app-more-sheet.is-open .app-more-panel{transform:translateY(0)}
    .app-more-handle{
      width:40px;height:4px;border-radius:999px;background:rgba(11,29,54,.12);
      margin:4px auto 14px;
    }
    .app-more-link{
      display:flex;align-items:center;gap:12px;min-height:52px;
      padding:10px 12px;border-radius:16px;text-decoration:none;
      color:var(--navy);font-weight:800;font-size:15px;
      transition:background .2s ease;
    }
    .app-more-link:active,.app-more-link:hover{background:var(--paper-2)}
    .app-more-icon{
      width:42px;height:42px;border-radius:14px;display:grid;place-items:center;flex-shrink:0;
      background:var(--orange-soft);color:var(--orange);
    }
    .app-more-icon--navy{background:rgba(11,29,54,.06);color:var(--navy)}
  }

  @keyframes appPageIn{
    from{opacity:0;transform:translateY(12px)}
    to{opacity:1;transform:translateY(0)}
  }
  @keyframes appCardIn{
    from{opacity:0;transform:translateY(18px) scale(.98)}
    to{opacity:1;transform:translateY(0) scale(1)}
  }
  @keyframes tabIndicator{
    from{opacity:0;transform:scaleX(.4)}
    to{opacity:1;transform:scaleX(1)}
  }
  @keyframes fabPulse{
    0%,100%{box-shadow:0 8px 24px rgba(0,0,0,.22)}
    50%{box-shadow:0 8px 24px rgba(0,0,0,.22),0 0 0 10px rgba(232,93,4,.12)}
  }

  @media (prefers-reduced-motion:reduce){
    .app-page-enter,.app-stagger > *,.app-tab.is-active::after,#ai-fab{animation:none!important}
    #app-more-sheet .app-more-panel,#app-more-sheet .app-more-bg{transition:none!important}
  }
</style>

<nav id="app-tabbar" class="md:hidden" aria-label="التنقل الرئيسي" @if($hideBottomNav) hidden @endif>
  <a href="{{ route('home') }}" class="app-tab {{ $isHome ? 'is-active' : '' }}" @if($isHome) aria-current="page" @endif>
    <svg fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10.5L12 3l9 7.5"/><path d="M5 9.5V20a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V9.5"/></svg>
    <span>الرئيسية</span>
  </a>
  <a href="{{ route('products.index') }}" class="app-tab {{ $isProducts ? 'is-active' : '' }}" @if($isProducts) aria-current="page" @endif>
    <svg fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16v12a2 2 0 01-2 2H6a2 2 0 01-2-2V7z"/><path d="M8 7V5a4 4 0 018 0v2"/></svg>
    <span>المنتجات</span>
  </a>
  <a href="{{ route('brands.index') }}" class="app-tab {{ $isBrands ? 'is-active' : '' }}" @if($isBrands) aria-current="page" @endif>
    <svg fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l2.2 4.5L19 8.2l-3.5 3.4.8 4.9L12 14.8 7.7 16.5l.8-4.9L5 8.2l4.8-.7L12 3z"/></svg>
    <span>البراندات</span>
  </a>
  <a href="{{ $offersUrl }}" class="app-tab app-tab--accent">
    <svg fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/><circle cx="12" cy="12" r="3.5"/></svg>
    <span>عروض</span>
  </a>
  <button type="button" id="app-more-btn" class="app-tab {{ $isDirectory ? 'is-active' : '' }}" aria-haspopup="dialog" aria-expanded="false" aria-controls="app-more-sheet">
    <svg fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="12" r="1.6" fill="currentColor" stroke="none"/><circle cx="12" cy="12" r="1.6" fill="currentColor" stroke="none"/><circle cx="19" cy="12" r="1.6" fill="currentColor" stroke="none"/></svg>
    <span>المزيد</span>
  </button>
</nav>

<div id="app-more-sheet" role="dialog" aria-modal="true" aria-label="المزيد" aria-hidden="true">
  <div class="app-more-bg" data-app-more-close></div>
  <div class="app-more-panel">
    <div class="app-more-handle" aria-hidden="true"></div>
    <p class="text-[11px] font-black tracking-[.18em] uppercase text-ink/35 mb-2 px-1">الدليل والمزيد</p>
    <a href="{{ route('directory.index', 'doctor') }}" class="app-more-link">
      <span class="app-more-icon" aria-hidden="true">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      </span>
      <span class="flex-1">دليل الأطباء</span>
      <svg class="w-4 h-4 text-ink/25" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <a href="{{ route('directory.index', 'nursery') }}" class="app-more-link">
      <span class="app-more-icon" aria-hidden="true">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
      </span>
      <span class="flex-1">دليل الحضانات</span>
      <svg class="w-4 h-4 text-ink/25" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <a href="{{ route('assistant.page') }}" class="app-more-link">
      <span class="app-more-icon app-more-icon--navy" aria-hidden="true">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
      </span>
      <span class="flex-1">المساعد الذكي</span>
      <svg class="w-4 h-4 text-ink/25" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <button type="button" id="app-more-menu" class="app-more-link w-full text-start">
      <span class="app-more-icon app-more-icon--navy" aria-hidden="true">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10"/></svg>
      </span>
      <span class="flex-1">القائمة الكاملة</span>
      <svg class="w-4 h-4 text-ink/25" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
  </div>
</div>

@once
@push('scripts')
<script>
(function () {
  const body = document.body;
  body.classList.add('app-shell');
  @if($hideBottomNav)
  body.classList.add('app-shell--no-tabs');
  @endif

  const main = document.querySelector('main, .product-page, section.bg-paper2\\/70, #hero');
  if (main && window.matchMedia('(max-width:767px)').matches) {
    document.querySelectorAll('#hero, .product-page, section.max-w-\\[1180px\\], .home-section').forEach((el, i) => {
      if (i === 0) el.classList.add('app-page-enter');
    });
    document.querySelectorAll('.grid.grid-cols-2, .brand-product-grid').forEach(g => {
      if (g.children.length && g.children.length <= 12) g.classList.add('app-stagger');
    });
  }

  const hdr = document.getElementById('hdr');
  if (hdr) hdr.classList.add('app-bar');

  const sheet = document.getElementById('app-more-sheet');
  const moreBtn = document.getElementById('app-more-btn');
  const moreMenu = document.getElementById('app-more-menu');
  const mobBtn = document.getElementById('mob-btn');

  function openMore() {
    if (!sheet) return;
    sheet.classList.add('is-open');
    sheet.setAttribute('aria-hidden', 'false');
    if (moreBtn) moreBtn.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  }
  function closeMore() {
    if (!sheet) return;
    sheet.classList.remove('is-open');
    sheet.setAttribute('aria-hidden', 'true');
    if (moreBtn) moreBtn.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }

  if (moreBtn) moreBtn.addEventListener('click', () => {
    sheet && sheet.classList.contains('is-open') ? closeMore() : openMore();
  });
  sheet && sheet.querySelectorAll('[data-app-more-close]').forEach(el => el.addEventListener('click', closeMore));
  if (moreMenu) moreMenu.addEventListener('click', () => {
    closeMore();
    if (mobBtn) mobBtn.click();
  });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMore(); });

  // Highlight "عروض" when landing on #offers
  if (location.hash === '#offers') {
    document.querySelectorAll('.app-tab').forEach(t => t.classList.remove('is-active'));
    const offersTab = document.querySelector('.app-tab.app-tab--accent');
    if (offersTab) offersTab.classList.add('is-active');
  }
})();
</script>
@endpush
@endonce
