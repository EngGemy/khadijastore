@extends('layouts.app')
@section('title', 'المنتجات · ' . ($storeName ?? 'متجر العلامات'))

@section('meta')
<meta name="description" content="تصفّح جميع منتجات {{ $storeName ?? 'متجر العلامات' }} — بحث وفلترة سريعة.">
<link rel="canonical" href="{{ route('products.index') }}">
@endsection

@section('content')
@include('partials.strip')
@include('partials.header')

@php
  $plpFeatured = $featuredBrand ?? featured_storefront_brand();
  $plpStrip = $featuredStrip ?? collect();
@endphp
<section class="bg-paper2/70 border-b border-line">
  <div class="max-w-[1180px] mx-auto px-4 sm:px-5 py-6 sm:py-12">
    <span class="inline-flex items-center gap-2 text-[11px] font-black tracking-[.16em] uppercase text-brand mb-2">
      <span class="w-1.5 h-1.5 rounded-full bg-brand"></span>المنتجات
    </span>
    <h1 class="font-extrabold tracking-tight text-ink" style="font-size:clamp(24px,5vw,40px)">اكتشف المنتجات</h1>
    <p class="text-muted mt-1.5 sm:mt-2 max-w-xl font-medium text-[13px] sm:text-base">{{ $products->total() }} منتج متاح — ابحث أو صفِّ حسب البراند.</p>

    @if($plpFeatured)
    <div class="mt-4 flex gap-2 overflow-x-auto app-h-scroll pb-0.5" style="scrollbar-width:none">
      <a href="{{ route('products.index', array_filter(['q' => $q ?: null])) }}"
         class="plp-brand-chip {{ ! $brandId ? 'is-active' : '' }}">الكل</a>
      <a href="{{ route('products.index', array_filter(['brand' => $plpFeatured->id, 'q' => $q ?: null])) }}"
         class="plp-brand-chip plp-brand-chip--featured {{ (string) $brandId === (string) $plpFeatured->id ? 'is-active' : '' }}">
        @include('partials.brand-avatar', ['brand' => $plpFeatured, 'size' => 'xs'])
        متجر سند للعطارة
      </a>
      @foreach($brands->where('id', '!=', $plpFeatured->id)->take(6) as $chipBrand)
      <a href="{{ route('products.index', array_filter(['brand' => $chipBrand->id, 'q' => $q ?: null])) }}"
         class="plp-brand-chip {{ (string) $brandId === (string) $chipBrand->id ? 'is-active' : '' }}">
        {{ $chipBrand->name }}
      </a>
      @endforeach
    </div>
    @endif

    <form method="GET" action="{{ route('products.index') }}" class="mt-4 sm:mt-6 flex flex-col sm:flex-row gap-2.5 sm:gap-3">
      <div class="souqi-search flex-1 max-w-none min-h-[48px]">
        <svg class="w-4 h-4 text-ink/35 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="search" name="q" value="{{ $q }}" placeholder="ابحث عن منتج…" aria-label="بحث المنتجات" class="text-base">
      </div>
      <select name="brand" class="rounded-2xl border border-line bg-white px-4 py-3 sm:py-2.5 text-sm font-bold text-ink min-h-[48px] min-w-[160px]" onchange="this.form.submit()">
        <option value="">كل البراندات</option>
        @foreach($brands as $b)
        <option value="{{ $b->id }}" @selected((string)$brandId === (string)$b->id)>{{ $b->name }}</option>
        @endforeach
      </select>
      <button type="submit" class="shine bg-ink text-white font-extrabold rounded-2xl px-6 py-3 sm:py-2.5 text-sm hover:bg-ink2 transition min-h-[48px]">تطبيق</button>
    </form>
  </div>
</section>

@if($plpFeatured && $plpStrip->isNotEmpty() && ! $brandId)
<section class="max-w-[1180px] mx-auto px-4 sm:px-5 pt-6 sm:pt-8">
  <div class="flex items-end justify-between gap-3 mb-4">
    <div>
      <span class="text-[11px] font-black tracking-[.14em] uppercase text-brand">مختارات سند</span>
      <h2 class="font-extrabold text-ink text-[18px] sm:text-[22px] mt-0.5">منتجات {{ $plpFeatured->name }}</h2>
    </div>
    <a href="{{ route('brand.show', $plpFeatured->slug) }}" class="text-[12px] font-extrabold text-brand whitespace-nowrap">المتجر ←</a>
  </div>
  <div class="flex gap-3 overflow-x-auto app-h-scroll pb-1" style="scrollbar-width:none;-webkit-overflow-scrolling:touch">
    @foreach($plpStrip as $p)
    @php
      $discount = ($p->compare_price && $p->compare_price > $p->price)
        ? round((1 - $p->price / $p->compare_price) * 100) : 0;
    @endphp
    <a href="{{ route('product.show', $p->slug) }}" class="product-card group flex flex-col w-[42vw] max-w-[170px] sm:w-[160px] shrink-0">
      <div class="product-card__media">
        @if($p->badge)<span class="product-card__badge">{{ $p->badge }}</span>@endif
        @if($discount > 0)<span class="product-card__discount">-{{ $discount }}%</span>@endif
        @include('partials.product-cover', ['product' => $p])
      </div>
      <div class="product-card__body !p-3">
        <h2 class="product-card__title !text-[13px]">{{ $p->name }}</h2>
        <div class="product-card__price-row">
          <span class="product-card__price !text-[16px]">{{ number_format($p->price) }}</span>
          <span class="text-[11px] font-bold text-ink/50">ج.م</span>
        </div>
      </div>
    </a>
    @endforeach
  </div>
</section>
@endif

<section class="max-w-[1180px] mx-auto px-4 sm:px-5 py-6 sm:py-10">
  @if($products->isNotEmpty())
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-5 app-stagger">
    @foreach($products as $p)
    @php
      $discount = ($p->compare_price && $p->compare_price > $p->price)
        ? round((1 - $p->price / $p->compare_price) * 100) : 0;
    @endphp
    <a href="{{ route('product.show', $p->slug) }}" class="product-card group flex flex-col">
      <div class="product-card__media">
        @if($p->badge)<span class="product-card__badge">{{ $p->badge }}</span>@endif
        @if($discount > 0)<span class="product-card__discount">-{{ $discount }}%</span>@endif
        @include('partials.product-cover', ['product' => $p])
        <div class="product-card__overlay"><span class="product-card__cta">عرض المنتج ←</span></div>
      </div>
      <div class="product-card__body">
        @if($p->brand)
        <div class="product-card__brand">
          @include('partials.brand-avatar', ['brand' => $p->brand, 'size' => 'xs'])
          <span class="truncate">{{ $p->brand->name }}</span>
        </div>
        @endif
        <h2 class="product-card__title">{{ $p->name }}</h2>
        <div class="product-card__price-row">
          <span class="product-card__price">{{ number_format($p->price) }}</span>
          <span class="text-[11px] font-bold text-ink/50">ج.م</span>
          @if($p->compare_price && $p->compare_price > $p->price)
          <span class="product-card__compare">{{ number_format($p->compare_price) }}</span>
          @endif
        </div>
      </div>
    </a>
    @endforeach
  </div>
  <div class="mt-10">{{ $products->withQueryString()->links() }}</div>
  @else
  <div class="text-center py-16 border border-dashed border-line rounded-2xl bg-paper2">
    <p class="font-bold text-ink/55">لا توجد نتائج مطابقة</p>
    <a href="{{ route('products.index') }}" class="inline-block mt-4 text-sm font-extrabold text-brand">مسح الفلاتر</a>
  </div>
  @endif
</section>

@include('partials.footer')
@endsection
