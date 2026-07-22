@extends('layouts.app')
@section('title', 'المنتجات · ' . ($storeName ?? 'متجر العلامات'))

@section('meta')
<meta name="description" content="تصفّح جميع منتجات {{ $storeName ?? 'متجر العلامات' }} — بحث وفلترة سريعة.">
<link rel="canonical" href="{{ route('products.index') }}">
@endsection

@section('content')
@include('partials.strip')
@include('partials.header')

<section class="bg-paper2/70 border-b border-line">
  <div class="max-w-[1180px] mx-auto px-4 sm:px-5 py-10 sm:py-12">
    <span class="inline-flex items-center gap-2 text-[11px] font-black tracking-[.16em] uppercase text-brand mb-2.5">
      <span class="w-1.5 h-1.5 rounded-full bg-brand"></span>المنتجات
    </span>
    <h1 class="font-extrabold tracking-tight text-ink" style="font-size:clamp(28px,4vw,40px)">اكتشف المنتجات</h1>
    <p class="text-muted mt-2 max-w-xl font-medium">{{ $products->total() }} منتج متاح — ابحث أو صفِّ حسب البراند.</p>

    <form method="GET" action="{{ route('products.index') }}" class="mt-6 flex flex-col sm:flex-row gap-3">
      <div class="souqi-search flex-1 max-w-none">
        <svg class="w-4 h-4 text-ink/35 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="search" name="q" value="{{ $q }}" placeholder="ابحث عن منتج…" aria-label="بحث المنتجات">
      </div>
      <select name="brand" class="rounded-2xl border border-line bg-white px-4 py-2.5 text-sm font-bold text-ink min-w-[160px]" onchange="this.form.submit()">
        <option value="">كل البراندات</option>
        @foreach($brands as $b)
        <option value="{{ $b->id }}" @selected((string)$brandId === (string)$b->id)>{{ $b->name }}</option>
        @endforeach
      </select>
      <button type="submit" class="shine bg-ink text-white font-extrabold rounded-2xl px-6 py-2.5 text-sm hover:bg-ink2 transition">تطبيق</button>
    </form>
  </div>
</section>

<section class="max-w-[1180px] mx-auto px-4 sm:px-5 py-10">
  @if($products->isNotEmpty())
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5 sm:gap-5">
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
