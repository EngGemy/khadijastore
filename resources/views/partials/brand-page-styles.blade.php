@once
@push('head')
<style>
  .brand-mesh{position:absolute;inset:0;overflow:hidden;pointer-events:none}
  .brand-blob{position:absolute;border-radius:50%;filter:blur(60px);opacity:.45;animation:brandBlob 20s ease-in-out infinite}
  .brand-blob-2{animation:brandBlob2 24s ease-in-out infinite;animation-delay:-6s}
  @keyframes brandBlob{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(20px,-12px) scale(1.05)}}
  @keyframes brandBlob2{0%,100%{transform:translate(0,0)}50%{transform:translate(-16px,10px)}}

  /* ── Hero premium ── */
  .brand-hero{position:relative;overflow:hidden;color:#fff;padding:14px 0 16px}
  @media(min-width:640px){.brand-hero{padding:24px 0 28px}}
  .brand-hero--compact{padding:12px 0 14px}
  .brand-hero__bg{position:absolute;inset:0;background:linear-gradient(145deg,#041a0c 0%,#0c1f3d 42%,#052e16 100%)}
  .brand-hero__orb{position:absolute;border-radius:50%;filter:blur(48px);opacity:.55;animation:heroOrb 14s ease-in-out infinite}
  .brand-hero__orb--1{width:200px;height:200px;top:-40px;inset-inline-start:-30px;background:#16a34a}
  .brand-hero__orb--2{width:160px;height:160px;bottom:-20px;inset-inline-end:10%;background:#6366f1;animation-delay:-4s}
  .brand-hero__orb--3{width:120px;height:120px;top:30%;inset-inline-end:-20px;background:#f59e0b;opacity:.35;animation-delay:-8s}
  @keyframes heroOrb{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(12px,-8px) scale(1.08)}}
  .brand-hero__shine{position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 50% -10%,rgba(255,255,255,.14),transparent 60%)}
  .brand-hero__grid{position:absolute;inset:0;opacity:.05;background-image:radial-gradient(circle at 1px 1px,#fff 1px,transparent 0);background-size:20px 20px}
  .brand-hero__inner{position:relative;z-index:2;max-width:1180px;margin:0 auto;padding:0 14px}
  @media(min-width:640px){.brand-hero__inner{padding:0 20px}}
  .brand-hero__glass{
    background:rgba(255,255,255,.07);
    border:1px solid rgba(255,255,255,.12);
    backdrop-filter:blur(16px);
    -webkit-backdrop-filter:blur(16px);
    border-radius:20px;
    padding:16px;
    box-shadow:0 20px 50px -20px rgba(0,0,0,.45),inset 0 1px 0 rgba(255,255,255,.1);
  }
  @media(min-width:640px){.brand-hero__glass{padding:22px 24px;border-radius:24px}}
  .brand-hero--compact .brand-hero__glass{padding:12px 14px;border-radius:16px}

  .brand-hero__profile{display:flex;align-items:center;gap:14px;min-width:0}
  @media(max-width:639px){
    .brand-hero__profile{flex-direction:column;text-align:center;align-items:center;gap:12px}
    .brand-hero__stats{justify-content:center}
  }
  .brand-hero__info{min-width:0;flex:1}
  @media(max-width:639px){.brand-hero__info{width:100%}}

  .brand-hero__logo-stage{position:relative;width:80px;height:80px;flex-shrink:0}
  @media(min-width:640px){.brand-hero__logo-stage{width:92px;height:92px}}
  .brand-hero--compact .brand-hero__logo-stage{width:56px;height:56px}
  .brand-hero__logo-glow{
    position:absolute;inset:-8px;border-radius:50%;
    background:conic-gradient(from 180deg,#16a34a,#a7f3d0,#6366f1,#16a34a);
    opacity:.75;animation:heroGlow 6s linear infinite;
  }
  @keyframes heroGlow{to{transform:rotate(360deg)}}
  .brand-hero__logo-frame{
    position:relative;z-index:2;width:100%;height:100%;border-radius:50%;
    background:linear-gradient(180deg,#fff 0%,#f0fdf4 100%);
    display:flex;align-items:center;justify-content:center;
    overflow:hidden;
    box-shadow:0 8px 24px -6px rgba(0,0,0,.35);
    border:3px solid rgba(255,255,255,.9);
  }
  .brand-hero__logo-img{
    width:72%;height:72%;max-width:72%;max-height:72%;
    object-fit:contain;object-position:center;display:block;
  }
  .brand-hero__mark{font-weight:900;font-size:1.75rem;line-height:1;color:#052e16}
  .brand-hero--compact .brand-hero__mark{font-size:1.25rem}

  .brand-hero__cat{
    display:inline-block;font-size:10px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;
    color:#86efac;background:rgba(22,163,74,.2);border:1px solid rgba(134,239,172,.35);
    border-radius:999px;padding:4px 10px;margin-bottom:6px;
  }
  .brand-hero__title{font-weight:900;letter-spacing:-.03em;line-height:1.15;margin:0;font-size:clamp(19px,5vw,34px)}
  .brand-hero__desc{margin:6px 0 0;font-size:12px;line-height:1.55;color:rgba(255,255,255,.72);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  @media(min-width:640px){.brand-hero__desc{font-size:13px;-webkit-line-clamp:3}}
  .brand-hero__stats{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px}

  .brand-hero__actions{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:14px}
  @media(max-width:359px){.brand-hero__actions{grid-template-columns:1fr}}
  @media(min-width:640px){.brand-hero__actions{display:flex;gap:10px;margin-top:16px}}
  .brand-hero__btn{
    display:inline-flex;align-items:center;justify-content:center;gap:6px;
    min-height:44px;padding:10px 16px;border-radius:14px;font-size:13px;font-weight:800;
    text-decoration:none;transition:transform .25s cubic-bezier(.16,1,.3,1),box-shadow .25s;
  }
  .brand-hero__btn:active{transform:scale(.96)}
  .brand-hero__btn--primary{background:#fff;color:#052e16;box-shadow:0 4px 14px rgba(0,0,0,.15)}
  .brand-hero__btn--primary:hover{box-shadow:0 8px 20px rgba(0,0,0,.2)}
  .brand-hero__btn--wa{background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;box-shadow:0 4px 16px rgba(22,163,74,.4)}

  .brand-hero-enter{animation:heroEnter .7s cubic-bezier(.16,1,.3,1) both}
  .brand-hero-enter--2{animation-delay:.08s}
  .brand-hero-enter--3{animation-delay:.14s}
  .brand-hero-enter--4{animation-delay:.2s}
  @keyframes heroEnter{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}

  .brand-stat-pill{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.15);border-radius:999px;padding:4px 10px;font-size:11px;font-weight:700;white-space:nowrap}

  .brand-nav-pill{position:relative;overflow:hidden;white-space:nowrap}
  .brand-nav-pill.is-active{box-shadow:0 4px 14px -4px rgba(0,0,0,.25)}

  /* share icon buttons */
  .brand-icon-btn{width:36px;height:36px;border-radius:12px;border:1px solid rgba(10,10,10,.1);background:#fff;color:#0a0a0a;display:grid;place-items:center;transition:transform .25s,box-shadow .25s,background .25s}
  .brand-icon-btn:active{transform:scale(.92)}
  .brand-icon-btn.is-success{background:#16a34a;color:#fff;border-color:#16a34a}
  .brand-icon-btn--wa{background:#16a34a;color:#fff;border-color:#16a34a}
  .brand-icon-btn--fb{background:#1877f2;color:#fff;border-color:#1877f2}

  /* category / mfg chips — mobile first */
  .brand-cat-chip,.brand-mfg-chip{display:flex;flex-direction:column;align-items:center;width:72px;text-decoration:none;color:inherit}
  @media(min-width:640px){.brand-cat-chip,.brand-mfg-chip{width:80px}}
  .brand-cat-chip__icon,.brand-mfg-chip__icon{position:relative;width:60px;height:60px;border-radius:50%;overflow:hidden;display:grid;place-items:center;font-weight:800;font-size:18px;color:#fff;background:var(--chip-color,#16a34a);transition:transform .35s cubic-bezier(.16,1,.3,1),box-shadow .35s}
  @media(min-width:640px){.brand-cat-chip__icon,.brand-mfg-chip__icon{width:68px;height:68px}}
  .brand-cat-chip:active .brand-cat-chip__icon,.brand-mfg-chip:active .brand-mfg-chip__icon{transform:scale(.92)}
  .brand-cat-chip__ring{position:absolute;inset:-3px;border-radius:50%;border:2px solid var(--chip-color,#16a34a);opacity:0;transform:scale(.85);transition:opacity .3s,transform .3s}
  .brand-cat-chip__icon img{border-radius:50%}
  .brand-cat-chip__label,.brand-mfg-chip__label{margin-top:8px;font-size:10px;font-weight:700;text-align:center;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;width:100%}
  @media(min-width:640px){.brand-cat-chip__label,.brand-mfg-chip__label{font-size:11px}}
  .brand-cat-chip__count{font-size:9px;font-weight:600;color:rgba(10,10,10,.4);margin-top:2px}
  .brand-mfg-chip__icon{background:linear-gradient(135deg,var(--from,#16a34a),var(--to,#059669))}
  .brand-mfg-card{transition:transform .35s cubic-bezier(.16,1,.3,1),box-shadow .35s}
  .brand-mfg-card:hover{transform:translateY(-4px);box-shadow:0 20px 40px -12px rgba(0,0,0,.2)}

  .brand-chip-scroll{display:flex;gap:14px;overflow-x:auto;padding:4px 2px 8px;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;scrollbar-width:none}
  .brand-chip-scroll::-webkit-scrollbar{display:none}

  .brand-search{position:relative}
  .brand-search input{width:100%;border-radius:14px;border:1px solid rgba(10,10,10,.1);background:#f6f6f4;padding:12px;padding-inline-start:42px;font-size:16px;font-weight:600;outline:none;transition:border-color .2s,box-shadow .2s;-webkit-appearance:none}
  @media(min-width:640px){.brand-search input{font-size:13px}}
  .brand-search input:focus{border-color:rgba(22,163,74,.4);box-shadow:0 0 0 3px rgba(22,163,74,.1)}

  /* product cards — mobile vitality */
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

  .brand-section-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px}
  .brand-section-title{font-size:15px;font-weight:800;letter-spacing:-.02em}
  @media(min-width:640px){.brand-section-title{font-size:18px}}

  /* مساحة آمنة أسفل الصفحة (أزرار عائمة + شريط iOS) */
  .brand-safe-bottom{padding-bottom:calc(88px + env(safe-area-inset-bottom,0px))}
  .brand-product-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
  @media(min-width:640px){.brand-product-grid{gap:16px}}
  @media(min-width:768px){.brand-product-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
  @media(min-width:1024px){.brand-product-grid{grid-template-columns:repeat(4,minmax(0,1fr))}}

  /* زر واتساب عائم — يسار الشاشة في RTL (بعيد عن المساعد الذكي) */
  .brand-wa-fab{
    position:fixed;z-index:9980;
    bottom:calc(20px + env(safe-area-inset-bottom,0px));
    inset-inline-end:16px;
    width:50px;height:50px;border-radius:50%;
    background:#16a34a;color:#fff;
    display:grid;place-items:center;
    box-shadow:0 10px 28px -6px rgba(22,163,74,.55);
    transition:transform .2s;
  }
  .brand-wa-fab:active{transform:scale(.95)}
  @media(min-width:640px){.brand-wa-fab{width:54px;height:54px;inset-inline-end:24px;bottom:24px}}

  @media(prefers-reduced-motion:reduce){.brand-blob,.brand-hero__logo-glow,.brand-hero-enter,.product-pop{animation:none!important;opacity:1!important;transform:none!important}}
  [x-cloak]{display:none!important}
</style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
@endonce
