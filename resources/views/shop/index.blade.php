@extends('layouts.app')
@section('title', 'متجر العلامات · BRANDS')

@section('content')
@include('partials.strip')

<header id="hdr" class="sticky top-0 z-40 bg-paper/70 backdrop-blur-2xl border-b border-transparent transition-all duration-300">
  <div class="max-w-[1180px] mx-auto px-5 h-[70px] flex items-center justify-between gap-5">
    <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-extrabold text-lg tracking-tight"><span class="w-10 h-10 rounded-xl bg-ink text-paper grid place-items-center font-extrabold text-lg">ع</span><span>متجر العلامات</span></a>
    <nav class="hidden md:flex gap-8 text-[14.5px] font-semibold text-ink/55">
      <a href="{{ route('home') }}" class="hover:text-ink transition">الرئيسية</a><a href="#brands" class="hover:text-ink transition">البراندات</a><a href="#cats" class="hover:text-ink transition">التصنيفات</a><a href="#products" class="hover:text-ink transition">المنتجات</a>
    </nav>
    <a href="https://wa.me/201001234567" target="_blank" class="inline-flex items-center gap-2 bg-accent text-white text-sm font-bold rounded-full shadow-cta hover:bg-accentDark hover:-translate-y-0.5 transition-all" style="padding:10px 18px"><svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>تواصل معنا</a>
  </div>
</header>

<!-- HERO -->
<section class="relative overflow-hidden py-20">
  <div class="absolute -top-52 -start-40 w-[600px] h-[600px] rounded-full pointer-events-none" style="background:radial-gradient(circle,rgba(22,163,74,.08),transparent 65%)"></div>
  <div class="max-w-[1180px] mx-auto px-5 relative z-10 grid lg:grid-cols-[1.15fr_.85fr] gap-12 items-center">
    <div>
      <span class="text-xs font-bold tracking-[.14em] uppercase text-accentDark">منصة البراندات الموثوقة · <span class="en tracking-[.18em]">EST. 2026</span></span>
      <h1 class="font-extrabold leading-[1.1] tracking-tight mt-4.5 mb-0" style="font-size:clamp(30px,5vw,52px);margin-top:18px">
        <span class="hl-line block overflow-hidden"><span>أفضل <span class="relative text-accent">البراندات<span class="absolute -inset-x-1 bottom-2 h-4 bg-accent/[.16] rounded -z-10"></span></span></span></span>
        <span class="hl-line block overflow-hidden"><span>في مكان واحد</span></span>
      </h1>
      <p class="text-ink/52 text-lg max-w-[480px] leading-relaxed" style="margin:22px 0 32px">موبايلات، قطع غيار، عطور ومواد عطارة — تشكيلة مختارة من علامات موثوقة، مع الدفع عند الاستلام والتوصيل لكل المحافظات.</p>
      <div class="flex gap-3.5 flex-wrap">
        <a href="#products" class="shine animate-ring bg-accent text-white font-bold rounded-2xl shadow-cta hover:bg-accentDark hover:-translate-y-0.5 transition-all" style="padding:16px 28px">تسوّق الآن</a>
        <a href="#brands" class="border-[1.5px] border-ink text-ink font-bold rounded-2xl hover:bg-ink hover:text-white hover:-translate-y-0.5 transition-all" style="padding:14px 28px">تصفّح البراندات</a>
      </div>
      <div class="flex gap-3.5 mt-11">
        <div class="flex-1 min-w-[90px]"><div class="font-extrabold text-3xl tracking-tight leading-none">{{ $brands->count() }}+</div><div class="text-[13px] text-ink/52 mt-1.5">براندات · Brands</div></div>
        <div class="flex-1 min-w-[90px] border-s border-line ps-3.5"><div class="font-extrabold text-3xl tracking-tight leading-none">1.2k+</div><div class="text-[13px] text-ink/52 mt-1.5">عميل سعيد</div></div>
        <div class="flex-1 min-w-[90px] border-s border-line ps-3.5"><div class="font-extrabold text-3xl tracking-tight leading-none">4.8★</div><div class="text-[13px] text-ink/52 mt-1.5">متوسط التقييم</div></div>
      </div>
    </div>
    <div class="relative h-[440px] reveal-scale max-lg:h-[340px]">
      <div class="absolute end-0 top-0 w-[62%] h-[64%] z-20 rounded-[28px] flex items-end p-5 font-bold text-white/50 overflow-hidden shadow-soft animate-floaty" style="background:linear-gradient(150deg,#1a1a1a,#2e2e2e)"><span class="absolute top-4.5 start-4.5 text-[11px] font-bold tracking-widest opacity-60 en" style="top:18px;inset-inline-start:18px">FEATURED</span>عطور النخبة</div>
      <div class="absolute start-0 top-[28%] w-[46%] h-[46%] z-30 rounded-[28px] flex items-end p-5 font-bold text-ink/52 overflow-hidden shadow-soft border border-line bg-gradient-to-br from-paper2 to-paper3 animate-floaty" style="animation-delay:.5s"><span class="absolute top-4.5 start-4.5 text-[11px] font-bold tracking-widest opacity-60 en" style="top:18px;inset-inline-start:18px">NEW</span>موبايل ستور</div>
      <div class="absolute end-[6%] bottom-0 w-[42%] h-[42%] z-10 rounded-[28px] flex items-end p-5 font-bold text-ink/52 overflow-hidden shadow-soft border border-line bg-gradient-to-br from-paper2 to-paper3 animate-floaty" style="animation-delay:1s"><span class="absolute top-4.5 start-4.5 text-[11px] font-bold tracking-widest opacity-60 en" style="top:18px;inset-inline-start:18px">SALE</span>العناية</div>
    </div>
  </div>
