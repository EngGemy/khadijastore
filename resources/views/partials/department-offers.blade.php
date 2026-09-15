@php
  $offers = $offers ?? collect();
  $heading = $heading ?? 'عروض القسم';
  $moreUrl = $moreUrl ?? null;
@endphp
@if($offers->isNotEmpty())
<section class="dept-offers" aria-label="{{ $heading }}">
  <div class="dept-offers__inner">
    <div class="brand-section-head" style="margin-bottom:12px">
      <h2 class="brand-section-title">{{ $heading }}</h2>
      @if($moreUrl)
        <a href="{{ $moreUrl }}" class="brand-section-link">المزيد</a>
      @endif
    </div>
    <div class="flex gap-3 overflow-x-auto app-h-scroll pb-1 -mx-1 px-1" style="scrollbar-width:none;-webkit-overflow-scrolling:touch">
      @foreach($offers as $p)
        <div class="w-[46vw] max-w-[180px] md:w-[200px] md:max-w-none shrink-0">
          @include('partials.product-card', ['product' => $p, 'storeBrand' => $p->brand, 'compact' => true])
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif
