@extends('layouts.app')
@section('title', $seo['title'] ?? ('منتجات · ' . $brand->name))

@section('meta')
@include('partials.brand-meta', ['brand' => $brand, 'seo' => $seo, 'fbPageView' => $fbPageView, 'breadcrumbLabel' => 'المنتجات'])
@endsection

@section('content')
@include('partials.brand-page-styles')
@include('partials.strip')
@include('partials.header')

@include('partials.brand-hero', ['brand' => $brand, 'compact' => true, 'brandStats' => $brandStats, 'showActions' => false])
@include('partials.brand-nav', ['brand' => $brand, 'active' => 'shop'])

@include('partials.shop-search-filter', [
  'brand' => $brand,
  'filterDepartments' => $filterDepartments,
  'filterBrandGroups' => $filterBrandGroups,
  'productCount' => $products->count(),
  'searchQuery' => $searchQuery,
  'deptId' => $deptId,
  'manufacturerSlug' => $manufacturerSlug,
  'sort' => $sort ?? 'all',
])

<section class="max-w-[1180px] mx-auto px-4 sm:px-5 py-6 sm:py-8 brand-safe-bottom">
  @if($searchQuery || $deptId || $manufacturerSlug)
    <div class="mb-4 flex flex-wrap gap-2 items-center">
      <span class="text-xs font-bold text-ink/40">نتائج:</span>
      @if($searchQuery)
        <span class="text-xs font-bold bg-paper2 border border-line px-2.5 py-1 rounded-full">"{{ $searchQuery }}"</span>
      @endif
      @if($deptId)
        @php $activeDept = $filterDepartments->firstWhere('id', $deptId); @endphp
        @if($activeDept)
          <span class="text-xs font-bold bg-paper2 border border-line px-2.5 py-1 rounded-full">{{ $activeDept->name }}</span>
        @endif
      @endif
      @if($activeManufacturer ?? null)
        <span class="text-xs font-bold bg-paper2 border border-line px-2.5 py-1 rounded-full">{{ $activeManufacturer->name }}</span>
      @endif
    </div>
  @endif

  @if($products->isEmpty())
    <div class="text-center py-20 px-4">
      <div class="text-5xl mb-4">🔍</div>
      <h2 class="font-extrabold text-xl mb-2">لا توجد منتجات</h2>
      <p class="text-ink/45 text-sm mb-6">جرّب تغيير البحث أو مسح الفلاتر</p>
      <a href="{{ route('brand.shop', $brand->slug) }}" class="inline-flex px-5 py-2.5 rounded-xl bg-ink text-white text-sm font-bold">عرض كل المنتجات</a>
    </div>
  @else
    <div class="grid brand-product-grid">
      @foreach($products as $p)
        <div class="product-pop" style="animation-delay:{{ min($loop->index * 0.04, 0.35) }}s">
          @include('partials.product-card', ['product' => $p, 'storeBrand' => $brand, 'compact' => true])
        </div>
      @endforeach
    </div>
  @endif
</section>

@include('partials.brand-wa-fab', ['brand' => $brand])

<footer class="bg-ink text-paper py-7"><div class="max-w-[1180px] mx-auto px-5 text-center text-[13px] text-white/40">© {{ date('Y') }} {{ $storeName ?? 'متجر العلامات' }}</div></footer>
@endsection

@push('scripts')
<script>
document.documentElement.classList.add('js');
const io=new IntersectionObserver(es=>{es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target);}});},{threshold:.1});
document.querySelectorAll('.stagger').forEach(el=>io.observe(el));
</script>
@endpush
