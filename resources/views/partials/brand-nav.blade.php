@props(['brand', 'active' => 'home'])

@php
  $tabs = [
    'home' => ['label' => 'الرئيسية', 'short' => 'الرئيسية', 'route' => route('brand.show', $brand->slug)],
    'brands' => ['label' => 'براندات مرتبطة', 'short' => 'مرتبطة', 'route' => route('brand.manufacturers', $brand->slug)],
    'shop' => ['label' => 'المنتجات', 'short' => 'المنتجات', 'route' => route('brand.shop', $brand->slug)],
  ];
@endphp

<nav class="brand-subnav" aria-label="تنقل المتجر">
  <div class="brand-subnav__inner">
    <div class="brand-subnav__track" role="tablist">
      @foreach($tabs as $key => $tab)
        <a href="{{ $tab['route'] }}"
           role="tab"
           class="brand-subnav__tab {{ $active === $key ? 'is-active' : '' }}"
           @if($active === $key) aria-current="page" @endif>
          <span class="brand-subnav__label brand-subnav__label--full">{{ $tab['label'] }}</span>
          <span class="brand-subnav__label brand-subnav__label--short">{{ $tab['short'] }}</span>
        </a>
      @endforeach
    </div>
  </div>
</nav>
