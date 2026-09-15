@php
  $banners = $banners ?? collect();
  $variant = $variant ?? 'home'; // home | brand | dept
@endphp

@if($banners->isNotEmpty())
@once
@push('head')
<style>
  .section-promo{margin:0}
  .section-promo--home{padding:8px 0 4px}
  .section-promo--brand{padding:4px 0 8px}
  .section-promo__inner{max-width:1180px;margin-inline:auto;padding:0 16px}
  @media(min-width:640px){.section-promo__inner{padding:0 20px}}
  .section-promo__track{
    display:flex;gap:12px;overflow-x:auto;overscroll-behavior-x:contain;
    scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;
    scrollbar-width:none;padding-bottom:4px;margin-inline:-4px;padding-inline:4px;
  }
  .section-promo__track::-webkit-scrollbar{display:none}
  .section-promo-card{
    position:relative;display:flex;align-items:stretch;overflow:hidden;
    border-radius:20px;min-height:132px;scroll-snap-align:start;
    background:linear-gradient(135deg,#061224 0%,#0B1D36 55%,#152A45 100%);
    color:#fff;text-decoration:none;flex-shrink:0;
    width:min(88vw,420px);box-shadow:0 12px 32px -16px rgba(11,29,54,.45);
    transition:transform .25s ease,box-shadow .25s ease;
  }
  .section-promo-card--solo{width:100%;min-height:148px}
  @media(min-width:768px){
    .section-promo-card{width:min(46%,520px);min-height:160px;border-radius:24px}
    .section-promo-card--solo{width:100%;min-height:180px}
  }
  .section-promo-card:hover{transform:translateY(-2px);box-shadow:0 18px 40px -16px rgba(11,29,54,.5)}
  .section-promo-card__media{position:absolute;inset:0}
  .section-promo-card__media img{width:100%;height:100%;object-fit:cover}
  .section-promo-card__shade{
    position:absolute;inset:0;
    background:linear-gradient(90deg,rgba(6,18,36,.88) 0%,rgba(11,29,54,.55) 55%,rgba(11,29,54,.25) 100%);
  }
  [dir=rtl] .section-promo-card__shade{
    background:linear-gradient(270deg,rgba(6,18,36,.88) 0%,rgba(11,29,54,.55) 55%,rgba(11,29,54,.25) 100%);
  }
  .section-promo-card__body{
    position:relative;z-index:2;display:flex;flex-direction:column;justify-content:center;
    gap:8px;padding:16px 18px;max-width:72%;
  }
  @media(min-width:768px){.section-promo-card__body{padding:22px 26px;max-width:60%}}
  .section-promo-card__kicker{
    font-size:10px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#F97316;
  }
  .section-promo-card__title{
    font-size:clamp(16px,3.4vw,22px);font-weight:900;line-height:1.25;letter-spacing:-.02em;margin:0;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
  }
  .section-promo-card__cta{
    display:inline-flex;align-items:center;gap:6px;align-self:flex-start;
    margin-top:4px;min-height:36px;padding:0 14px;border-radius:999px;
    background:#E85D04;color:#fff;font-size:12px;font-weight:800;
    box-shadow:0 8px 20px -8px rgba(232,93,4,.55);
  }
  .section-promo-card__orb{
    position:absolute;width:140px;height:140px;border-radius:50%;filter:blur(28px);opacity:.45;
    inset-inline-end:-20px;bottom:-30px;background:#E85D04;pointer-events:none;
  }
  @media(max-width:639px){
    .section-promo-card{border-radius:18px;min-height:124px}
    .section-promo-card__body{padding:14px 16px;max-width:78%}
    .section-promo-card:hover{transform:none}
  }
</style>
@endpush
@endonce

<section class="section-promo section-promo--{{ $variant }}" aria-label="بنرات الأقسام">
  <div class="section-promo__inner">
    @if($banners->count() === 1)
      @include('partials.section-promo-card', ['banner' => $banners->first(), 'solo' => true])
    @else
      <div class="section-promo__track" role="list">
        @foreach($banners as $banner)
          @include('partials.section-promo-card', ['banner' => $banner, 'solo' => false])
        @endforeach
      </div>
    @endif
  </div>
</section>
@endif