</section>

<!-- MARQUEE -->
<section class="py-6.5 border-y border-line bg-paper2 overflow-hidden marq-mask" style="padding:26px 0">
  <div class="flex gap-4.5 w-max animate-marquee" style="gap:18px">
    @foreach($brands->concat($brands) as $b)
      <a href="{{ route('brand.show', $b->slug) }}" class="flex items-center gap-2.5 border border-line rounded-full bg-paper font-bold text-[15px] whitespace-nowrap hover:-translate-y-0.5 hover:border-ink transition-all" style="padding:10px 18px"><span class="rounded-lg bg-ink text-paper grid place-items-center text-[13px] font-extrabold overflow-hidden" style="width:30px;height:30px">@php $logo = $b->getFirstMediaUrl('logo', 'thumb'); @endphp @if($logo)<img src="{{ $logo }}" alt="{{ $b->name }}" class="w-full h-full object-cover">@else{{ $b->mark }}@endif</span>{{ $b->name }} <span class="en text-[11px] text-ink/52 font-semibold">· {{ \Illuminate\Support\Str::after($b->category_label ?? '', '· ') }}</span></a>
    @endforeach
  </div>
</section>

<!-- CATEGORIES -->
<section id="cats" class="max-w-[1180px] mx-auto px-5 py-18" style="padding-top:72px;padding-bottom:72px">
  <div class="reveal mb-9"><span class="text-xs font-bold tracking-[.14em] uppercase text-accentDark block mb-2.5">تسوّق حسب الفئة · <span class="en tracking-[.18em]">CATEGORIES</span></span><h2 class="font-extrabold tracking-tight" style="font-size:clamp(24px,3.5vw,36px)">كل ما تحتاجه، مصنّف بعناية</h2></div>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 stagger">
    <a href="{{ route('brand.show', 'mobile') }}" class="group relative rounded-[28px] overflow-hidden aspect-[.82] flex flex-col justify-end p-6 text-white bg-ink hover:-translate-y-1.5 hover:shadow-lg2 transition-all duration-500"><span class="absolute top-5.5 start-5.5 rounded-full border border-white/25 grid place-items-center group-hover:bg-white group-hover:text-ink group-hover:-rotate-45 transition-all" style="top:22px;inset-inline-start:22px;width:34px;height:34px">↗</span><span class="text-[34px] mb-auto opacity-90 group-hover:scale-110 transition-transform duration-500">📱</span><span class="font-extrabold text-lg tracking-tight leading-tight">موبايلات وأكسسوار</span><span class="text-[11px] font-semibold opacity-50 tracking-widest mt-1 en">MOBILES</span></a>
    <a href="{{ route('brand.show', 'parts') }}" class="group relative rounded-[28px] overflow-hidden aspect-[.82] flex flex-col justify-end p-6 text-white hover:-translate-y-1.5 hover:shadow-lg2 transition-all duration-500" style="background:linear-gradient(155deg,#1c1c1c,#333)"><span class="absolute top-5.5 start-5.5 rounded-full border border-white/25 grid place-items-center group-hover:bg-white group-hover:text-ink group-hover:-rotate-45 transition-all" style="top:22px;inset-inline-start:22px;width:34px;height:34px">↗</span><span class="text-[34px] mb-auto opacity-90 group-hover:scale-110 transition-transform duration-500">🔧</span><span class="font-extrabold text-lg tracking-tight leading-tight">قطع غيار</span><span class="text-[11px] font-semibold opacity-50 tracking-widest mt-1 en">SPARE PARTS</span></a>
    <a href="{{ route('brand.show', 'perfume') }}" class="group relative rounded-[28px] overflow-hidden aspect-[.82] flex flex-col justify-end p-6 text-white hover:-translate-y-1.5 hover:shadow-lg2 transition-all duration-500" style="background:linear-gradient(155deg,#0a0a0a,#262626)"><span class="absolute top-5.5 start-5.5 rounded-full border border-white/25 grid place-items-center group-hover:bg-white group-hover:text-ink group-hover:-rotate-45 transition-all" style="top:22px;inset-inline-start:22px;width:34px;height:34px">↗</span><span class="text-[34px] mb-auto opacity-90 group-hover:scale-110 transition-transform duration-500">🌸</span><span class="font-extrabold text-lg tracking-tight leading-tight">عطور</span><span class="text-[11px] font-semibold opacity-50 tracking-widest mt-1 en">PERFUMES</span></a>
    <a href="{{ route('brand.show', 'attar') }}" class="group relative rounded-[28px] overflow-hidden aspect-[.82] flex flex-col justify-end p-6 text-white hover:-translate-y-1.5 hover:shadow-lg2 transition-all duration-500" style="background:linear-gradient(155deg,#242424,#0d0d0d)"><span class="absolute top-5.5 start-5.5 rounded-full border border-white/25 grid place-items-center group-hover:bg-white group-hover:text-ink group-hover:-rotate-45 transition-all" style="top:22px;inset-inline-start:22px;width:34px;height:34px">↗</span><span class="text-[34px] mb-auto opacity-90 group-hover:scale-110 transition-transform duration-500">🌿</span><span class="font-extrabold text-lg tracking-tight leading-tight">مواد عطارة</span><span class="text-[11px] font-semibold opacity-50 tracking-widest mt-1 en">HERBS</span></a>
  </div>
