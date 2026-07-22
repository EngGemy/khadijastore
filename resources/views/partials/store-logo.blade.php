{{-- Recommended store logo: 360×96px (≈ 3.75:1) PNG/WebP transparent, or square 256×256 --}}
@props([
    'imgClass' => 'h-9 w-auto max-w-[130px] max-h-10 object-contain object-center rounded-md shrink-0',
    'fallbackClass' => 'w-9 h-9 rounded-xl bg-ink text-paper grid place-items-center font-extrabold text-base shrink-0',
    'fallbackMark' => 'ع',
    'showName' => false,
    'nameClass' => 'font-extrabold text-lg tracking-tight truncate',
])

@if($storeLogo ?? store_logo_url())
  <img src="{{ $storeLogo ?? store_logo_url() }}" alt="{{ $storeName ?? setting('store.name', 'متجر العلامات') }}" class="{{ $imgClass }}" width="130" height="36" loading="eager" title="شعار المتجر الموصى به: 360×96px أو 256×256">
@else
  <span class="{{ $fallbackClass }}">{{ $fallbackMark }}</span>
@endif

@if($showName)
  <span class="{{ $nameClass }}">{{ $storeName ?? setting('store.name', 'متجر العلامات') }}</span>
@endif
