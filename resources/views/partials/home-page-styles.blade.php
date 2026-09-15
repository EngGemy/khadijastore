<style>
  /* ── Token-aligned homepage surfaces ─────────────────────────────── */
  .home-section{background:transparent}
  .sec-eyebrow{display:inline-flex;align-items:center;gap:.5rem;font-size:11px;font-weight:900;letter-spacing:.16em;text-transform:uppercase;color:var(--orange)}
  .sec-eyebrow::before{content:'';width:6px;height:6px;border-radius:999px;background:var(--orange-bright);flex-shrink:0}

  /* ── Store filter bar ───────────────────────────────────────────── */
  #store-brands-filter{
    background:linear-gradient(180deg,rgba(255,255,255,.98) 0%,rgba(245,247,250,.96) 100%);
    border-bottom:1px solid var(--line);
    box-shadow:0 8px 32px -12px rgba(11,29,54,.08);
  }
  #store-brands-filter .store-filter-head{
    display:flex;align-items:center;justify-content:space-between;gap:.75rem;
    padding:.65rem 1rem;border-bottom:1px solid rgba(11,29,54,.06);
  }
  #store-brands-filter .store-filter-track{
    display:flex;gap:.5rem;overflow-x:auto;overscroll-behavior-x:contain;
    padding:.75rem 1rem 1rem;scroll-snap-type:x proximity;
    -webkit-overflow-scrolling:touch;scrollbar-width:none;
  }
  #store-brands-filter .store-filter-track::-webkit-scrollbar{display:none}
  .store-chip-group{
    display:inline-flex;align-items:stretch;flex-shrink:0;scroll-snap-align:start;
    border-radius:999px;border:1.5px solid rgba(11,29,54,.1);background:#fff;
    overflow:hidden;transition:transform .22s ease,box-shadow .22s ease,border-color .22s ease;
    box-shadow:0 2px 10px rgba(11,29,54,.04);
  }
  .store-chip-group:hover{border-color:rgba(11,29,54,.22);box-shadow:0 6px 20px rgba(11,29,54,.07);transform:translateY(-1px)}
  .store-chip-group:has(.home-brand-chip.is-active){
    border-color:var(--navy);background:var(--navy);
    box-shadow:0 8px 24px rgba(11,29,54,.18);
  }
  .home-brand-chip{
    display:inline-flex;align-items:center;gap:.5rem;padding:.5rem .75rem .5rem .5rem;
    font-size:.8125rem;font-weight:800;color:rgba(11,29,54,.62);background:transparent;
    border:none;cursor:pointer;touch-action:manipulation;white-space:nowrap;
    transition:color .2s ease;
  }
  .home-brand-chip.is-active{color:#fff}
  .store-chip-group:has(.home-brand-chip.is-active) .store-chip-link{
    color:rgba(255,255,255,.75);border-inline-start-color:rgba(255,255,255,.15);
  }
  .store-chip-group:has(.home-brand-chip.is-active) .store-chip-link:hover{
    color:#fff;background:rgba(255,255,255,.1);
  }
  .store-chip-link{
    display:grid;place-items:center;width:2.25rem;flex-shrink:0;
    color:rgba(11,29,54,.38);border-inline-start:1px solid rgba(11,29,54,.08);
    transition:color .2s ease,background .2s ease;
  }
  .store-chip-link:hover{color:var(--orange);background:rgba(232,93,4,.06)}
  .store-chip-group--solo{border-radius:999px}
  .store-chip-group--solo .home-brand-chip{padding:.5rem 1rem .5rem .5rem}

  /* ── Premium product cards ──────────────────────────────────────── */
  .product-card{
    position:relative;border-radius:16px;border:1px solid rgba(11,29,54,.08);
    background:#fff;overflow:hidden;
    transition:transform .45s cubic-bezier(.16,1,.3,1),box-shadow .45s cubic-bezier(.16,1,.3,1),border-color .3s ease;
    box-shadow:0 4px 18px rgba(11,29,54,.05);
  }
  .product-card::before{
    content:'';position:absolute;inset:0;border-radius:inherit;pointer-events:none;
    background:linear-gradient(145deg,rgba(11,29,54,.03),transparent 42%);
    opacity:0;transition:opacity .35s ease;
  }
  .product-card:hover{
    transform:translateY(-6px);
    box-shadow:0 20px 48px -16px rgba(11,29,54,.14);
    border-color:rgba(232,93,4,.28);
  }
  .product-card:hover::before{opacity:1}
  .product-card__media{
    position:relative;aspect-ratio:1;overflow:hidden;
    background:linear-gradient(135deg,var(--paper-2) 0%,var(--paper-3) 100%);
  }
  .product-card__media::after{
    content:'';position:absolute;inset:0;pointer-events:none;
    background:linear-gradient(to top,rgba(11,29,54,.4) 0%,transparent 40%);
    opacity:0;transition:opacity .35s ease;
  }
  .product-card:hover .product-card__media::after{opacity:1}
  .product-card__overlay{
    position:absolute;inset-inline:0;bottom:0;padding:.75rem 1rem;
    transform:translateY(110%);transition:transform .4s cubic-bezier(.16,1,.3,1);
    z-index:5;
  }
  .product-card:hover .product-card__overlay{transform:translateY(0)}
  .product-card__cta{
    display:block;width:100%;text-align:center;padding:.55rem .75rem;
    border-radius:12px;background:#fff;color:var(--navy);font-size:.8125rem;font-weight:800;
    box-shadow:0 4px 16px rgba(11,29,54,.12);
  }
  .product-card__badge{
    position:absolute;top:.65rem;inset-inline-start:.65rem;z-index:10;
    background:var(--orange);color:#fff;font-size:.625rem;font-weight:800;
    padding:.25rem .55rem;border-radius:999px;
  }
  .product-card__discount{
    position:absolute;top:.65rem;inset-inline-end:.65rem;z-index:10;
    background:#dc2626;color:#fff;font-size:.625rem;font-weight:800;
    padding:.25rem .5rem;border-radius:999px;
    box-shadow:0 4px 12px rgba(220,38,38,.35);
  }
  .product-card__body{padding:.9rem 1rem 1rem;display:flex;flex-direction:column;gap:.35rem;flex:1}
  .product-card__brand{
    display:flex;align-items:center;gap:.4rem;font-size:.6875rem;font-weight:800;
    color:var(--orange);text-transform:none;
  }
  .product-card__title{
    font-weight:800;font-size:.9375rem;line-height:1.35;color:var(--navy);
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
  }
  .product-card__meta{font-size:.75rem;font-weight:600;color:rgba(11,29,54,.45)}
  .product-card__price-row{
    display:flex;align-items:baseline;gap:.35rem;margin-top:auto;padding-top:.5rem;flex-wrap:wrap;
  }
  .product-card__price{font-weight:900;font-size:1.25rem;letter-spacing:-.02em;color:var(--navy)}
  .product-card__compare{font-size:.75rem;color:rgba(11,29,54,.35);text-decoration:line-through}

  /* ── Offer cards ────────────────────────────────────────────────── */
  .offer-card{
    position:relative;display:flex;flex-direction:column;
    border-radius:18px;border:1px solid rgba(11,29,54,.08);background:#fff;overflow:hidden;
    box-shadow:0 6px 22px rgba(11,29,54,.06);
    transition:transform .4s cubic-bezier(.16,1,.3,1),box-shadow .4s ease,border-color .3s ease;
  }
  .offer-card::after{
    content:'';position:absolute;inset-inline:0;top:0;height:3px;
    background:linear-gradient(90deg,var(--navy),var(--orange-bright));
    opacity:0;transition:opacity .3s ease;
  }
  .offer-card:hover{
    transform:translateY(-7px);
    box-shadow:0 24px 50px -18px rgba(232,93,4,.28);
    border-color:rgba(232,93,4,.35);
  }
  .offer-card:hover::after{opacity:1}
  .offer-card__media{
    position:relative;aspect-ratio:1;overflow:hidden;
    background:linear-gradient(145deg,var(--paper-2),var(--paper-3));
  }
  .offer-card__media img{
    width:100%;height:100%;object-fit:cover;
    transition:transform .6s cubic-bezier(.16,1,.3,1);
  }
  .offer-card:hover .offer-card__media img{transform:scale(1.06)}
  .offer-card__pct{
    position:absolute;top:.7rem;inset-inline-start:.7rem;z-index:5;
    background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;
    font-size:.7rem;font-weight:900;padding:.35rem .6rem;border-radius:999px;
    box-shadow:0 6px 16px rgba(220,38,38,.4);
  }
  .offer-card__badge{
    position:absolute;top:.7rem;inset-inline-start:.7rem;z-index:5;
    background:var(--orange);color:#fff;font-size:.65rem;font-weight:800;
    padding:.3rem .55rem;border-radius:999px;
  }
  .offer-card__overlay{
    position:absolute;inset-inline:0;bottom:0;padding:.75rem;
    transform:translateY(110%);transition:transform .4s cubic-bezier(.16,1,.3,1);z-index:4;
  }
  .offer-card:hover .offer-card__overlay{transform:translateY(0)}
  .offer-card__cta{
    display:block;text-align:center;padding:.55rem;border-radius:12px;
    background:var(--navy);color:#fff;font-size:.75rem;font-weight:800;
  }
  .offer-card__body{padding:.85rem 1rem 1.05rem;display:flex;flex-direction:column;gap:.3rem;flex:1}
  .offer-card__brand{font-size:.65rem;font-weight:800;color:var(--orange);letter-spacing:.02em}
  .offer-card__title{
    font-weight:800;font-size:.9rem;line-height:1.35;color:var(--navy);
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
  }
  .offer-card__prices{display:flex;align-items:baseline;gap:.4rem;flex-wrap:wrap;margin-top:.35rem}
  .offer-card__price{font-weight:900;font-size:1.2rem;color:var(--navy);letter-spacing:-.02em}
  .offer-card__price small{font-size:.65rem;font-weight:700;color:rgba(11,29,54,.5)}
  .offer-card__compare{font-size:.75rem;color:rgba(11,29,54,.35);text-decoration:line-through}
  .offer-card__save{
    margin-top:.15rem;font-size:.7rem;font-weight:800;color:#16a34a;
    background:rgba(22,163,74,.08);border-radius:999px;padding:.2rem .55rem;width:fit-content;
  }

  /* ── Brand grid cards ───────────────────────────────────────────── */
  .brand-store-card{
    position:relative;border-radius:18px;border:1px solid rgba(11,29,54,.08);
    background:#fff;overflow:hidden;padding:1.5rem;
    display:flex;flex-direction:column;gap:1rem;
    transition:transform .45s cubic-bezier(.16,1,.3,1),box-shadow .45s ease,border-color .3s ease;
    box-shadow:0 4px 18px rgba(11,29,54,.05);
  }
  .brand-store-card::before{
    content:'';position:absolute;top:0;inset-inline:0;height:3px;
    background:linear-gradient(90deg,var(--navy),var(--orange));opacity:0;transition:opacity .3s ease;
  }
  .brand-store-card:hover{
    transform:translateY(-5px);
    box-shadow:0 22px 50px -18px rgba(11,29,54,.14);
    border-color:rgba(11,29,54,.18);
  }
  .brand-store-card:hover::before{opacity:1}
  .brand-store-card__footer{
    display:flex;align-items:center;justify-content:space-between;
    margin-top:auto;padding-top:1rem;border-top:1px solid rgba(11,29,54,.08);
  }
  .brand-store-card__cta{
    display:inline-flex;align-items:center;gap:.35rem;font-size:.8125rem;font-weight:800;color:var(--navy);
    transition:gap .25s ease,color .2s ease;
  }
  .brand-store-card:hover .brand-store-card__cta{gap:.65rem;color:var(--orange)}

  /* ── Marquee pills ──────────────────────────────────────────────── */
  .brand-marquee-pill{
    display:inline-flex;align-items:center;gap:.55rem;padding:.55rem 1rem;
    border-radius:999px;border:1px solid rgba(11,29,54,.1);background:#fff;
    font-weight:800;font-size:.875rem;white-space:nowrap;
    transition:transform .25s ease,border-color .25s ease,box-shadow .25s ease;
    box-shadow:0 2px 8px rgba(11,29,54,.04);
  }
  .brand-marquee-pill:hover{
    transform:translateY(-2px);border-color:rgba(232,93,4,.35);
    box-shadow:0 8px 20px rgba(232,93,4,.12);
  }

  /* Image size hints (admin/dev tooling via title attrs) */
  .brand-avatar[data-rec-size]::after{content:none}

  /* ── Store shelves (متجر + منتجاته) ──────────────────────────────── */
  .store-shelves{display:flex;flex-direction:column;gap:1.1rem}
  @media(min-width:640px){.store-shelves{gap:1.5rem}}
  .store-shelf{
    background:#fff;border:1px solid rgba(11,29,54,.08);border-radius:20px;
    overflow:hidden;box-shadow:0 8px 28px -16px rgba(11,29,54,.18);
    transition:border-color .25s ease,box-shadow .25s ease;
  }
  .store-shelf--featured{
    border-color:rgba(232,93,4,.28);
    box-shadow:0 12px 36px -16px rgba(232,93,4,.28);
  }
  .store-shelf__head{
    display:flex;align-items:center;justify-content:space-between;gap:.75rem;
    padding:.85rem 1rem;background:linear-gradient(180deg,#fff 0%,#f7f9fc 100%);
    border-bottom:1px solid rgba(11,29,54,.06);
  }
  .store-shelf--featured .store-shelf__head{
    background:linear-gradient(135deg,rgba(11,29,54,.96),#152a45 55%,rgba(232,93,4,.85));
  }
  .store-shelf--featured .store-shelf__name,
  .store-shelf--featured .store-shelf__meta{color:#fff}
  .store-shelf--featured .store-shelf__meta{color:rgba(255,255,255,.72)}
  .store-shelf__brand{
    display:flex;align-items:center;gap:.7rem;min-width:0;flex:1;
    text-decoration:none;color:inherit;
  }
  .store-shelf__brand-text{min-width:0}
  .store-shelf__name-row{display:flex;align-items:center;gap:.4rem;min-width:0}
  .store-shelf__name{
    margin:0;font-size:.95rem;font-weight:900;letter-spacing:-.01em;color:var(--navy);
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
  }
  .store-shelf__pill{
    flex-shrink:0;font-size:9px;font-weight:900;letter-spacing:.04em;
    padding:.15rem .45rem;border-radius:999px;background:rgba(249,115,22,.95);color:#fff;
  }
  .store-shelf__meta{
    display:flex;align-items:center;gap:.3rem;margin:.2rem 0 0;
    font-size:11px;font-weight:700;color:rgba(11,29,54,.45);
  }
  .store-shelf__cat{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:10rem}
  .store-shelf__dot{opacity:.45}
  .store-shelf__actions{display:flex;align-items:center;gap:.4rem;flex-shrink:0}
  .store-shelf__cta{
    display:inline-flex;align-items:center;justify-content:center;gap:.3rem;
    min-height:40px;padding:0 .9rem;border-radius:999px;
    background:var(--navy);color:#fff;font-size:12px;font-weight:800;
    text-decoration:none;transition:transform .2s ease,background .2s ease;
  }
  .store-shelf__cta:hover{background:var(--orange);transform:translateY(-1px)}
  .store-shelf--featured .store-shelf__cta{background:#fff;color:var(--navy)}
  .store-shelf--featured .store-shelf__cta:hover{background:var(--orange);color:#fff}
  .store-shelf__wa{
    display:grid;place-items:center;width:40px;height:40px;border-radius:999px;
    background:#25D366;color:#fff;text-decoration:none;
    box-shadow:0 6px 16px rgba(37,211,102,.35);
    transition:transform .2s ease;
  }
  .store-shelf__wa:hover{transform:scale(1.06)}
  .store-shelf__rail{
    display:flex;gap:.7rem;overflow-x:auto;overscroll-behavior-x:contain;
    scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;
    padding:.9rem 1rem 1.05rem;scrollbar-width:none;
  }
  .store-shelf__rail::-webkit-scrollbar{display:none}

  .shelf-card{
    flex:0 0 auto;width:148px;scroll-snap-align:start;
    display:flex;flex-direction:column;
    border-radius:16px;border:1px solid rgba(11,29,54,.08);background:#fff;
    overflow:hidden;text-decoration:none;color:inherit;
    box-shadow:0 4px 14px rgba(11,29,54,.05);
    transition:transform .3s cubic-bezier(.16,1,.3,1),box-shadow .3s ease,border-color .2s ease;
  }
  .shelf-card:hover{
    transform:translateY(-3px);border-color:rgba(232,93,4,.3);
    box-shadow:0 14px 30px -14px rgba(11,29,54,.2);
  }
  .shelf-card:active{transform:scale(.98)}
  .shelf-card__media{
    position:relative;aspect-ratio:1;overflow:hidden;
    background:linear-gradient(145deg,var(--paper-2),var(--paper-3));
  }
  .shelf-card__media .product-cover{position:absolute;inset:0;width:100%;height:100%}
  .shelf-card__media img{width:100%;height:100%;object-fit:cover;display:block}
  .shelf-card__media .product-cover__fallback{font-size:1.5rem}
  .shelf-card__badge,.shelf-card__discount{
    position:absolute;z-index:2;font-size:9px;font-weight:900;
    padding:.2rem .4rem;border-radius:999px;color:#fff;
  }
  .shelf-card__badge{top:6px;inset-inline-start:6px;background:var(--orange)}
  .shelf-card__discount{top:6px;inset-inline-end:6px;background:#dc2626}
  .shelf-card__oos{
    position:absolute;inset:0;z-index:3;display:grid;place-items:center;
    background:rgba(255,255,255,.82);font-size:11px;font-weight:900;color:#b91c1c;
  }
  .shelf-card__body{padding:.55rem .6rem .7rem;display:flex;flex-direction:column;gap:.25rem;flex:1}
  .shelf-card__title{
    margin:0;font-size:12px;font-weight:800;line-height:1.35;color:var(--navy);
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
    min-height:2.6em;
  }
  .shelf-card__price-row{display:flex;align-items:baseline;gap:.3rem;flex-wrap:wrap;margin-top:auto}
  .shelf-card__price{font-size:15px;font-weight:900;color:var(--navy);letter-spacing:-.02em}
  .shelf-card__price small{font-size:9px;font-weight:700;margin-inline-start:2px;color:rgba(11,29,54,.45)}
  .shelf-card__compare{font-size:10px;font-weight:700;color:rgba(11,29,54,.35);text-decoration:line-through}

  .shelf-more{
    flex:0 0 auto;width:112px;scroll-snap-align:start;
    display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.55rem;
    border-radius:16px;border:1.5px dashed rgba(11,29,54,.16);
    background:linear-gradient(180deg,#f8fafc,#f1f5f9);
    text-decoration:none;color:var(--navy);min-height:100%;
    transition:border-color .2s ease,background .2s ease,transform .2s ease;
  }
  .shelf-more:hover{border-color:var(--orange);background:#fff7ed;transform:translateY(-2px)}
  .shelf-more__icon{
    width:42px;height:42px;border-radius:999px;display:grid;place-items:center;
    background:var(--navy);color:#fff;
  }
  .shelf-more__label{font-size:11px;font-weight:900;text-align:center;padding:0 .4rem}

  @media(min-width:640px){
    .store-shelf__head{padding:1rem 1.15rem}
    .store-shelf__name{font-size:1.05rem}
    .store-shelf__rail{gap:.85rem;padding:1rem 1.15rem 1.2rem}
    .shelf-card{width:172px;border-radius:18px}
    .shelf-card__title{font-size:13px}
    .shelf-card__price{font-size:17px}
    .shelf-more{width:128px}
  }
  @media(min-width:1024px){
    .shelf-card{width:188px}
    .store-shelf__rail{scrollbar-width:thin}
  }

  /* ── Featured brand spotlight (سند) ──────────────────────────────── */
  .sanad-spotlight{padding-block:clamp(28px,7vw,52px)}
  .sanad-spotlight__logo{
    width:56px;height:56px;border-radius:16px;background:#fff;
    display:grid;place-items:center;overflow:hidden;
    box-shadow:0 10px 28px -12px rgba(0,0,0,.45);
    border:2px solid rgba(249,115,22,.35);
  }
  .sanad-spotlight__card{
    display:flex;flex-direction:column;border-radius:16px;overflow:hidden;
    background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);
    transition:transform .3s cubic-bezier(.16,1,.3,1),background .25s ease,border-color .25s ease;
  }
  .sanad-spotlight__card:hover{
    transform:translateY(-3px);background:rgba(255,255,255,.11);
    border-color:rgba(249,115,22,.4);
  }
  .sanad-spotlight__media{
    position:relative;aspect-ratio:1;background:rgba(255,255,255,.92);
    overflow:hidden;
  }
  .sanad-spotlight__media img{width:100%;height:100%;object-fit:cover}
  .sanad-spotlight__badge{
    position:absolute;top:8px;inset-inline-start:8px;z-index:2;
    background:#E85D04;color:#fff;font-size:10px;font-weight:800;
    padding:3px 7px;border-radius:999px;
  }
  .sanad-spotlight__badge--soft{background:rgba(11,29,54,.85)}
  .sanad-spotlight__body{padding:.7rem .75rem .85rem}
  .sanad-spotlight__title{
    font-size:.8125rem;font-weight:800;color:#fff;line-height:1.35;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
    min-height:2.2em;
  }
  .sanad-spotlight__price-row{display:flex;align-items:baseline;gap:.3rem;margin-top:.4rem}
  .sanad-spotlight__price{font-size:1.05rem;font-weight:900;color:#F97316}
  .sanad-spotlight__compare{font-size:11px;font-weight:700;color:rgba(255,255,255,.4);text-decoration:line-through}

  /* ── Products listing chips ─────────────────────────────────────── */
  .plp-brand-chip{
    display:inline-flex;align-items:center;gap:.45rem;flex-shrink:0;
    min-height:40px;padding:.45rem .9rem;border-radius:999px;
    border:1.5px solid rgba(11,29,54,.1);background:#fff;
    font-size:12px;font-weight:800;color:rgba(11,29,54,.55);
    white-space:nowrap;transition:all .2s ease;
  }
  .plp-brand-chip:hover{border-color:rgba(11,29,54,.25);color:var(--navy)}
  .plp-brand-chip.is-active{
    background:var(--navy);border-color:var(--navy);color:#fff;
    box-shadow:0 8px 20px rgba(11,29,54,.18);
  }
  .plp-brand-chip--featured{
    border-color:rgba(232,93,4,.35);color:var(--orange);
    background:rgba(232,93,4,.06);
  }
  .plp-brand-chip--featured.is-active{
    background:linear-gradient(135deg,#0B1D36,#132a4a);
    border-color:#0B1D36;color:#fff;
  }

  @media (max-width:639px){
    .offer-card__overlay{display:none}
    .product-card__overlay{display:none}
    .product-card:active{transform:scale(.98)}
    .offer-card:active{transform:scale(.98)}
    .brand-store-card:active{transform:scale(.99)}
    .sanad-spotlight__card:active{transform:scale(.98)}
    .product-card__media{border-radius:14px 14px 0 0}
    .offer-card{border-radius:16px}
    .home-section .sec-eyebrow{font-size:10px}
  }
  @media (max-width:767px){
    #store-brands-filter{top:56px}
    #brands,#products,#offers,#directory-teasers,#cats,#store-brands-filter,#letters,#features,#sanad-spotlight{scroll-margin-top:72px}
  }
  @media (prefers-reduced-motion:reduce){
    .offer-card:hover,.product-card:hover,.brand-store-card:hover,.sanad-spotlight__card:hover{transform:none}
  }
</style>
