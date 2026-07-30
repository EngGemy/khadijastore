@once
@push('head')
<style>
  .brand-mesh{position:absolute;inset:0;overflow:hidden;pointer-events:none}
  .brand-blob{position:absolute;border-radius:50%;filter:blur(60px);opacity:.45;animation:brandBlob 20s ease-in-out infinite}
  .brand-blob-2{animation:brandBlob2 24s ease-in-out infinite;animation-delay:-6s}
  @keyframes brandBlob{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(20px,-12px) scale(1.05)}}
  @keyframes brandBlob2{0%,100%{transform:translate(0,0)}50%{transform:translate(-16px,10px)}}

  /* ── Brand page shell ── */
  .brand-page{--brand-order-h:68px;--navy:#0B1D36;--orange:#E85D04;--orange-bright:#F97316}
  .brand-block{
    max-width:1180px;margin-inline:auto;
    padding:20px 16px 8px;
  }
  @media(min-width:640px){.brand-block{padding:28px 20px 12px}}
  .brand-block--search{padding-top:14px;padding-bottom:4px}
  .brand-block--last{padding-bottom:8px}
  .brand-section-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px}
  .brand-section-title{font-size:17px;font-weight:900;letter-spacing:-.02em;color:var(--navy,#0B1D36);margin:0}
  @media(min-width:640px){.brand-section-title{font-size:20px}}
  .brand-section-link{
    font-size:13px;font-weight:800;color:var(--orange,#E85D04);
    text-decoration:none;min-height:44px;display:inline-flex;align-items:center;
    padding-inline:4px;
  }

  /* ── Hero — navy + orange ── */
  .brand-hero{position:relative;overflow:hidden;color:#fff;padding:12px 0 14px}
  @media(min-width:640px){.brand-hero{padding:24px 0 28px}}
  .brand-hero--compact{padding:10px 0 12px}
  .brand-hero__bg{position:absolute;inset:0;background:linear-gradient(145deg,#06101f 0%,#0B1D36 48%,#132a4a 100%)}
  .brand-hero__orb{position:absolute;border-radius:50%;filter:blur(48px);opacity:.5;animation:heroOrb 14s ease-in-out infinite}
  .brand-hero__orb--1{width:200px;height:200px;top:-40px;inset-inline-start:-30px;background:#E85D04}
  .brand-hero__orb--2{width:160px;height:160px;bottom:-20px;inset-inline-end:10%;background:#F97316;opacity:.35;animation-delay:-4s}
  .brand-hero__orb--3{width:120px;height:120px;top:30%;inset-inline-end:-20px;background:#1e3a5f;opacity:.6;animation-delay:-8s}
  @keyframes heroOrb{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(12px,-8px) scale(1.08)}}
  .brand-hero__shine{position:absolute;inset:0;background:radial-gradient(ellipse 80% 55% at 50% -10%,rgba(249,115,22,.22),transparent 60%)}
  .brand-hero__grid{position:absolute;inset:0;opacity:.04;background-image:radial-gradient(circle at 1px 1px,#fff 1px,transparent 0);background-size:20px 20px}
  .brand-hero__inner{position:relative;z-index:2;max-width:1180px;margin:0 auto;padding:0 14px}
  @media(min-width:640px){.brand-hero__inner{padding:0 20px}}
  .brand-hero__glass{
    background:rgba(255,255,255,.08);
    border:1px solid rgba(255,255,255,.14);
    backdrop-filter:blur(16px);
    -webkit-backdrop-filter:blur(16px);
    border-radius:22px;
    padding:16px;
    box-shadow:0 20px 50px -20px rgba(0,0,0,.45),inset 0 1px 0 rgba(255,255,255,.1);
  }
  @media(min-width:640px){.brand-hero__glass{padding:22px 24px;border-radius:24px}}
  .brand-hero--compact .brand-hero__glass{padding:12px 14px;border-radius:16px}

  .brand-hero__top{display:flex;align-items:flex-start;gap:10px}
  .brand-hero__share{flex-shrink:0;padding-top:2px}
  .brand-hero__profile{display:flex;align-items:center;gap:14px;min-width:0;flex:1}
  @media(max-width:639px){
    .brand-hero__profile{flex-direction:row;text-align:start;align-items:center;gap:12px}
    .brand-hero__stats{justify-content:flex-start}
  }
  .brand-hero__info{min-width:0;flex:1}
  @media(max-width:639px){.brand-hero__info{width:auto}}

  .brand-hero__logo-stage{position:relative;width:72px;height:72px;flex-shrink:0}
  @media(min-width:640px){.brand-hero__logo-stage{width:92px;height:92px}}
  .brand-hero--compact .brand-hero__logo-stage{width:52px;height:52px}
  .brand-hero__logo-glow{
    position:absolute;inset:-8px;border-radius:50%;
    background:conic-gradient(from 180deg,#E85D04,#F97316,#fdba74,#E85D04);
    opacity:.7;animation:heroGlow 6s linear infinite;
  }
  @keyframes heroGlow{to{transform:rotate(360deg)}}
  .brand-hero__logo-frame{
    position:relative;z-index:2;width:100%;height:100%;border-radius:50%;
    background:linear-gradient(180deg,#fff 0%,#fff7ed 100%);
    display:flex;align-items:center;justify-content:center;
    overflow:hidden;
    box-shadow:0 8px 24px -6px rgba(0,0,0,.35);
    border:3px solid rgba(255,255,255,.9);
  }
  .brand-hero__logo-img{
    width:72%;height:72%;max-width:72%;max-height:72%;
    object-fit:contain;object-position:center;display:block;
  }
  .brand-hero__mark{font-weight:900;font-size:1.6rem;line-height:1;color:#0B1D36}
  .brand-hero--compact .brand-hero__mark{font-size:1.15rem}

  .brand-hero__cat{
    display:inline-block;font-size:10px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;
    color:#fdba74;background:rgba(232,93,4,.22);border:1px solid rgba(249,115,22,.4);
    border-radius:999px;padding:4px 10px;margin-bottom:6px;
  }
  .brand-hero__title{font-weight:900;letter-spacing:-.03em;line-height:1.2;margin:0;font-size:clamp(18px,4.8vw,34px)}
  .brand-hero__desc{margin:6px 0 0;font-size:12px;line-height:1.55;color:rgba(255,255,255,.72);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  @media(min-width:640px){.brand-hero__desc{font-size:13px;-webkit-line-clamp:3}}
  .brand-hero__stats{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px}

  .brand-hero__actions{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:16px}
  @media(max-width:359px){.brand-hero__actions{grid-template-columns:1fr}}
  @media(min-width:640px){.brand-hero__actions{display:flex;gap:10px;margin-top:16px}}
  .brand-hero__btn{
    display:inline-flex;align-items:center;justify-content:center;gap:6px;
    min-height:48px;padding:12px 16px;border-radius:16px;font-size:14px;font-weight:800;
    text-decoration:none;transition:transform .25s cubic-bezier(.16,1,.3,1),box-shadow .25s;
  }
  .brand-hero__btn:active{transform:scale(.96)}
  .brand-hero__btn--primary{background:#fff;color:#0B1D36;box-shadow:0 4px 14px rgba(0,0,0,.15)}
  .brand-hero__btn--primary:hover{box-shadow:0 8px 20px rgba(0,0,0,.2)}
  .brand-hero__btn--wa{background:linear-gradient(135deg,#F97316,#E85D04);color:#fff;box-shadow:0 4px 16px rgba(232,93,4,.45)}

  .brand-hero-enter{animation:heroEnter .7s cubic-bezier(.16,1,.3,1) both}
  .brand-hero-enter--2{animation-delay:.08s}
  .brand-hero-enter--3{animation-delay:.14s}
  .brand-hero-enter--4{animation-delay:.2s}
  @keyframes heroEnter{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}

  .brand-stat-pill{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.15);border-radius:999px;padding:5px 11px;font-size:11px;font-weight:700;white-space:nowrap}

  /* ── Sub-nav — segmented app control ── */
  .brand-subnav{
    position:sticky;top:56px;z-index:40;
    background:rgba(255,255,255,.94);
    backdrop-filter:blur(16px) saturate(1.15);
    -webkit-backdrop-filter:blur(16px) saturate(1.15);
    border-bottom:1px solid rgba(11,29,54,.08);
  }
  @media(min-width:768px){.brand-subnav{top:68px}}
  .brand-subnav__inner{max-width:1180px;margin:0 auto;padding:10px 14px}
  @media(min-width:640px){.brand-subnav__inner{padding:12px 20px}}
  .brand-subnav__track{
    display:grid;grid-template-columns:repeat(3,1fr);gap:4px;
    padding:4px;border-radius:16px;
    background:rgba(11,29,54,.06);
  }
  .brand-subnav__tab{
    display:flex;align-items:center;justify-content:center;
    min-height:42px;padding:8px 6px;border-radius:12px;
    font-size:12px;font-weight:800;text-decoration:none;text-align:center;
    color:rgba(11,29,54,.48);transition:background .2s,color .2s,box-shadow .2s;
    -webkit-tap-highlight-color:transparent;
  }
  @media(min-width:640px){.brand-subnav__tab{font-size:13px;min-height:44px;padding:10px 12px}}
  .brand-subnav__tab.is-active{
    background:#0B1D36;color:#fff;
    box-shadow:0 6px 16px -6px rgba(11,29,54,.4);
  }
  .brand-subnav__label--short{display:inline}
  .brand-subnav__label--full{display:none}
  @media(min-width:480px){
    .brand-subnav__label--short{display:none}
    .brand-subnav__label--full{display:inline}
  }

  /* ── Share sheet ── */
  .brand-share__trigger{
    display:inline-flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;
    min-width:44px;min-height:44px;padding:6px 8px;border-radius:14px;
    background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);
    color:#fff;font-size:9px;font-weight:800;cursor:pointer;
    -webkit-tap-highlight-color:transparent;
  }
  .brand-share__trigger:active{transform:scale(.94);background:rgba(255,255,255,.2)}
  .brand-share__trigger-label{line-height:1}
  .brand-hero--compact .brand-share__trigger-label{display:none}
  .brand-share__sheet{
    position:fixed;inset:0;z-index:80;
    background:rgba(11,29,54,.45);backdrop-filter:blur(3px);
    display:flex;align-items:flex-end;justify-content:center;
  }
  .brand-share__panel{
    width:100%;max-width:480px;
    background:#fff;border-radius:22px 22px 0 0;
    padding:12px 16px calc(20px + env(safe-area-inset-bottom,0px));
    box-shadow:0 -16px 48px rgba(11,29,54,.16);
  }
  .brand-share__handle{width:40px;height:4px;border-radius:999px;background:rgba(11,29,54,.12);margin:4px auto 14px}
  .brand-share__title{font-size:16px;font-weight:900;color:#0B1D36;margin:0 0 14px;text-align:center}
  .brand-share__actions{display:grid;gap:8px}
  .brand-share__action{
    display:flex;align-items:center;gap:12px;min-height:52px;
    padding:10px 14px;border-radius:16px;text-decoration:none;
    color:#0B1D36;font-weight:800;font-size:15px;background:rgba(11,29,54,.04);
    border:none;width:100%;cursor:pointer;font-family:inherit;text-align:start;
  }
  .brand-share__action:active{background:rgba(11,29,54,.08)}
  .brand-share__icon{
    width:42px;height:42px;border-radius:14px;display:grid;place-items:center;flex-shrink:0;
  }
  .brand-share__icon--copy{background:rgba(11,29,54,.08);color:#0B1D36}
  .brand-share__icon--wa{background:#16a34a;color:#fff}
  .brand-share__icon--fb{background:#1877f2;color:#fff}
  .brand-share__cancel{
    margin-top:10px;width:100%;min-height:48px;border-radius:14px;
    border:1px solid rgba(11,29,54,.1);background:#fff;
    font-weight:800;font-size:14px;color:rgba(11,29,54,.55);cursor:pointer;font-family:inherit;
  }

  /* legacy icon buttons (desktop manufacturers etc.) */
  .brand-icon-btn{width:36px;height:36px;border-radius:12px;border:1px solid rgba(10,10,10,.1);background:#fff;color:#0a0a0a;display:grid;place-items:center;transition:transform .25s,box-shadow .25s,background .25s}
  .brand-icon-btn:active{transform:scale(.92)}
  .brand-icon-btn.is-success{background:#16a34a;color:#fff;border-color:#16a34a}
  .brand-icon-btn--wa{background:#16a34a;color:#fff;border-color:#16a34a}
  .brand-icon-btn--fb{background:#1877f2;color:#fff;border-color:#1877f2}

  /* category / mfg chips — larger tap targets */
  .brand-cat-chip,.brand-mfg-chip{display:flex;flex-direction:column;align-items:center;width:76px;text-decoration:none;color:inherit;scroll-snap-align:start;flex-shrink:0}
  @media(min-width:640px){.brand-cat-chip,.brand-mfg-chip{width:84px}}
  .brand-cat-chip__icon,.brand-mfg-chip__icon{position:relative;width:64px;height:64px;border-radius:50%;overflow:hidden;display:grid;place-items:center;font-weight:800;font-size:18px;color:#fff;background:var(--chip-color,#E85D04);transition:transform .35s cubic-bezier(.16,1,.3,1),box-shadow .35s}
  @media(min-width:640px){.brand-cat-chip__icon,.brand-mfg-chip__icon{width:72px;height:72px}}
  .brand-cat-chip:active .brand-cat-chip__icon,.brand-mfg-chip:active .brand-mfg-chip__icon{transform:scale(.92)}
  .brand-cat-chip__ring{position:absolute;inset:-3px;border-radius:50%;border:2px solid var(--chip-color,#E85D04);opacity:0;transform:scale(.85);transition:opacity .3s,transform .3s}
  .brand-cat-chip__icon img{border-radius:50%}
  .brand-cat-chip__label,.brand-mfg-chip__label{margin-top:10px;font-size:11px;font-weight:700;text-align:center;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;width:100%;color:#0B1D36}
  @media(min-width:640px){.brand-cat-chip__label,.brand-mfg-chip__label{font-size:12px}}
  .brand-cat-chip__count{font-size:10px;font-weight:600;color:rgba(11,29,54,.4);margin-top:2px}
  .brand-mfg-chip__icon{background:linear-gradient(135deg,var(--from,#E85D04),var(--to,#c2410c))}
  .brand-mfg-card{transition:transform .35s cubic-bezier(.16,1,.3,1),box-shadow .35s}
  .brand-mfg-card:hover{transform:translateY(-4px);box-shadow:0 20px 40px -12px rgba(0,0,0,.2)}

  .brand-chip-scroll{
    display:flex;gap:16px;overflow-x:auto;padding:6px 2px 12px;
    scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;scrollbar-width:none;
    margin-inline:-4px;padding-inline:4px;
  }
  .brand-chip-scroll::-webkit-scrollbar{display:none}

  .brand-search{position:relative}
  .brand-search__icon{position:absolute;inset-inline-start:14px;top:50%;transform:translateY(-50%);width:18px;height:18px;color:rgba(11,29,54,.35);pointer-events:none}
  .brand-search input{
    width:100%;border-radius:16px;border:1px solid rgba(11,29,54,.1);background:#f4f5f7;
    padding:14px;padding-inline-start:44px;font-size:16px;font-weight:600;outline:none;
    transition:border-color .2s,box-shadow .2s;-webkit-appearance:none;min-height:48px;color:#0B1D36;
  }
  @media(min-width:640px){.brand-search input{font-size:14px}}
  .brand-search input:focus{border-color:rgba(232,93,4,.45);box-shadow:0 0 0 3px rgba(232,93,4,.12);background:#fff}

  /* product cards */
  .product-pop{animation:productPop .55s cubic-bezier(.16,1,.3,1) both}
  @keyframes productPop{0%{opacity:0;transform:translateY(16px) scale(.96)}100%{opacity:1;transform:translateY(0) scale(1)}}
  .product-pop:nth-child(1){animation-delay:.04s}.product-pop:nth-child(2){animation-delay:.08s}.product-pop:nth-child(3){animation-delay:.12s}.product-pop:nth-child(4){animation-delay:.16s}
  .product-pop:nth-child(5){animation-delay:.2s}.product-pop:nth-child(6){animation-delay:.24s}.product-pop:nth-child(7){animation-delay:.28s}.product-pop:nth-child(8){animation-delay:.32s}
  .brand-product-card{transition:transform .35s cubic-bezier(.16,1,.3,1),box-shadow .35s}
  .brand-product-card:active{transform:scale(.97)}
  @media(max-width:380px){
    .brand-product-card h3{font-size:12px!important}
    .brand-product-card .font-extrabold{font-size:15px!important}
  }

  .brand-product-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
  @media(min-width:640px){.brand-product-grid{gap:16px}}
  @media(min-width:768px){.brand-product-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
  @media(min-width:1024px){.brand-product-grid{grid-template-columns:repeat(4,minmax(0,1fr))}}

  /* Safe bottom — order bar only (tabs hidden on brand home) */
  .brand-safe-bottom{padding-bottom:calc(28px + env(safe-area-inset-bottom,0px))}
  @media(max-width:767px){
    .brand-page--home .brand-safe-bottom{
      padding-bottom:calc(var(--brand-order-h,68px) + 24px + env(safe-area-inset-bottom,0px));
    }
    .brand-page:not(.brand-page--home) .brand-safe-bottom{
      padding-bottom:calc(var(--app-nav-h,64px) + 28px + env(safe-area-inset-bottom,0px));
    }
  }

  /* Sticky «اطلب بضغطة» — flush when tabs hidden */
  .brand-order-bar{
    position:fixed;inset-inline:0;bottom:0;z-index:56;
    display:flex;gap:10px;align-items:center;
    padding:12px 14px calc(12px + env(safe-area-inset-bottom,0px));
    background:rgba(255,255,255,.96);
    backdrop-filter:blur(18px) saturate(1.2);
    -webkit-backdrop-filter:blur(18px) saturate(1.2);
    border-top:1px solid rgba(11,29,54,.08);
    box-shadow:0 -10px 32px rgba(11,29,54,.1);
  }
  body.app-shell:not(.app-shell--no-tabs) .brand-order-bar{
    bottom:calc(var(--app-nav-h,64px) + env(safe-area-inset-bottom,0px));
    padding-bottom:12px;
  }
  .brand-order-bar__shop{
    flex:1;min-height:50px;display:grid;place-items:center;
    border-radius:16px;border:1.5px solid rgba(11,29,54,.12);
    font-size:14px;font-weight:800;color:#0B1D36;background:#fff;text-decoration:none;
  }
  .brand-order-bar__wa{
    flex:1.4;min-height:50px;display:inline-flex;align-items:center;justify-content:center;gap:8px;
    border-radius:16px;background:linear-gradient(135deg,#F97316,#E85D04);color:#fff;
    font-size:14px;font-weight:900;text-decoration:none;
    box-shadow:0 10px 24px -8px rgba(232,93,4,.55);
  }
  .brand-order-bar__wa:active,.brand-order-bar__shop:active{transform:scale(.97)}

  /* Hide competing FABs on brand pages (AI via المزيد؛ CTA owns brand home bottom) */
  @media(max-width:767px){
    body:has(.brand-page) #ai-fab{display:none!important}
    body:has(.brand-page--home) .brand-wa-fab{display:none!important}
    body.app-shell--no-tabs:has(.brand-page--home){
      padding-bottom:calc(68px + env(safe-area-inset-bottom,0px));
    }
  }

  /* WA FAB — above bottom tabs on shop/manufacturers */
  .brand-wa-fab{
    position:fixed;z-index:9980;
    bottom:calc(20px + env(safe-area-inset-bottom,0px));
    inset-inline-end:16px;
    width:50px;height:50px;border-radius:50%;
    background:#16a34a;color:#fff;
    display:grid;place-items:center;
    box-shadow:0 10px 28px -6px rgba(22,163,74,.55);
    transition:transform .2s;
    -webkit-tap-highlight-color:transparent;
  }
  .brand-wa-fab:active{transform:scale(.95)}
  @media(max-width:767px){
    .brand-wa-fab{bottom:calc(var(--app-nav-h,64px) + 16px + env(safe-area-inset-bottom,0px))}
  }
  @media(min-width:768px){.brand-wa-fab{width:54px;height:54px;inset-inline-end:24px;bottom:24px}}

  @media(prefers-reduced-motion:reduce){.brand-blob,.brand-hero__logo-glow,.brand-hero-enter,.product-pop{animation:none!important;opacity:1!important;transform:none!important}}
  [x-cloak]{display:none!important}
</style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
@endonce
