@props(['brand', 'active' => 'home'])

@php
  $tabs = [
    'home' => ['label' => 'الرئيسية', 'route' => route('brand.show', $brand->slug)],
    'brands' => ['label' => 'البراندات', 'route' => route('brand.manufacturers', $brand->slug)],
    'shop' => ['label' => 'المنتجات', 'route' => route('brand.shop', $brand->slug)],
  ];
@endphp

<nav class="sticky top-[56px] md:top-[70px] z-40 bg-paper border-b border-line shadow-sm" aria-label="تنقل المتجر">
  <div class="max-w-[1180px] mx-auto px-4 sm:px-5">
    <div class="flex gap-1 overflow-x-auto scrollbar-none py-2 -mx-1">
      @foreach($tabs as $key => $tab)
        <a href="{{ $tab['route'] }}"
           class="flex-shrink-0 px-4 py-2.5 rounded-xl text-sm font-bold transition-all {{ $active === $key ? 'bg-ink text-white shadow-md' : 'text-ink/55 hover:bg-paper2 hover:text-ink' }}"
           @if($active === $key) aria-current="page" @endif>
          {{ $tab['label'] }}
        </a>
      @endforeach
    </div>
  </div>
</nav>
