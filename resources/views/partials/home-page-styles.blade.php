<style>
  /* ── Store filter bar ───────────────────────────────────────────── */
  #store-brands-filter{
    background:linear-gradient(180deg,rgba(255,255,255,.98) 0%,rgba(246,246,244,.96) 100%);
    border-bottom:1px solid rgba(10,10,10,.08);
    box-shadow:0 8px 32px -12px rgba(0,0,0,.08);
  }
  #store-brands-filter .store-filter-head{
    display:flex;align-items:center;justify-content:space-between;gap:.75rem;
    padding:.65rem 1rem;border-bottom:1px solid rgba(10,10,10,.06);
  }
  #store-brands-filter .store-filter-track{
    display:flex;gap:.5rem;overflow-x:auto;overscroll-behavior-x:contain;
    padding:.75rem 1rem 1rem;scroll-snap-type:x proximity;
    -webkit-overflow-scrolling:touch;scrollbar-width:none;
  }
  #store-brands-filter .store-filter-track::-webkit-scrollbar{display:none}
  .store-chip-group{
    display:inline-flex;align-items:stretch;flex-shrink:0;scroll-snap-align:start;
    border-radius:999px;border:1.5px solid rgba(10,10,10,.1);background:#fff;
    overflow:hidden;transition:transform .22s ease,box-shadow .22s ease,border-color .22s ease;
    box-shadow:0 2px 10px rgba(0,0,0,.04);
  }
  .store-chip-group:hover{border-color:rgba(10,10,10,.22);box-shadow:0 6px 20px rgba(0,0,0,.07)}
  .store-chip-group:has(.home-brand-chip.is-active){
    border-color:#0a0a0a;background:#0a0a0a;
    box-shadow:0 8px 24px rgba(10,10,10,.18);
  }
  .home-brand-chip{
    display:inline-flex;align-items:center;gap:.5rem;padding:.5rem .75rem .5rem .5rem;
    font-size:.8125rem;font-weight:800;color:rgba(10,10,10,.62);background:transparent;
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
    color:rgba(10,10,10,.38);border-inline-start:1px solid rgba(10,10,10,.08);
    transition:color .2s ease,background .2s ease;
  }
  .store-chip-link:hover{color:#16a34a;background:rgba(22,163,74,.06)}
  .store-chip-group--solo{border-radius:999px}
  .store-chip-group--solo .home-brand-chip{padding:.5rem 1rem .5rem .5rem}

  /* ── Premium product cards ──────────────────────────────────────── */
  .product-card{
    position:relative;border-radius:20px;border:1px solid rgba(10,10,10,.08);
    background:#fff;overflow:hidden;
    transition:transform .45s cubic-bezier(.16,1,.3,1),box-shadow .45s cubic-bezier(.16,1,.3,1),border-color .3s ease;
    box-shadow:0 2px 12px rgba(0,0,0,.04);
  }
  .product-card::before{
    content:'';position:absolute;inset:0;border-radius:inherit;pointer-events:none;
    background:linear-gradient(145deg,rgba(22,163,74,.04),transparent 42%);
    opacity:0;transition:opacity .35s ease;
  }
  .product-card:hover{
    transform:translateY(-6px);
    box-shadow:0 20px 48px -16px rgba(0,0,0,.14);
    border-color:rgba(10,10,10,.12);
  }
  .product-card:hover::before{opacity:1}
  .product-card__media{
    position:relative;aspect-ratio:1;overflow:hidden;
    background:linear-gradient(135deg,#f6f6f4 0%,#ededea 100%);
  }
  .product-card__media::after{
    content:'';position:absolute;inset:0;pointer-events:none;
    background:linear-gradient(to top,rgba(10,10,10,.35) 0%,transparent 38%);
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
    border-radius:12px;background:#fff;color:#0a0a0a;font-size:.8125rem;font-weight:800;
    box-shadow:0 4px 16px rgba(0,0,0,.12);
  }
  .product-card__badge{
    position:absolute;top:.65rem;inset-inline-start:.65rem;z-index:10;
    background:#0a0a0a;color:#fff;font-size:.625rem;font-weight:800;
    padding:.25rem .55rem;border-radius:999px;
  }
  .product-card__discount{
    position:absolute;top:.65rem;inset-inline-end:.65rem;z-index:10;
    background:#dc2626;color:#fff;font-size:.625rem;font-weight:800;
    padding:.25rem .5rem;border-radius:999px;
  }
  .product-card__body{padding:.9rem 1rem 1rem;display:flex;flex-direction:column;gap:.35rem;flex:1}
  .product-card__brand{
    display:flex;align-items:center;gap:.4rem;font-size:.6875rem;font-weight:800;
    color:#15803d;text-transform:none;
  }
  .product-card__title{
    font-weight:800;font-size:.9375rem;line-height:1.35;color:#0a0a0a;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
  }
  .product-card__meta{font-size:.75rem;font-weight:600;color:rgba(10,10,10,.45)}
  .product-card__price-row{
    display:flex;align-items:baseline;gap:.35rem;margin-top:auto;padding-top:.5rem;flex-wrap:wrap;
  }
  .product-card__price{font-weight:900;font-size:1.25rem;letter-spacing:-.02em;color:#0a0a0a}
  .product-card__compare{font-size:.75rem;color:rgba(10,10,10,.35);text-decoration:line-through}

  /* ── Brand grid cards ───────────────────────────────────────────── */
  .brand-store-card{
    position:relative;border-radius:22px;border:1px solid rgba(10,10,10,.08);
    background:#fff;overflow:hidden;padding:1.5rem;
    display:flex;flex-direction:column;gap:1rem;
    transition:transform .45s cubic-bezier(.16,1,.3,1),box-shadow .45s ease,border-color .3s ease;
    box-shadow:0 2px 14px rgba(0,0,0,.04);
  }
  .brand-store-card::before{
    content:'';position:absolute;top:0;inset-inline:0;height:3px;
    background:linear-gradient(90deg,#16a34a,#22c55e);opacity:0;transition:opacity .3s ease;
  }
  .brand-store-card:hover{
    transform:translateY(-5px);
    box-shadow:0 22px 50px -18px rgba(0,0,0,.14);
    border-color:rgba(22,163,74,.25);
  }
  .brand-store-card:hover::before{opacity:1}
  .brand-store-card__footer{
    display:flex;align-items:center;justify-content:space-between;
    margin-top:auto;padding-top:1rem;border-top:1px solid rgba(10,10,10,.08);
  }
  .brand-store-card__cta{
    display:inline-flex;align-items:center;gap:.35rem;font-size:.8125rem;font-weight:800;color:#15803d;
    transition:gap .25s ease;
  }
  .brand-store-card:hover .brand-store-card__cta{gap:.65rem}

  /* ── Marquee pills ──────────────────────────────────────────────── */
  .brand-marquee-pill{
    display:inline-flex;align-items:center;gap:.55rem;padding:.55rem 1rem;
    border-radius:999px;border:1px solid rgba(10,10,10,.1);background:#fff;
    font-weight:800;font-size:.875rem;white-space:nowrap;
    transition:transform .25s ease,border-color .25s ease,box-shadow .25s ease;
    box-shadow:0 2px 8px rgba(0,0,0,.04);
  }
  .brand-marquee-pill:hover{
    transform:translateY(-2px);border-color:rgba(10,10,10,.2);
    box-shadow:0 8px 20px rgba(0,0,0,.08);
  }
</style>
