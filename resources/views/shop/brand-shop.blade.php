@extends('layouts.app')
@section('title', $seo['title'] ?? ('منتجات · ' . $brand->name))

@section('meta')
@include('partials.brand-meta', ['brand' => $brand, 'seo' => $seo, 'fbPageView' => $fbPageView, 'breadcrumbLabel' => 'المنتجات'])
@endsection

@section('content')
@include('partials.strip')
@include('partials.header')

@include('partials.brand-hero', ['brand' => $brand, 'compact' => true, 'productCount' => $products->count()])
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

<section class="max-w-[1180px] mx-auto px-4 sm:px-5 py-6 sm:py-8" style="padding-bottom:88px">
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
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5 stagger">
      @foreach($products as $p)
        @include('partials.product-card', ['product' => $p, 'storeBrand' => $brand])
      @endforeach
    </div>
  @endif
</section>

@if($brand->whatsapp)
<a href="https://wa.me/{{ $brand->whatsapp }}" target="_blank" rel="noopener" title="تواصل"
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
