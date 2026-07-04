{{-- Mobile-first store filters (Noon/Amazon style) --}}
@props([
    'filterDepartments' => collect(),
    'filterBrandGroups' => collect(),
    'productCount' => 0,
])

@php
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
    touch-action: manipulation;
  }
  .filter-pill:active { transform: scale(.97); }
  .filter-pill.is-active {
    background: #0a0a0a;
    border-color: #0a0a0a;
    color: #fff;
  }
  .filter-pill .pill-count {
    font-size: 0.6875rem;
    font-weight: 600;
    opacity: .55;
  }
  .filter-pill.is-active .pill-count { opacity: .75; }
  .filter-sort-pill {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
  }
  @media (min-width: 768px) {
    .filter-scroll-row { padding-inline: 1.25rem; gap: 0.625rem; }
    .filter-pill { padding: 0.5625rem 1rem; font-size: 0.875rem; }
  }
</style>

<div id="storeFilterBar" class="sticky top-[56px] md:top-[70px] z-30 bg-paper/95 backdrop-blur-xl border-b border-line shadow-[0_4px_20px_rgba(0,0,0,.04)]">
  {{-- شريط علوي مدمج --}}
  <div class="flex items-center justify-between gap-3 px-4 py-2 border-b border-line/50">
    <p id="storeResultCount" class="text-[12px] font-bold text-ink/45">
      <span id="storeResultNum">{{ $productCount }}</span> منتج
    </p>
    <button type="button" id="storeFilterReset"
            class="hidden text-[11px] font-bold text-accentDark px-2.5 py-1 rounded-full bg-accent/10">
      مسح الفلتر
    </button>
  </div>

  @if($hasDepartments)
  <div class="py-2.5">
    <p class="px-4 mb-1.5 text-[10px] font-bold tracking-wide text-ink/35 uppercase">القسم</p>
    <div class="filter-scroll-row" data-store-dept-filter role="tablist" aria-label="الأقسام">
      <button type="button" class="filter-pill is-active" data-dept="" role="tab" aria-selected="true">الكل</button>
      @foreach($filterDepartments as $dept)
      <button type="button" class="filter-pill" data-dept="{{ $dept->id }}" role="tab" aria-selected="false">
        {{ $dept->name }}
        <span class="pill-count">{{ $dept->display_count }}</span>
      </button>
      @endforeach
    </div>
  </div>
  @endif

  @if($hasBrands)
  <div class="py-2.5 border-t border-line/40 {{ $hasDepartments ? '' : 'pt-3' }}" id="storeBrandRow">
    <p class="px-4 mb-1.5 text-[10px] font-bold tracking-wide text-ink/35 uppercase">البراند</p>
    <div class="filter-scroll-row" data-store-brand-filter role="tablist" aria-label="البراندات">
      <button type="button" class="filter-pill is-active" data-brand-ids="" data-parent-ids="" role="tab" aria-selected="true">الكل</button>
      @foreach($filterBrandGroups as $group)
      <button type="button"
              class="filter-pill"
              data-brand-ids="{{ $group->ids }}"
              data-parent-ids="{{ $group->parent_ids }}"
              role="tab"
              aria-selected="false">
        {{ $group->name }}
        <span class="pill-count">{{ $group->count }}</span>
      </button>
      @endforeach
    </div>
  </div>
  @endif

  <div class="py-2.5 bg-paper2/70 border-t border-line/40">
    <div class="filter-scroll-row" data-store-sort-filter role="tablist" aria-label="الترتيب">
      <button type="button" class="filter-pill filter-sort-pill is-active" data-sort="all">الكل</button>
      <button type="button" class="filter-pill filter-sort-pill" data-sort="bestseller">🔥 الأكثر مبيعًا</button>
      <button type="button" class="filter-pill filter-sort-pill" data-sort="new">✨ جديد</button>
      <button type="button" class="filter-pill filter-sort-pill" data-sort="deals">🏷️ عروض</button>
    </div>
  </div>
</div>
