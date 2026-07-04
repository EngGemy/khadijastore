@extends('layouts.app')
@section('title', ($seo['title'] ?? $brand->name . ' · متجر العلامات'))

@section('meta')
@php $seoMeta = $seo ?? []; @endphp
@if(!empty($seoMeta['description']))<meta name="description" content="{{ $seoMeta['description'] }}">@endif
<link rel="canonical" href="{{ $seoMeta['url'] ?? url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $seoMeta['title'] ?? $brand->name }}">
<meta property="og:description" content="{{ $seoMeta['description'] ?? '' }}">
<meta property="og:url" content="{{ $seoMeta['url'] ?? url()->current() }}">
@if(!empty($seoMeta['image']))<meta property="og:image" content="{{ $seoMeta['image'] }}">@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoMeta['title'] ?? $brand->name }}">

<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BreadcrumbList",
  "itemListElement": [
    {"@@type":"ListItem","position":1,"name":"الرئيسية","item":"{{ url('/') }}"},
    {"@@type":"ListItem","position":2,"name":"{{ e($brand->name) }}","item":"{{ $seoMeta['url'] ?? url()->current() }}"}
  ]
}
</script>

<x-facebook-pixel
    :brand-id="$brand->id"
    :page-view-event-id="$fbPageView['event_id'] ?? null"
/>

@endsection

@section('content')
@include('partials.strip')

@include('partials.header')

<!-- BANNER -->
<section class="relative overflow-hidden bg-ink text-white max-sm:py-8 sm:py-12 md:py-15">
  <div class="absolute -top-2/5 -end-[5%] w-[480px] h-[480px] animate-spinSlow" style="background:radial-gradient(circle,rgba(22,163,74,.18),transparent 65%)"></div>
  <div class="absolute inset-0 opacity-[.04]" style="background-image:radial-gradient(circle at 1px 1px,#fff 1px,transparent 0);background-size:28px 28px"></div>
  <div class="max-w-[1180px] mx-auto px-4 sm:px-5 relative z-10 flex items-center gap-4 sm:gap-7 max-sm:flex-col max-sm:text-center">
    <div id="brandMark" class="w-[72px] h-[72px] sm:w-[90px] sm:h-[90px] rounded-2xl sm:rounded-3xl bg-white text-ink grid place-items-center font-extrabold text-3xl sm:text-4xl shrink-0 animate-floaty overflow-hidden" style="box-shadow:0 16px 40px -8px rgba(0,0,0,.4)">@php $logo = $brand->getFirstMediaUrl('logo', 'thumb'); @endphp @if($logo)<img src="{{ $logo }}" alt="{{ $brand->name }}" class="w-full h-full object-cover">@else{{ $brand->mark }}@endif</div>
    <div class="min-w-0 flex-1">
      <span id="brandCat" class="inline-block bg-white/10 border border-white/15 text-[10px] sm:text-xs font-semibold rounded-full mb-2 sm:mb-3 tracking-wide px-3 py-1">{{ $brand->category_label }}</span>
      <h1 id="brandTitle" class="font-extrabold tracking-tight truncate sm:whitespace-normal" style="font-size:clamp(22px,5vw,44px)">{{ $brand->name }}</h1>
      <p id="brandDesc" class="text-white/60 text-[13px] sm:text-[15px] max-w-[440px] mt-1.5 sm:mt-2 leading-relaxed line-clamp-2 sm:line-clamp-none">{{ $brand->description }}</p>
      @php
        $brandRatingStat = number_format($brand->products->avg('rating') ?? 0, 1);
        $brandSalesStat  = $brand->products->sum('sales_count') ?? 0;
        $brandProductCount = $brand->products->count();
      @endphp
      <div class="flex gap-5 sm:gap-7 mt-3 sm:mt-4 max-sm:justify-center"><div><div class="font-extrabold text-[22px]">{{ $brandProductCount }}</div><div class="text-xs text-white/45 mt-0.5">منتج</div></div><div><div class="font-extrabold text-[22px]">{{ $brandRatingStat }}★</div><div class="text-xs text-white/45 mt-0.5">التقييم</div></div><div><div class="font-extrabold text-[22px]">+{{ number_format($brandSalesStat) }}</div><div class="text-xs text-white/45 mt-0.5">مبيعات</div></div></div>
    </div>
  </div>
</section>

<!-- FILTER -->
@include('partials.store-product-filter', [
  'filterDepartments' => $filterDepartments ?? collect(),
  'filterBrandGroups' => $filterBrandGroups ?? collect(),
  'productCount' => $brand->products->count(),
])

<section class="max-w-[1180px] mx-auto px-4 sm:px-5 py-6 sm:py-11" style="padding-bottom:72px">
  <div id="prodGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 stagger">
    @foreach($brand->products as $p)
    <a href="{{ route('product.show', $p->slug) }}"
       class="store-product-card group border border-line rounded-[18px] overflow-hidden flex flex-col bg-paper hover:-translate-y-1.5 hover:shadow-lg2 transition-all duration-500"
       data-category-id="{{ $p->category_id }}"
       data-category-parent="{{ $p->category?->parent_id ?? '' }}"
       data-sales="{{ $p->sales_count ?? 0 }}"
       data-featured="{{ $p->is_featured ? '1' : '0' }}"
       data-has-deal="{{ ($p->compare_price && $p->compare_price > $p->price) ? '1' : '0' }}"
       data-is-new="{{ $p->created_at && $p->created_at->gt(now()->subDays(30)) ? '1' : '0' }}">
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
  <p id="storeFilterEmpty" class="hidden text-center text-sm text-ink/45 py-16">لا توجد منتجات مطابقة للفلتر الحالي.</p>
