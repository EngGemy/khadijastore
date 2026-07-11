@props(['brand', 'active' => 'home'])

@php
  $tabs = [
    'home' => ['label' => 'الرئيسية', 'route' => route('brand.show', $brand->slug)],
    'brands' => ['label' => 'براندات مرتبطة', 'route' => route('brand.manufacturers', $brand->slug)],
    'shop' => ['label' => 'المنتجات', 'route' => route('brand.shop', $brand->slug)],
  ];
@endphp

<nav class="sticky top-[52px] sm:top-[64px] z-40 bg-paper/95 backdrop-blur-md border-b border-line" aria-label="تنقل المتجر">
  <div class="max-w-[1180px] mx-auto px-4 sm:px-5">
    <div class="flex gap-1 overflow-x-auto scrollbar-none py-2">
      @foreach($tabs as $key => $tab)
        <a href="{{ $tab['route'] }}"
           class="brand-nav-pill flex-shrink-0 px-3.5 py-2 rounded-xl text-[12px] sm:text-[13px] font-bold transition-colors {{ $active === $key ? 'is-active bg-ink text-white' : 'text-ink/50 hover:text-ink hover:bg-paper2' }}"
           @if($active === $key) aria-current="page" @endif>
          {{ $tab['label'] }}
        </a>
      @endforeach
    </div>
  </div>
</nav>