</section>

<!-- BRANDS -->
<section id="brands" class="max-w-[1180px] mx-auto px-5 py-18" style="padding-top:72px;padding-bottom:72px">
  <div class="reveal flex items-end justify-between gap-5 mb-9"><div><span class="text-xs font-bold tracking-[.14em] uppercase text-accentDark block mb-2.5">شركاؤنا · <span class="en tracking-[.18em]">OUR BRANDS</span></span><h2 class="font-extrabold tracking-tight" style="font-size:clamp(24px,3.5vw,36px)">براندات تثق بها</h2></div><a href="#" class="text-sm font-bold inline-flex items-center gap-1.5 hover:gap-2.5 transition-all whitespace-nowrap">عرض الكل <span>←</span></a></div>
  <div class="grid md:grid-cols-3 gap-5 stagger">
    @foreach($brands as $b)
    <a href="{{ route('brand.show', $b->slug) }}" class="group border border-line rounded-[18px] flex flex-col gap-4 relative overflow-hidden bg-paper hover:-translate-y-1.5 hover:shadow-lg2 transition-all duration-500" style="padding:26px"><div class="flex items-center gap-3.5"><span class="rounded-2xl bg-ink text-white grid place-items-center font-extrabold text-2xl group-hover:-rotate-6 group-hover:scale-105 transition-transform duration-500 overflow-hidden" style="width:52px;height:52px">@php $logo = $b->getFirstMediaUrl('logo', 'thumb'); @endphp @if($logo)<img src="{{ $logo }}" alt="{{ $b->name }}" class="w-full h-full object-cover">@else{{ $b->mark }}@endif</span><div><h3 class="font-extrabold text-lg tracking-tight">{{ $b->name }}</h3><div class="text-xs text-ink/52 font-semibold">{{ $b->category_label }}</div></div></div><p class="text-sm text-ink/52 leading-relaxed">{{ $b->description }}</p><div class="flex items-center justify-between mt-auto pt-4 border-t border-line"><span class="text-[13px] text-ink/52 font-semibold">{{ $b->products_count }} منتج</span><span class="text-sm font-bold text-accentDark inline-flex gap-1.5 group-hover:gap-2.5 transition-all">زيارة المتجر <span>←</span></span></div></a>
    @endforeach
  </div>
