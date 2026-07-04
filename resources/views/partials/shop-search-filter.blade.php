{{-- Server-side shop filter (Noon/Amazon style) --}}
@props([
    'brand',
    'filterDepartments' => collect(),
    'filterBrandGroups' => collect(),
    'productCount' => 0,
    'searchQuery' => '',
    'deptId' => null,
    'manufacturerSlug' => '',
    'sort' => 'all',
])

@php
  $baseParams = array_filter([
    'q' => $searchQuery ?: null,
    'sort' => $sort !== 'all' ? $sort : null,
  ]);
  $shopRoute = fn (array $params = []) => route('brand.shop', array_merge(['slug' => $brand->slug], array_filter($params)));
  $hasDepartments = $filterDepartments->isNotEmpty();
  $hasBrands = $filterBrandGroups->isNotEmpty();
@endphp

<style>
  .filter-scroll-row {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    overscroll-behavior-x: contain;
    scroll-snap-type: x proximity;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-inline: 1rem;
  }
  .filter-scroll-row::-webkit-scrollbar { display: none; }
  .filter-pill {
    flex-shrink: 0;
    scroll-snap-align: start;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.5rem 0.875rem;
    border-radius: 999px;
    border: 1.5px solid rgba(10,10,10,.1);
    background: #fff;
    color: rgba(10,10,10,.55);
    font-size: 0.8125rem;
    font-weight: 700;
    white-space: nowrap;
    transition: all .2s ease;
    text-decoration: none;
  }
  .filter-pill.is-active {
    background: #0a0a0a;
    border-color: #0a0a0a;
    color: #fff;
  }
  .filter-pill .pill-count { font-size: 0.6875rem; font-weight: 600; opacity: .55; }
  .filter-pill.is-active .pill-count { opacity: .75; }
</style>

{{-- Search bar --}}
<div class="sticky top-[56px] md:top-[70px] z-40 bg-paper/95 backdrop-blur-xl border-b border-line shadow-sm">
  <div class="max-w-[1180px] mx-auto px-4 py-3">
    <form action="{{ $shopRoute() }}" method="GET" class="relative flex gap-2">
      @if($deptId)<input type="hidden" name="dept" value="{{ $deptId }}">@endif
      @if($manufacturerSlug)<input type="hidden" name="manufacturer" value="{{ $manufacturerSlug }}">@endif
      @if($sort !== 'all')<input type="hidden" name="sort" value="{{ $sort }}">@endif
      <div class="relative flex-1">
        <input type="search" name="q" value="{{ $searchQuery }}"
               placeholder="ابحث في {{ $brand->name }}…"
               class="w-full rounded-xl border border-line bg-paper2 ps-11 pe-4 py-3 text-sm font-semibold outline-none focus:border-ink/25 focus:ring-2 focus:ring-ink/5"
               autocomplete="off">
        <svg class="absolute start-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-ink/35" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      </div>
      <button type="submit" class="shrink-0 px-4 py-3 rounded-xl bg-ink text-white text-sm font-bold">بحث</button>
    </form>
  </div>

  <div class="flex items-center justify-between gap-3 px-4 py-2 border-t border-line/50 max-w-[1180px] mx-auto w-full">
    <p class="text-[12px] font-bold text-ink/45"><span>{{ $productCount }}</span> منتج</p>
    @if($deptId || $manufacturerSlug || $searchQuery || $sort !== 'all')
      <a href="{{ $shopRoute() }}" class="text-[11px] font-bold text-accentDark px-2.5 py-1 rounded-full bg-accent/10">مسح الكل</a>
    @endif
  </div>

  @if($hasDepartments)
  <div class="py-2.5 border-t border-line/40">
    <p class="px-4 mb-1.5 text-[10px] font-bold tracking-wide text-ink/35 uppercase">القسم</p>
    <div class="filter-scroll-row">
      @php $allDeptParams = array_merge($baseParams, array_filter(['manufacturer' => $manufacturerSlug ?: null])); @endphp
      <a href="{{ $shopRoute($allDeptParams) }}"
         class="filter-pill {{ !$deptId ? 'is-active' : '' }}">الكل</a>
      @foreach($filterDepartments as $dept)
        @php $deptParams = array_merge($baseParams, ['dept' => $dept->id], array_filter(['manufacturer' => $manufacturerSlug ?: null])); @endphp
        <a href="{{ $shopRoute($deptParams) }}"
           class="filter-pill {{ (int)$deptId === $dept->id ? 'is-active' : '' }}">
          {{ $dept->name }}
          <span class="pill-count">{{ $dept->display_count }}</span>
        </a>
      @endforeach
    </div>
  </div>
  @endif

  @if($hasBrands)
  <div class="py-2.5 border-t border-line/40">
    <p class="px-4 mb-1.5 text-[10px] font-bold tracking-wide text-ink/35 uppercase">البراند</p>
    <div class="filter-scroll-row">
      @php $allMfgParams = array_merge($baseParams, array_filter(['dept' => $deptId ?: null])); @endphp
      <a href="{{ $shopRoute($allMfgParams) }}"
         class="filter-pill {{ !$manufacturerSlug ? 'is-active' : '' }}">الكل</a>
      @foreach($filterBrandGroups as $group)
        @if(!$deptId || str_contains($group->parent_ids, (string)$deptId))
          @php $mfgParams = array_merge($baseParams, ['manufacturer' => $group->slug], array_filter(['dept' => $deptId ?: null])); @endphp
          <a href="{{ $shopRoute($mfgParams) }}"
             class="filter-pill {{ $manufacturerSlug === $group->slug ? 'is-active' : '' }}">
            {{ $group->name }}
            <span class="pill-count">{{ $group->count }}</span>
          </a>
        @endif
      @endforeach
    </div>
  </div>
  @endif

  <div class="py-2.5 bg-paper2/70 border-t border-line/40">
    <div class="filter-scroll-row">
      @foreach(['all' => 'الكل', 'bestseller' => '🔥 الأكثر مبيعًا', 'new' => '✨ جديد', 'deals' => '🏷️ عروض'] as $key => $label)
        @php $sortParams = array_merge($baseParams, array_filter(['sort' => $key !== 'all' ? $key : null, 'dept' => $deptId ?: null, 'manufacturer' => $manufacturerSlug ?: null])); @endphp
        <a href="{{ $shopRoute($sortParams) }}"
           class="filter-pill {{ $sort === $key ? 'is-active' : '' }}">{{ $label }}</a>
      @endforeach
    </div>
  </div>
</div>
