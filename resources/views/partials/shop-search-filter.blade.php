{{-- Server-side shop filter — compact on mobile via «فلترة» sheet --}}
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
  $activeFilterCount = (int) (bool) $deptId
    + (int) (bool) $manufacturerSlug
    + (int) ($sort !== 'all');
  $sortLabels = ['all' => 'الكل', 'bestseller' => 'الأكثر مبيعًا', 'new' => 'جديد', 'deals' => 'عروض'];
@endphp

<style>
  .filter-scroll-row {
    display: flex; gap: 0.5rem; overflow-x: auto;
    overscroll-behavior-x: contain; scroll-snap-type: x proximity;
    -webkit-overflow-scrolling: touch; scrollbar-width: none;
    padding-inline: 1rem;
  }
  .filter-scroll-row::-webkit-scrollbar { display: none; }
  .filter-pill {
    flex-shrink: 0; scroll-snap-align: start;
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 0.5rem 0.875rem; border-radius: 999px;
    border: 1.5px solid rgba(11,29,54,.1); background: #fff;
    color: rgba(11,29,54,.55); font-size: 0.8125rem; font-weight: 700;
    white-space: nowrap; text-decoration: none; transition: all .2s ease;
  }
  .filter-pill.is-active { background: #0B1D36; border-color: #0B1D36; color: #fff; }
  .filter-pill .pill-count { font-size: 0.6875rem; font-weight: 600; opacity: .55; }
  .filter-pill.is-active .pill-count { opacity: .75; }

  .shop-filter-bar { position: sticky; top: 56px; z-index: 40; background: rgba(255,255,255,.95); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(11,29,54,.08); }
  @media(min-width:768px){ .shop-filter-bar{ top: 68px; } }

  .shop-filter-mobile-tools { display: flex; align-items: center; gap: 8px; padding: 8px 16px 12px; }
  .shop-filter-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    min-height: 44px; padding: 0 14px; border-radius: 14px;
    border: 1.5px solid rgba(11,29,54,.12); background: #fff;
    font-size: 13px; font-weight: 800; color: #0B1D36; cursor: pointer; font-family: inherit;
  }
  .shop-filter-btn--active { border-color: #E85D04; background: #FFF0E6; color: #C2410C; }
  .shop-filter-badge {
    min-width: 20px; height: 20px; padding: 0 6px; border-radius: 999px;
    background: #E85D04; color: #fff; font-size: 11px; font-weight: 900;
    display: inline-grid; place-items: center;
  }
  .shop-filter-count { font-size: 12px; font-weight: 700; color: rgba(11,29,54,.45); margin-inline-start: auto; }

  .shop-filter-desktop { display: none; }
  @media(min-width:768px){
    .shop-filter-mobile-tools { display: none; }
    .shop-filter-desktop { display: block; }
  }

  .shop-filter-sheet {
    position: fixed; inset: 0; z-index: 80;
    background: rgba(11,29,54,.45); backdrop-filter: blur(3px);
    display: flex; align-items: flex-end; justify-content: center;
  }
  .shop-filter-sheet__panel {
    width: 100%; max-width: 520px; max-height: min(82vh, 640px);
    background: #fff; border-radius: 22px 22px 0 0;
    padding: 12px 0 calc(16px + env(safe-area-inset-bottom,0px));
    box-shadow: 0 -16px 48px rgba(11,29,54,.16);
    display: flex; flex-direction: column; overflow: hidden;
  }
  .shop-filter-sheet__handle { width: 40px; height: 4px; border-radius: 999px; background: rgba(11,29,54,.12); margin: 4px auto 10px; }
  .shop-filter-sheet__head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 16px 12px; border-bottom: 1px solid rgba(11,29,54,.08);
  }
  .shop-filter-sheet__title { margin: 0; font-size: 16px; font-weight: 900; color: #0B1D36; }
  .shop-filter-sheet__body { overflow-y: auto; flex: 1; -webkit-overflow-scrolling: touch; }
  .shop-filter-sheet__section { padding: 14px 0; border-bottom: 1px solid rgba(11,29,54,.06); }
  .shop-filter-sheet__label {
    padding: 0 16px 8px; margin: 0;
    font-size: 11px; font-weight: 800; letter-spacing: .04em; color: rgba(11,29,54,.4); text-transform: uppercase;
  }
  [x-cloak]{display:none!important}
</style>

<div class="shop-filter-bar" x-data="{ open: false }" @keydown.escape.window="open = false">
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

  {{-- Mobile: one «فلترة» control — products stay above the fold --}}
  <div class="shop-filter-mobile-tools md:hidden">
    <button type="button" class="shop-filter-btn {{ $activeFilterCount ? 'shop-filter-btn--active' : '' }}"
            @click="open = true" aria-haspopup="dialog" :aria-expanded="open.toString()">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h18M6 12h12M10 19h4"/></svg>
      فلترة
      @if($activeFilterCount)
        <span class="shop-filter-badge">{{ $activeFilterCount }}</span>
      @endif
    </button>
    @if($deptId || $manufacturerSlug || $searchQuery || $sort !== 'all')
      <a href="{{ $shopRoute() }}" class="text-[11px] font-bold text-accentDark px-2.5 py-1 rounded-full bg-accent/10">مسح</a>
    @endif
    <span class="shop-filter-count">{{ $productCount }} منتج</span>
  </div>

  {{-- Desktop: full filter rows --}}
  <div class="shop-filter-desktop">
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
        <a href="{{ $shopRoute($allDeptParams) }}" class="filter-pill {{ !$deptId ? 'is-active' : '' }}">الكل</a>
        @foreach($filterDepartments as $dept)
          @php $deptParams = array_merge($baseParams, ['dept' => $dept->id], array_filter(['manufacturer' => $manufacturerSlug ?: null])); @endphp
          <a href="{{ $shopRoute($deptParams) }}" class="filter-pill {{ (int)$deptId === $dept->id ? 'is-active' : '' }}">
            {{ $dept->name }} <span class="pill-count">{{ $dept->display_count }}</span>
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
        <a href="{{ $shopRoute($allMfgParams) }}" class="filter-pill {{ !$manufacturerSlug ? 'is-active' : '' }}">الكل</a>
        @foreach($filterBrandGroups as $group)
          @if(!$deptId || str_contains($group->parent_ids, (string)$deptId))
            @php $mfgParams = array_merge($baseParams, ['manufacturer' => $group->slug], array_filter(['dept' => $deptId ?: null])); @endphp
            <a href="{{ $shopRoute($mfgParams) }}" class="filter-pill {{ $manufacturerSlug === $group->slug ? 'is-active' : '' }}">
              {{ $group->name }} <span class="pill-count">{{ $group->count }}</span>
            </a>
          @endif
        @endforeach
      </div>
    </div>
    @endif

    <div class="py-2.5 bg-paper2/70 border-t border-line/40">
      <div class="filter-scroll-row">
        @foreach($sortLabels as $key => $label)
          @php $sortParams = array_merge($baseParams, array_filter(['sort' => $key !== 'all' ? $key : null, 'dept' => $deptId ?: null, 'manufacturer' => $manufacturerSlug ?: null])); @endphp
          <a href="{{ $shopRoute($sortParams) }}" class="filter-pill {{ $sort === $key ? 'is-active' : '' }}">{{ $label }}</a>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Mobile bottom sheet --}}
  <div class="shop-filter-sheet md:hidden" x-show="open" x-cloak x-transition.opacity
       role="dialog" aria-modal="true" aria-label="فلترة المنتجات"
       @click.self="open = false">
    <div class="shop-filter-sheet__panel" @click.stop x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
         style="transform: translateY(0)">
      <div class="shop-filter-sheet__handle" aria-hidden="true"></div>
      <div class="shop-filter-sheet__head">
        <h3 class="shop-filter-sheet__title">فلترة</h3>
        <button type="button" class="shop-filter-btn" @click="open = false" aria-label="إغلاق">إغلاق</button>
      </div>
      <div class="shop-filter-sheet__body">
        <div class="shop-filter-sheet__section">
          <p class="shop-filter-sheet__label">الترتيب</p>
          <div class="filter-scroll-row">
            @foreach($sortLabels as $key => $label)
              @php $sortParams = array_merge($baseParams, array_filter(['sort' => $key !== 'all' ? $key : null, 'dept' => $deptId ?: null, 'manufacturer' => $manufacturerSlug ?: null])); @endphp
              <a href="{{ $shopRoute($sortParams) }}" class="filter-pill {{ $sort === $key ? 'is-active' : '' }}">{{ $label }}</a>
            @endforeach
          </div>
        </div>

        @if($hasDepartments)
        <div class="shop-filter-sheet__section">
          <p class="shop-filter-sheet__label">القسم</p>
          <div class="filter-scroll-row" style="flex-wrap:wrap;overflow:visible;padding-bottom:4px">
            @php $allDeptParams = array_merge($baseParams, array_filter(['manufacturer' => $manufacturerSlug ?: null])); @endphp
            <a href="{{ $shopRoute($allDeptParams) }}" class="filter-pill {{ !$deptId ? 'is-active' : '' }}">الكل</a>
            @foreach($filterDepartments as $dept)
              @php $deptParams = array_merge($baseParams, ['dept' => $dept->id], array_filter(['manufacturer' => $manufacturerSlug ?: null])); @endphp
              <a href="{{ $shopRoute($deptParams) }}" class="filter-pill {{ (int)$deptId === $dept->id ? 'is-active' : '' }}">
                {{ $dept->name }} <span class="pill-count">{{ $dept->display_count }}</span>
              </a>
            @endforeach
          </div>
        </div>
        @endif

        @if($hasBrands)
        <div class="shop-filter-sheet__section">
          <p class="shop-filter-sheet__label">البراند</p>
          <div class="filter-scroll-row" style="flex-wrap:wrap;overflow:visible;padding-bottom:4px">
            @php $allMfgParams = array_merge($baseParams, array_filter(['dept' => $deptId ?: null])); @endphp
            <a href="{{ $shopRoute($allMfgParams) }}" class="filter-pill {{ !$manufacturerSlug ? 'is-active' : '' }}">الكل</a>
            @foreach($filterBrandGroups as $group)
              @if(!$deptId || str_contains($group->parent_ids, (string)$deptId))
                @php $mfgParams = array_merge($baseParams, ['manufacturer' => $group->slug], array_filter(['dept' => $deptId ?: null])); @endphp
                <a href="{{ $shopRoute($mfgParams) }}" class="filter-pill {{ $manufacturerSlug === $group->slug ? 'is-active' : '' }}">
                  {{ $group->name }} <span class="pill-count">{{ $group->count }}</span>
                </a>
              @endif
            @endforeach
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
