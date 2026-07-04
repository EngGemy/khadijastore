@extends('layouts.app')
@section('title', $seo['title'] ?? ('براندات · ' . $brand->name))

@section('meta')
@include('partials.brand-meta', ['brand' => $brand, 'seo' => $seo, 'fbPageView' => $fbPageView, 'breadcrumbLabel' => 'البراندات'])
@endsection

@push('head')
<style>
  @keyframes meshMove {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -20px) scale(1.05); }
    66% { transform: translate(-20px, 15px) scale(0.98); }
  }
  @keyframes meshMove2 {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    50% { transform: translate(-40px, 30px) rotate(8deg); }
  }
  .mfg-mesh-bg {
    position: fixed;
    inset: 0;
    z-index: 0;
    overflow: hidden;
    pointer-events: none;
    background: #f6f6f4;
  }
  .mfg-blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: .45;
    animation: meshMove 18s ease-in-out infinite;
  }
  .mfg-blob-2 { animation: meshMove2 22s ease-in-out infinite; animation-delay: -5s; }
  .mfg-blob-3 { animation: meshMove 25s ease-in-out infinite reverse; animation-delay: -10s; }
  .mfg-card {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    transition: transform .35s cubic-bezier(.16,1,.3,1), box-shadow .35s ease;
  }
  .mfg-card:hover { transform: translateY(-6px) scale(1.02); box-shadow: 0 24px 48px -12px rgba(0,0,0,.2); }
  .mfg-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--from), var(--to));
    z-index: 0;
  }
  .mfg-card-shimmer {
    position: absolute;
    inset: -50%;
    background: conic-gradient(from 0deg, transparent, rgba(255,255,255,.15), transparent 30%);
    animation: spinSlow 8s linear infinite;
    z-index: 1;
    pointer-events: none;
  }
  @media (prefers-reduced-motion: reduce) {
    .mfg-blob, .mfg-card-shimmer { animation: none !important; }
  }
</style>
@endpush

@section('content')
@include('partials.strip')
@include('partials.header')

<div class="mfg-mesh-bg" aria-hidden="true">
  <div class="mfg-blob w-[500px] h-[500px] -top-32 -start-32 bg-emerald-300"></div>
  <div class="mfg-blob mfg-blob-2 w-[400px] h-[400px] top-1/3 end-0 bg-indigo-200"></div>
  <div class="mfg-blob mfg-blob-3 w-[350px] h-[350px] bottom-0 start-1/4 bg-amber-200"></div>
</div>

<div class="relative z-10">
  @include('partials.brand-hero', ['brand' => $brand, 'compact' => true, 'productCount' => $manufacturerBrands->sum('count')])
  @include('partials.brand-nav', ['brand' => $brand, 'active' => 'brands'])

  <section class="max-w-[1180px] mx-auto px-4 sm:px-5 py-8 sm:py-12" style="padding-bottom:96px">
    <div class="text-center mb-8 sm:mb-10 reveal">
      <span class="text-[11px] font-bold tracking-[.2em] uppercase text-accentDark">Shop by Brand</span>
      <h2 class="font-extrabold tracking-tight mt-2" style="font-size:clamp(24px,4vw,36px)">اختر البراند المفضل</h2>
      <p class="text-ink/50 text-sm mt-2 max-w-md mx-auto">اضغط على أي براند للانتقال إلى منتجاته داخل {{ $brand->name }}</p>
    </div>

    @if($manufacturerBrands->isEmpty())
      <p class="text-center text-ink/45 py-20">لا توجد براندات متاحة حالياً.</p>
    @else
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5 stagger">
        @foreach($manufacturerBrands as $mfg)
        <a href="{{ route('brand.shop', [$brand->slug, 'manufacturer' => $mfg->slug]) }}"
           class="mfg-card aspect-[3/4] flex flex-col group"
           style="--from:{{ $mfg->gradient_from }};--to:{{ $mfg->gradient_to }}">
          <div class="mfg-card-shimmer"></div>
          <div class="relative z-10 flex-1 flex flex-col p-4 sm:p-5">
            <div class="flex-1 flex items-center justify-center py-4">
              @if($mfg->image)
                <div class="w-[85%] aspect-square rounded-2xl bg-white/95 shadow-xl p-3 group-hover:scale-105 transition-transform duration-500">
                  <img src="{{ $mfg->image }}" alt="{{ $mfg->name }}" class="w-full h-full object-contain" loading="lazy">
                </div>
              @else
                <div class="w-[70%] aspect-square rounded-2xl bg-white/20 backdrop-blur border border-white/30 grid place-items-center">
                  <span class="text-white font-extrabold text-2xl sm:text-3xl drop-shadow-lg">{{ mb_substr($mfg->name, 0, 2) }}</span>
                </div>
              @endif
            </div>
            <div class="text-white text-center mt-auto">
              <h3 class="font-extrabold text-base sm:text-lg leading-tight drop-shadow">{{ $mfg->name }}</h3>
              <p class="text-[12px] text-white/75 font-semibold mt-1">{{ $mfg->count }} منتج</p>
              @if(!empty($mfg->departments))
                <p class="text-[10px] text-white/55 mt-1.5 line-clamp-1">{{ implode(' · ', $mfg->departments) }}</p>
              @endif
            </div>
          </div>
        </a>
        @endforeach
      </div>
    @endif
  </section>
</div>

<footer class="relative z-10 bg-ink text-paper py-7"><div class="max-w-[1180px] mx-auto px-5 text-center text-[13px] text-white/40">© {{ date('Y') }} {{ $storeName ?? 'متجر العلامات' }}</div></footer>
@endsection

@push('scripts')
<script>
document.documentElement.classList.add('js');
const io=new IntersectionObserver(es=>{es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target);}});},{threshold:.08});
document.querySelectorAll('.stagger,.reveal').forEach(el=>io.observe(el));
</script>
@endpush
