@extends('layouts.app')
@section('title', $seo['title'] ?? ($brand->name . ' · متجر العلامات'))

@section('meta')
@include('partials.brand-meta', ['brand' => $brand, 'seo' => $seo, 'fbPageView' => $fbPageView])
@endsection

@section('content')
@include('partials.brand-page-styles')
@include('partials.strip')
@include('partials.header')

@include('partials.brand-hero', ['brand' => $brand, 'brandStats' => $brandStats])
@include('partials.brand-nav', ['brand' => $brand, 'active' => 'home'])

{{-- بحث --}}
<section class="max-w-[1180px] mx-auto px-4 sm:px-5 pt-3 pb-1">
  <form action="{{ route('brand.shop', $brand->slug) }}" method="GET" class="brand-search">
    <input type="search" name="q" placeholder="ابحث في {{ $brand->name }}…" autocomplete="off">
    <svg class="absolute start-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-ink/35 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
  </form>
</section>

{{-- ═══ المنتجات أولاً (mobile-first) ═══ --}}
@if($homeProducts->isNotEmpty())
<section class="max-w-[1180px] mx-auto px-4 sm:px-5 py-4 sm:py-6">
  <div class="brand-section-head">
    <h2 class="brand-section-title">المنتجات</h2>
    <a href="{{ route('brand.shop', $brand->slug) }}" class="text-[12px] font-bold text-accentDark whitespace-nowrap">عرض الكل ←</a>
  </div>
  <div class="grid brand-product-grid">
    @foreach($homeProducts as $p)
      <div class="product-pop" style="animation-delay:{{ min($loop->index * 0.05, 0.4) }}s">
        @include('partials.product-card', ['product' => $p, 'storeBrand' => $brand, 'compact' => true])
      </div>
    @endforeach
  </div>
</section>
@endif

{{-- ═══ التصنيفات + أيقونات المشاركة ═══ --}}
@if($departments->isNotEmpty())
<section class="max-w-[1180px] mx-auto px-4 sm:px-5 py-4 sm:py-6 border-t border-line/40">
  <div class="brand-section-head">
    <h2 class="brand-section-title">التصنيفات</h2>
    <div class="flex items-center gap-2">
      @include('partials.brand-share-icons', ['brand' => $brand, 'seo' => $seo])
      <a href="{{ route('brand.shop', $brand->slug) }}" class="text-[12px] font-bold text-accentDark whitespace-nowrap max-sm:hidden">عرض الكل ←</a>
    </div>
  </div>
  <div class="brand-chip-scroll stagger">
    @foreach($departments as $dept)
      @include('partials.brand-dept-icon', ['dept' => $dept, 'brand' => $brand, 'index' => $loop->index])
    @endforeach
  </div>
</section>
@else
{{-- share icons alone if no departments --}}
<section class="max-w-[1180px] mx-auto px-4 sm:px-5 py-3 flex justify-end">
  @include('partials.brand-share-icons', ['brand' => $brand, 'seo' => $seo])
</section>
@endif

{{-- ═══ براندات مرتبطة ═══ --}}
@if($manufacturerBrands->isNotEmpty())
<section class="max-w-[1180px] mx-auto px-4 sm:px-5 py-4 sm:py-6 border-t border-line/40 brand-safe-bottom">
  <div class="brand-section-head">
    <h2 class="brand-section-title">براندات مرتبطة</h2>
    <a href="{{ route('brand.manufacturers', $brand->slug) }}" class="text-[12px] font-bold text-accentDark whitespace-nowrap">عرض الكل ←</a>
  </div>
  <div class="brand-chip-scroll stagger">
    @foreach($manufacturerBrands->take(12) as $mfg)
      @include('partials.brand-mfg-icon', ['mfg' => $mfg, 'brand' => $brand])
    @endforeach
  </div>
</section>
@endif

@include('partials.brand-wa-fab', ['brand' => $brand])

<footer class="bg-ink text-paper py-6"><div class="max-w-[1180px] mx-auto px-5 text-center text-[12px] text-white/40">© {{ date('Y') }} {{ $storeName ?? 'متجر العلامات' }}</div></footer>
@endsection

@push('scripts')
<script>
document.documentElement.classList.add('js');
const io=new IntersectionObserver(es=>{es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target);}});},{threshold:.06});
document.querySelectorAll('.stagger,.reveal').forEach(el=>io.observe(el));
</script>
@endpush
