{{-- Homepage offers / deals — compare_price > price, badge, or featured --}}
@php
  $offers = $offerProducts ?? collect();
@endphp
@if($offers->isNotEmpty())
<section id="offers" class="home-section relative overflow-hidden">
  <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
    <div class="absolute inset-0" style="background:linear-gradient(180deg,var(--paper-2) 0%,#fff 55%,var(--paper-2) 100%)"></div>
    <div class="absolute -top-24 -start-16 w-[420px] h-[420px] rounded-full opacity-60"
         style="background:radial-gradient(circle,rgba(249,115,22,.14),transparent 68%)"></div>
    <div class="absolute bottom-0 -end-20 w-[360px] h-[360px] rounded-full opacity-50"
         style="background:radial-gradient(circle,rgba(11,29,54,.08),transparent 70%)"></div>
  </div>

  <div class="max-w-[1180px] mx-auto px-4 sm:px-5 relative z-10">
    <div class="reveal flex flex-col sm:flex-row sm:items-end sm:justify-between gap-5 mb-8">
      <div>
        <span class="sec-eyebrow mb-2.5">عروض حصرية · DEALS</span>
        <h2 class="font-extrabold tracking-tight text-ink" style="font-size:clamp(24px,3.4vw,36px);line-height:1.15">
          وفّر أكثر مع <span class="text-brand">أفضل العروض</span>
        </h2>
        <p class="text-muted text-[14px] mt-2.5 max-w-lg font-medium leading-relaxed">
          منتجات بخصم حقيقي أو شارة عرض — أسعار أوضح، قيمة أفضل.
        </p>
      </div>
      <a href="{{ route('products.index') }}"
         class="shine inline-flex items-center gap-2 bg-ink text-paper font-extrabold rounded-2xl px-5 py-3 text-[13px] hover:bg-ink2 hover:-translate-y-0.5 transition-all shadow-soft whitespace-nowrap self-start sm:self-auto">
        كل العروض
        <span aria-hidden="true">←</span>
      </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-5 stagger">
      @foreach($offers->take(8) as $p)
      @php
        $discount = ($p->compare_price && $p->compare_price > $p->price)
          ? (int) round((1 - $p->price / $p->compare_price) * 100) : 0;
        $saved = ($discount > 0) ? (int) ($p->compare_price - $p->price) : 0;
      @endphp
      <a href="{{ route('product.show', $p->slug) }}" class="offer-card group">
        <div class="offer-card__media">
          @if($discount > 0)
            <span class="offer-card__pct">-{{ $discount }}%</span>
          @elseif($p->badge)
            <span class="offer-card__badge">{{ $p->badge }}</span>
          @elseif($p->is_featured)
            <span class="offer-card__badge">مميّز</span>
          @endif
          @include('partials.product-cover', ['product' => $p])
          <div class="offer-card__overlay">
            <span class="offer-card__cta">احصل على العرض ←</span>
          </div>
        </div>
        <div class="offer-card__body">
          @if($p->brand)
          <p class="offer-card__brand">{{ $p->brand->name }}</p>
          @endif
          <h3 class="offer-card__title">{{ $p->name }}</h3>
          <div class="offer-card__prices">
            <span class="offer-card__price">{{ number_format($p->price) }} <small>ج.م</small></span>
            @if($discount > 0)
            <span class="offer-card__compare">{{ number_format($p->compare_price) }}</span>
            @endif
          </div>
          @if($saved > 0)
          <p class="offer-card__save">توفّر {{ number_format($saved) }} ج.م</p>
          @endif
        </div>
      </a>
      @endforeach
    </div>
  </div>
</section>
@endif