</section>

<a href="https://wa.me/{{ $brand->whatsapp }}" id="brandWaFloat" target="_blank" title="تواصل مع البراند" class="fixed bottom-6.5 start-6.5 z-50 w-[58px] h-[58px] rounded-full bg-accent text-white grid place-items-center animate-ring hover:scale-110 hover:-translate-y-0.5 transition-all" style="bottom:26px;inset-inline-start:26px;box-shadow:0 12px 30px -6px rgba(22,163,74,.55)"><svg class="w-7 h-7 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg></a>

<footer class="bg-ink text-paper py-7"><div class="max-w-[1180px] mx-auto px-5 text-center text-[13px] text-white/40">© 2026 متجر العلامات · جميع الحقوق محفوظة</div></footer>


@endsection

@push('scripts')
<script>
document.documentElement.classList.add('js');
const io=new IntersectionObserver(es=>{es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target);}});},{threshold:.1});
document.querySelectorAll('.stagger').forEach(el=>io.observe(el));

(function () {
  const grid = document.getElementById('prodGrid');
  const cards = grid ? Array.from(grid.querySelectorAll('.store-product-card')) : [];
  const emptyState = document.getElementById('storeFilterEmpty');
  const resultNum = document.getElementById('storeResultNum');
  const resetBtn = document.getElementById('storeFilterReset');
  const brandRow = document.getElementById('storeBrandRow');

  let activeDept = '';
  let activeBrandIds = [];
  let activeSort = 'all';

  function setPillActive(container, activeBtn) {
    container.querySelectorAll('.filter-pill').forEach(btn => {
      const on = btn === activeBtn;
      btn.classList.toggle('is-active', on);
      btn.setAttribute('aria-selected', on ? 'true' : 'false');
    });
  }

  function updateBrandVisibility() {
    if (!brandRow) return;
    const brandContainer = brandRow.querySelector('[data-store-brand-filter]');
    if (!brandContainer) return;

    brandContainer.querySelectorAll('.filter-pill[data-brand-ids]').forEach(btn => {
      if (btn.dataset.brandIds === '') return;
      const parentIds = (btn.dataset.parentIds || '').split(',').filter(Boolean);
      const show = !activeDept || parentIds.includes(activeDept);
      btn.style.display = show ? '' : 'none';
    });

    const allBtn = brandContainer.querySelector('.filter-pill[data-brand-ids=""]');
    if (allBtn) {
      setPillActive(brandContainer, allBtn);
      activeBrandIds = [];
    }
  }

  function matchesFilters(card) {
    const catId = card.dataset.categoryId || '';
    const catParent = card.dataset.categoryParent || '';

    if (activeBrandIds.length) {
      if (!activeBrandIds.includes(catId)) return false;
    } else if (activeDept) {
      if (catId !== activeDept && catParent !== activeDept) return false;
    }

    if (activeSort === 'bestseller') return parseInt(card.dataset.sales || '0', 10) > 0;
    if (activeSort === 'new') return card.dataset.isNew === '1';
    if (activeSort === 'deals') return card.dataset.hasDeal === '1';

    return true;
  }

  function applyStoreFilters() {
    let matched = cards.filter(matchesFilters);

    if (activeSort === 'bestseller') {
      matched = matched.slice().sort((a, b) =>
        parseInt(b.dataset.sales || '0', 10) - parseInt(a.dataset.sales || '0', 10)
      );
      matched.forEach(card => grid.appendChild(card));
    }

    let visible = 0;
    cards.forEach(card => {
      const show = matched.includes(card);
      card.style.display = show ? '' : 'none';
      if (show) visible++;
    });

    if (resultNum) resultNum.textContent = visible;
    if (emptyState) emptyState.classList.toggle('hidden', visible > 0);
    if (resetBtn) {
      resetBtn.classList.toggle('hidden', !activeDept && !activeBrandIds.length && activeSort === 'all');
    }
  }

  document.querySelectorAll('[data-store-dept-filter] .filter-pill').forEach(btn => {
    btn.addEventListener('click', function () {
      setPillActive(this.parentElement, this);
      activeDept = this.dataset.dept || '';
      updateBrandVisibility();
      applyStoreFilters();
    });
  });

  document.querySelectorAll('[data-store-brand-filter] .filter-pill').forEach(btn => {
    btn.addEventListener('click', function () {
      setPillActive(this.parentElement, this);
      const ids = (this.dataset.brandIds || '').split(',').filter(Boolean);
      activeBrandIds = ids;
      applyStoreFilters();
    });
  });

  document.querySelectorAll('[data-store-sort-filter] .filter-pill').forEach(btn => {
    btn.addEventListener('click', function () {
      setPillActive(this.parentElement, this);
      activeSort = this.dataset.sort || 'all';
      applyStoreFilters();
    });
  });

  if (resetBtn) {
    resetBtn.addEventListener('click', function () {
      activeDept = '';
      activeBrandIds = [];
      activeSort = 'all';
      document.querySelectorAll('[data-store-dept-filter], [data-store-brand-filter], [data-store-sort-filter]').forEach(row => {
        const first = row.querySelector('.filter-pill');
        if (first) setPillActive(row, first);
      });
      updateBrandVisibility();
      applyStoreFilters();
    });
  }

  updateBrandVisibility();
  applyStoreFilters();
})();
</script>
@endpush
