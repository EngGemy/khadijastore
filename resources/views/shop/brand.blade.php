@extends('layouts.app')
@section('title', $brand->name . ' · متجر العلامات')

@section('content')
@include('partials.strip')


<div class="bg-ink text-paper text-center text-[13px] py-2.5 font-medium relative z-50"><span class="inline-block w-1.5 h-1.5 rounded-full bg-accent align-middle ms-2 animate-blink"></span>شحن مجاني داخل القاهرة والجيزة · الدفع عند الاستلام</div>

<header id="hdr" class="sticky top-0 z-40 bg-paper/70 backdrop-blur-2xl border-b border-transparent transition-all duration-300">
  <div class="max-w-[1180px] mx-auto px-5 h-[70px] flex items-center justify-between gap-5">
    <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-extrabold text-lg tracking-tight"><span class="w-10 h-10 rounded-xl bg-ink text-paper grid place-items-center font-extrabold text-lg">ع</span><span>متجر العلامات</span></a>
    <nav class="hidden md:flex gap-8 text-[14.5px] font-semibold text-ink/55"><a href="{{ route('home') }}" class="hover:text-ink transition">الرئيسية</a><a href="{{ route('home') }}#brands" class="hover:text-ink transition">البراندات</a><a href="{{ route('home') }}#cats" class="hover:text-ink transition">التصنيفات</a></nav>
    <a href="https://wa.me/{{ $brand->whatsapp }}" id="brandWa" target="_blank" class="inline-flex items-center gap-2 bg-accent text-white text-sm font-bold rounded-full shadow-cta hover:bg-accentDark hover:-translate-y-0.5 transition-all" style="padding:10px 18px"><svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>واتساب البراند</a>
  </div>
</header>

<!-- BANNER -->
<section class="relative overflow-hidden bg-ink text-white py-15" style="padding:60px 0">
  <div class="absolute -top-2/5 -end-[5%] w-[480px] h-[480px] animate-spinSlow" style="background:radial-gradient(circle,rgba(22,163,74,.18),transparent 65%)"></div>
  <div class="absolute inset-0 opacity-[.04]" style="background-image:radial-gradient(circle at 1px 1px,#fff 1px,transparent 0);background-size:28px 28px"></div>
  <div class="max-w-[1180px] mx-auto px-5 relative z-10 flex items-center gap-7 max-sm:flex-col max-sm:text-center">
    <div id="brandMark" class="w-[90px] h-[90px] rounded-3xl bg-white text-ink grid place-items-center font-extrabold text-4xl shrink-0 animate-floaty overflow-hidden" style="box-shadow:0 16px 40px -8px rgba(0,0,0,.4)">@php $logo = $brand->getFirstMediaUrl('logo', 'thumb'); @endphp @if($logo)<img src="{{ $logo }}" alt="{{ $brand->name }}" class="w-full h-full object-cover">@else{{ $brand->mark }}@endif</div>
    <div>
      <span id="brandCat" class="inline-block bg-white/10 border border-white/15 text-xs font-semibold rounded-full mb-3 tracking-wide" style="padding:5px 14px">{{ $brand->category_label }}</span>
      <h1 id="brandTitle" class="font-extrabold tracking-tight" style="font-size:clamp(28px,5vw,44px)">{{ $brand->name }}</h1>
      <p id="brandDesc" class="text-white/60 text-[15px] max-w-[440px] mt-2 leading-relaxed">{{ $brand->description }}</p>
      @php
        $brandRatingStat = number_format($brand->products->avg('rating') ?? 0, 1);
        $brandSalesStat  = $brand->products->sum('sales_count') ?? 0;
        $brandProductCount = $brand->products->count();
      @endphp
      <div class="flex gap-7 mt-4.5 max-sm:justify-center" style="margin-top:18px"><div><div class="font-extrabold text-[22px]">{{ $brandProductCount }}</div><div class="text-xs text-white/45 mt-0.5">منتج</div></div><div><div class="font-extrabold text-[22px]">{{ $brandRatingStat }}★</div><div class="text-xs text-white/45 mt-0.5">التقييم</div></div><div><div class="font-extrabold text-[22px]">+{{ number_format($brandSalesStat) }}</div><div class="text-xs text-white/45 mt-0.5">مبيعات</div></div></div>
    </div>
  </div>
</section>

