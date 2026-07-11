@extends('layouts.app')
@section('title', $seo['title'] ?? ('براندات مرتبطة · ' . $brand->name))

@section('meta')
@include('partials.brand-meta', ['brand' => $brand, 'seo' => $seo, 'fbPageView' => $fbPageView, 'breadcrumbLabel' => 'براندات مرتبطة'])
@endsection

@section('content')
@include('partials.brand-page-styles')
@include('partials.strip')
@include('partials.header')

@include('partials.brand-hero', ['brand' => $brand, 'compact' => true, 'brandStats' => $brandStats, 'showActions' => false])
@include('partials.brand-nav', ['brand' => $brand, 'active' => 'brands'])

<section class="max-w-[1180px] mx-auto px-4 sm:px-5 py-4 sm:py-8" style="padding-bottom:96px">
  <div class="brand-section-head reveal">
    <h2 class="brand-section-title">براندات مرتبطة</h2>
    @include('partials.brand-share-icons', ['brand' => $brand, 'seo' => $seo])
  </div>
  <p class="text-[12px] text-ink/45 mb-4 reveal">اضغط على أي براند لعرض منتجاته</p>

  @if($manufacturerBrands->isEmpty())
    <p class="text-center text-ink/45 py-16 text-sm">لا توجد براندات مرتبطة حالياً.</p>
  @else
    <div class="brand-chip-scroll stagger flex-wrap sm:flex-nowrap gap-y-4">
      @foreach($manufacturerBrands as $mfg)
        @include('partials.brand-mfg-icon', ['mfg' => $mfg, 'brand' => $brand])
      @endforeach
    </div>

    {{-- grid cards on larger screens --}}
    <div class="hidden sm:grid sm:grid-cols-3 lg:grid-cols-4 gap-4 mt-8 stagger">
      @foreach($manufacturerBrands as $mfg)
      <a href="{{ route('brand.shop', [$brand->slug, 'manufacturer' => $mfg->slug]) }}"
         class="brand-mfg-card group relative overflow-hidden rounded-2xl aspect-[3/4] flex flex-col"
         style="--from:{{ $mfg->gradient_from }};--to:{{ $mfg->gradient_to }}">
        <div class="absolute inset-0 bg-gradient-to-br from-[var(--from)] to-[var(--to)]"></div>
        <div class="relative z-10 flex flex-col h-full p-4 justify-end text-white">
          @if($mfg->image)
            <div class="flex-1 flex items-center justify-center mb-3">
              <div class="w-[75%] aspect-square rounded-xl bg-white/95 p-2 group-hover:scale-105 transition-transform">
                <img src="{{ $mfg->image }}" alt="{{ $mfg->name }}" class="w-full h-full object-contain" loading="lazy">
              </div>
            </div>
          @endif
          <h3 class="font-extrabold text-base">{{ $mfg->name }}</h3>
          <p class="text-[11px] text-white/70 mt-1">{{ $mfg->count }} منتج</p>
        </div>
      </a>
      @endforeach
    </div>
  @endif
</section>

<footer class="bg-ink text-paper py-6"><div class="max-w-[1180px] mx-auto px-5 text-center text-[12px] text-white/40">© {{ date('Y') }} {{ $storeName ?? 'متجر العلامات' }}</div></footer>
@endsection

@push('scripts')
<script>
document.documentElement.classList.add('js');
const io=new IntersectionObserver(es=>{es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target);}});},{threshold:.06});
document.querySelectorAll('.stagger,.reveal').forEach(el=>io.observe(el));
</script>
@endpush