</section>

<!-- PRODUCTS -->
<section id="products" class="max-w-[1180px] mx-auto px-5 py-18" style="padding-top:72px;padding-bottom:72px">
  <div class="reveal flex items-end justify-between gap-5 mb-9"><div><span class="text-xs font-bold tracking-[.14em] uppercase text-accentDark block mb-2.5">الأكثر طلبًا · <span class="en tracking-[.18em]">BESTSELLERS</span></span><h2 class="font-extrabold tracking-tight" style="font-size:clamp(24px,3.5vw,36px)">منتجات يحبها عملاؤنا</h2></div><a href="#" class="text-sm font-bold inline-flex items-center gap-1.5 hover:gap-2.5 transition-all whitespace-nowrap">عرض الكل <span>←</span></a></div>
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 stagger">
    @foreach($products as $p)
    <a href="{{ route('product.show', $p->slug) }}" class="group border border-line rounded-[18px] overflow-hidden flex flex-col bg-paper hover:-translate-y-1.5 hover:shadow-lg2 transition-all duration-500">
      <div class="aspect-square bg-gradient-to-br from-paper2 to-paper3 relative overflow-hidden grid place-items-center">
        @if($p->badge)<span class="absolute top-3 start-3 bg-ink text-paper text-[11px] font-bold px-2.5 py-1 rounded-full z-10">{{ $p->badge }}</span>@endif
        @php $cover = $p->getFirstMediaUrl('cover', 'thumb'); @endphp
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
        <span class="text-[11px] text-accentDark font-bold">{{ $p->brand->name }}</span>
        <h3 class="font-bold text-[15px] leading-snug">{{ $p->name }}</h3>
        <span class="text-[12.5px] text-ink/52 font-semibold">★ {{ number_format($p->rating, 1) }} ({{ $p->sales_count }})</span>
        <div class="flex items-baseline gap-1.5 mt-auto pt-2"><span class="font-extrabold text-[21px] tracking-tight">{{ number_format($p->price) }}</span><span class="text-[13px] font-bold">ج.م</span>@if($p->compare_price)<span class="text-[13px] text-ink/38 line-through ms-0.5">{{ number_format($p->compare_price) }}</span>@endif</div>
      </div>
    </a>
    @endforeach
  </div>
</section>

<!-- CTA -->
<section class="max-w-[1180px] mx-auto px-5 pb-18" style="padding-bottom:72px">
  <div class="reveal-scale relative overflow-hidden rounded-[28px] bg-ink text-white text-center" style="padding:56px 48px">
    <div class="absolute -top-3/5 -start-[10%] w-[500px] h-[500px] animate-spinSlow" style="background:radial-gradient(circle,rgba(22,163,74,.22),transparent 65%)"></div>
    <div class="relative z-10">
      <span class="text-xs font-bold tracking-[.14em] uppercase text-accent">جاهز تطلب؟ · <span class="en tracking-[.18em]">READY?</span></span>
      <h2 class="font-extrabold tracking-tight mt-3 mb-3.5" style="font-size:clamp(24px,3.5vw,36px)">اطلب الآن وادفع عند الاستلام</h2>
      <p class="text-white/60 max-w-[440px] mx-auto mb-7">توصيل سريع خلال 24 ساعة لكل المحافظات، مع ضمان استبدال 14 يوم.</p>
      <a href="#products" class="shine animate-ring inline-block bg-accent text-white font-bold rounded-2xl shadow-cta hover:bg-accentDark transition-all" style="padding:16px 28px">ابدأ التسوّق</a>
    </div>
  </div>
</section>

@include('partials.footer')
@endsection

@push('scripts')
<script>
document.documentElement.classList.add('js');
const io=new IntersectionObserver(es=>{es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target);}});},{threshold:.12,rootMargin:'0px 0px -40px 0px'});
document.querySelectorAll('.reveal,.reveal-scale,.stagger').forEach(el=>io.observe(el));
const hdr=document.getElementById('hdr');
addEventListener('scroll',()=>{hdr.classList.toggle('border-line',scrollY>16);hdr.classList.toggle('shadow-soft',scrollY>16);},{passive:true});
</script>
@endpush
