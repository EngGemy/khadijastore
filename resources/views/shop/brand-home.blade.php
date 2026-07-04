@extends('layouts.app')
@section('title', $seo['title'] ?? ($brand->name . ' · متجر العلامات'))

@section('meta')
@include('partials.brand-meta', ['brand' => $brand, 'seo' => $seo, 'fbPageView' => $fbPageView])
@endsection

@section('content')
@include('partials.strip')
@include('partials.header')

@include('partials.brand-hero', ['brand' => $brand, 'productCount' => $productCount])
@include('partials.brand-nav', ['brand' => $brand, 'active' => 'home'])

{{-- بحث سريع → صفحة المنتجات --}}
<section class="max-w-[1180px] mx-auto px-4 sm:px-5 py-4">
  <form action="{{ route('brand.shop', $brand->slug) }}" method="GET" class="relative">
    <input type="search" name="q" placeholder="ابحث في {{ $brand->name }}…"
           class="w-full rounded-2xl border border-line bg-paper2 ps-12 pe-4 py-3.5 text-sm font-semibold outline-none focus:border-ink/30 focus:ring-2 focus:ring-ink/5 transition-all"
           autocomplete="off">
    <svg class="absolute start-4 top-1/2 -translate-y-1/2 w-5 h-5 text-ink/35" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
  </form>
</section>

{{-- الأقسام --}}
@if($departments->isNotEmpty())
<section class="max-w-[1180px] mx-auto px-4 sm:px-5 py-6 sm:py-8">
  <div class="flex items-end justify-between gap-4 mb-5">
    <div>
      <span class="text-[11px] font-bold tracking-wide text-accentDark uppercase">تسوق حسب القسم</span>
      <h2 class="font-extrabold text-xl sm:text-2xl tracking-tight mt-1">الأقسام</h2>
    </div>
    <a href="{{ route('brand.shop', $brand->slug) }}" class="text-sm font-bold text-accentDark whitespace-nowrap">عرض الكل ←</a>
  </div>
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 stagger">
    @foreach($departments as $dept)
    <a href="{{ route('brand.shop', [$brand->slug, 'dept' => $dept->id]) }}"
       class="group relative overflow-hidden rounded-2xl border border-line bg-paper aspect-[4/3] flex flex-col justify-end hover:-translate-y-1 hover:shadow-lg2 transition-all duration-300">
      <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-ink/20 to-transparent z-10"></div>
      @if($dept->cover_image)
        <img src="{{ $dept->cover_image }}" alt="" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
      @else
        <div class="absolute inset-0 bg-gradient-to-br from-paper2 to-paper3 grid place-items-center text-4xl">{{ $dept->icon ?? '📦' }}</div>
      @endif
      <div class="relative z-20 p-3 sm:p-4 text-white">
        <span class="text-2xl mb-1 block drop-shadow">{{ $dept->icon ?? '📦' }}</span>
        <h3 class="font-extrabold text-[15px] sm:text-base leading-tight">{{ $dept->name }}</h3>
        <p class="text-[11px] text-white/70 mt-0.5 font-semibold">{{ $dept->display_count }} منتج</p>
      </div>
    </a>
    @endforeach
  </div>
</section>
@endif

{{-- البراندات --}}
@if($manufacturerBrands->isNotEmpty())
<section class="max-w-[1180px] mx-auto px-4 sm:px-5 py-6 sm:py-8 border-t border-line/60">
  <div class="flex items-end justify-between gap-4 mb-5">
    <div>
      <span class="text-[11px] font-bold tracking-wide text-accentDark uppercase">الماركات</span>
      <h2 class="font-extrabold text-xl sm:text-2xl tracking-tight mt-1">تسوق حسب البراند</h2>
    </div>
    <a href="{{ route('brand.manufacturers', $brand->slug) }}" class="text-sm font-bold text-accentDark whitespace-nowrap">كل البراندات ←</a>
  </div>
  <div class="flex gap-3 overflow-x-auto pb-2 -mx-1 px-1 scrollbar-none snap-x snap-mandatory">
    @foreach($manufacturerBrands->take(8) as $mfg)
    <a href="{{ route('brand.shop', [$brand->slug, 'manufacturer' => $mfg->slug]) }}"
       class="snap-start flex-shrink-0 w-[120px] sm:w-[140px] group">
      <div class="aspect-square rounded-2xl overflow-hidden border border-line relative mb-2 group-hover:-translate-y-1 transition-transform shadow-sm group-hover:shadow-md"
           style="background:linear-gradient(135deg,{{ $mfg->gradient_from }},{{ $mfg->gradient_to }})">
        @if($mfg->image)
          <img src="{{ $mfg->image }}" alt="{{ $mfg->name }}" class="absolute inset-2 w-[calc(100%-16px)] h-[calc(100%-16px)] object-contain mx-auto rounded-xl bg-white/90 p-1" loading="lazy">
        @else
          <span class="absolute inset-0 grid place-items-center text-white font-extrabold text-lg">{{ mb_substr($mfg->name, 0, 2) }}</span>
        @endif
      </div>
      <p class="text-[12px] font-bold text-center truncate">{{ $mfg->name }}</p>
      <p class="text-[10px] text-ink/40 text-center">{{ $mfg->count }} منتج</p>
    </a>
    @endforeach
  </div>
</section>
@endif

{{-- منتجات مميزة --}}
@if($featuredProducts->isNotEmpty())
<section class="max-w-[1180px] mx-auto px-4 sm:px-5 py-6 sm:py-10 border-t border-line/60" style="padding-bottom:88px">
  <div class="flex items-end justify-between gap-4 mb-5">
    <h2 class="font-extrabold text-xl sm:text-2xl tracking-tight">منتجات مميزة</h2>
    <a href="{{ route('brand.shop', $brand->slug) }}" class="text-sm font-bold text-accentDark">عرض الكل ←</a>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5 stagger">
    @foreach($featuredProducts as $p)
      @include('partials.product-card', ['product' => $p, 'storeBrand' => $brand])
    @endforeach
  </div>
</section>
@endif

@if($brand->whatsapp)
<a href="https://wa.me/{{ $brand->whatsapp }}" target="_blank" rel="noopener" title="تواصل مع المتجر"
   class="fixed z-50 w-[54px] h-[54px] rounded-full bg-accent text-white grid place-items-center animate-ring hover:scale-110 transition-all"
   style="bottom:24px;inset-inline-start:24px;box-shadow:0 12px 30px -6px rgba(22,163,74,.55)">
  <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
</a>
@endif

<footer class="bg-ink text-paper py-7"><div class="max-w-[1180px] mx-auto px-5 text-center text-[13px] text-white/40">© {{ date('Y') }} {{ $storeName ?? 'متجر العلامات' }}</div></footer>
@endsection

@push('scripts')
<script>
document.documentElement.classList.add('js');
const io=new IntersectionObserver(es=>{es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target);}});},{threshold:.1});
document.querySelectorAll('.stagger').forEach(el=>io.observe(el));
</script>
@endpush
