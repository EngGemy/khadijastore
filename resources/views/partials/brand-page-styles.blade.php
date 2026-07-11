@once
@push('head')
<style>
  .brand-mesh{position:absolute;inset:0;overflow:hidden;pointer-events:none}
  .brand-blob{position:absolute;border-radius:50%;filter:blur(60px);opacity:.45;animation:brandBlob 20s ease-in-out infinite}
  .brand-blob-2{animation:brandBlob2 24s ease-in-out infinite;animation-delay:-6s}
  @keyframes brandBlob{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(20px,-12px) scale(1.05)}}
  @keyframes brandBlob2{0%,100%{transform:translate(0,0)}50%{transform:translate(-16px,10px)}}

  .brand-hero{position:relative;overflow:hidden;color:#fff;background:linear-gradient(135deg,#0a0a0a,#111827 50%,#052e16)}
  .brand-hero__grid{position:absolute;inset:0;opacity:.04;background-image:radial-gradient(circle at 1px 1px,#fff 1px,transparent 0);background-size:22px 22px}
  .brand-hero__logo-ring{position:absolute;inset:-6px;border-radius:22px;background:conic-gradient(from 0deg,rgba(22,163,74,.75),rgba(255,255,255,.12),rgba(22,163,74,.75));animation:brandSpin 7s linear infinite}
  .brand-hero__logo-wrap{position:relative;width:64px;height:64px;flex-shrink:0}
  @media(min-width:640px){.brand-hero__logo-wrap{width:88px;height:88px}}
  @keyframes brandSpin{to{transform:rotate(360deg)}}
  .brand-hero__logo{position:relative;z-index:2;width:100%;height:100%;border-radius:18px;background:#fff;color:#0a0a0a;display:grid;place-items:center;font-weight:800;font-size:1.5rem;overflow:hidden;box-shadow:0 12px 32px -8px rgba(0,0,0,.4)}
  @media(min-width:640px){.brand-hero__logo{border-radius:22px;font-size:2rem}}
  .brand-stat-pill{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.12);border-radius:999px;padding:4px 10px;font-size:11px;font-weight:700;white-space:nowrap}

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
  .brand-search input{width:100%;border-radius:14px;border:1px solid rgba(10,10,10,.1);background:#f6f6f4;padding:12px 12px 12px 42px;font-size:13px;font-weight:600;outline:none;transition:border-color .2s,box-shadow .2s}
  .brand-search input:focus{border-color:rgba(22,163,74,.4);box-shadow:0 0 0 3px rgba(22,163,74,.1)}

  /* product cards — mobile vitality */
  .product-pop{animation:productPop .55s cubic-bezier(.16,1,.3,1) both}
  @keyframes productPop{0%{opacity:0;transform:translateY(16px) scale(.96)}100%{opacity:1;transform:translateY(0) scale(1)}}
  .product-pop:nth-child(1){animation-delay:.04s}.product-pop:nth-child(2){animation-delay:.08s}.product-pop:nth-child(3){animation-delay:.12s}.product-pop:nth-child(4){animation-delay:.16s}
  .product-pop:nth-child(5){animation-delay:.2s}.product-pop:nth-child(6){animation-delay:.24s}.product-pop:nth-child(7){animation-delay:.28s}.product-pop:nth-child(8){animation-delay:.32s}
  .brand-product-card{transition:transform .35s cubic-bezier(.16,1,.3,1),box-shadow .35s}
  .brand-product-card:active{transform:scale(.97)}

  .brand-section-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px}
  .brand-section-title{font-size:15px;font-weight:800;letter-spacing:-.02em}
  @media(min-width:640px){.brand-section-title{font-size:18px}}

  @media(prefers-reduced-motion:reduce){.brand-blob,.brand-hero__logo-ring,.product-pop{animation:none!important;opacity:1!important;transform:none!important}}
  [x-cloak]{display:none!important}
</style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
@endonce