<!-- FILTER -->
<div class="border-b border-line bg-paper/85 backdrop-blur-2xl sticky top-[70px] z-30">
  <div class="max-w-[1180px] mx-auto px-5 flex gap-2.5 py-4 overflow-x-auto" style="scrollbar-width:none">
    <button class="chip whitespace-nowrap rounded-full border-[1.5px] border-ink bg-ink text-white text-sm font-semibold transition" style="padding:9px 18px">الكل</button>
    <button class="chip whitespace-nowrap rounded-full border-[1.5px] border-line bg-paper text-sm font-semibold text-ink/52 hover:border-ink hover:text-ink transition" style="padding:9px 18px">الأكثر مبيعًا</button>
    <button class="chip whitespace-nowrap rounded-full border-[1.5px] border-line bg-paper text-sm font-semibold text-ink/52 hover:border-ink hover:text-ink transition" style="padding:9px 18px">جديد</button>
    <button class="chip whitespace-nowrap rounded-full border-[1.5px] border-line bg-paper text-sm font-semibold text-ink/52 hover:border-ink hover:text-ink transition" style="padding:9px 18px">عروض</button>
    <button class="chip whitespace-nowrap rounded-full border-[1.5px] border-line bg-paper text-sm font-semibold text-ink/52 hover:border-ink hover:text-ink transition" style="padding:9px 18px">العناية بالبشرة</button>
    <button class="chip whitespace-nowrap rounded-full border-[1.5px] border-line bg-paper text-sm font-semibold text-ink/52 hover:border-ink hover:text-ink transition" style="padding:9px 18px">المكياج</button>
  </div>
</div>

<section class="max-w-[1180px] mx-auto px-5 py-11" style="padding-top:44px;padding-bottom:72px">
  <div id="prodGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 stagger">
    @foreach($brand->products as $p)
    <a href="{{ route('product.show', $p->slug) }}" class="group border border-line rounded-[18px] overflow-hidden flex flex-col bg-paper hover:-translate-y-1.5 hover:shadow-lg2 transition-all duration-500">
      <div class="aspect-square bg-gradient-to-br from-paper2 to-paper3 relative overflow-hidden grid place-items-center">
        @if($p->badge)<span class="absolute top-3 start-3 bg-ink text-paper text-[11px] font-bold px-2.5 py-1 rounded-full z-10">{{ $p->badge }}</span>@endif
        @php $cover = $p->getFirstMediaUrl('cover','thumb'); @endphp
        @if($cover)<img src="{{ $cover }}" alt="{{ $p->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
        @else<span class="font-extrabold text-2xl text-ink/10 group-hover:scale-110 transition-transform duration-500">{{ $p->mark }}</span>@endif
        @if($p->isOutOfStock())
          <span class="absolute inset-0 bg-paper/80 backdrop-blur-sm grid place-items-center z-20"><span class="bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full">نفد المخزون</span></span>
        @elseif($p->isLowStock())
          <span class="absolute top-3 end-3 bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full z-10">متبقي {{ $p->total_stock }}</span>
        @endif
        <span class="absolute inset-x-0 bottom-0 bg-ink text-white text-center py-3 text-sm font-bold translate-y-full group-hover:translate-y-0 transition-transform duration-300">عرض المنتج ←</span>
      </div>
      <div class="p-4 flex flex-col gap-1.5 flex-1">
        <span class="text-[11px] text-accentDark font-bold">{{ $brand->name }}</span>
        <h3 class="font-bold text-[15px] leading-snug">{{ $p->name }}</h3>
        <span class="text-[12.5px] text-ink/52 font-semibold">★ {{ number_format($p->rating,1) }} ({{ $p->sales_count }})</span>
        <div class="flex items-baseline gap-1.5 mt-auto pt-2"><span class="font-extrabold text-[21px] tracking-tight">{{ number_format($p->price) }}</span><span class="text-[13px] font-bold">ج.م</span>@if($p->compare_price)<span class="text-[13px] text-ink/38 line-through ms-0.5">{{ number_format($p->compare_price) }}</span>@endif</div>
      </div>
    </a>
    @endforeach
  </div>
</section>

<a href="https://wa.me/{{ $brand->whatsapp }}" id="brandWaFloat" target="_blank" title="تواصل مع البراند" class="fixed bottom-6.5 start-6.5 z-50 w-[58px] h-[58px] rounded-full bg-accent text-white grid place-items-center animate-ring hover:scale-110 hover:-translate-y-0.5 transition-all" style="bottom:26px;inset-inline-start:26px;box-shadow:0 12px 30px -6px rgba(22,163,74,.55)"><svg class="w-7 h-7 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg></a>

<footer class="bg-ink text-paper py-7"><div class="max-w-[1180px] mx-auto px-5 text-center text-[13px] text-white/40">© 2026 متجر العلامات · جميع الحقوق محفوظة</div></footer>


@endsection

@push('scripts')
<script>
document.documentElement.classList.add('js');
const io=new IntersectionObserver(es=>{es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target);}});},{threshold:.1});
document.querySelectorAll('.stagger').forEach(el=>io.observe(el));
document.querySelectorAll('.chip').forEach(c=>c.addEventListener('click',function(){document.querySelectorAll('.chip').forEach(x=>{x.classList.remove('bg-ink','text-white','border-ink');x.classList.add('border-line','bg-paper','text-ink/52');});this.classList.remove('border-line','bg-paper','text-ink/52');this.classList.add('bg-ink','text-white','border-ink');}));
const hdr=document.getElementById('hdr');
addEventListener('scroll',()=>{hdr.classList.toggle('border-line',scrollY>16);hdr.classList.toggle('shadow-soft',scrollY>16);},{passive:true});
</script>
@endpush
