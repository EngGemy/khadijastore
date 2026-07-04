@props([
    'imgClass' => 'h-10 w-auto max-w-[160px] object-contain',
    'fallbackClass' => 'w-10 h-10 rounded-xl bg-ink text-paper grid place-items-center font-extrabold text-lg shrink-0',
    'fallbackMark' => 'ع',
    'showName' => false,
    'nameClass' => 'font-extrabold text-lg tracking-tight',
])

@if($storeLogo ?? store_logo_url())
  <img src="{{ $storeLogo ?? store_logo_url() }}" alt="{{ $storeName ?? setting('store.name', 'متجر العلامات') }}" class="{{ $imgClass }}" width="160" height="40">
@else
  <span class="{{ $fallbackClass }}">{{ $fallbackMark }}</span>
@endif

@if($showName)
  <span class="{{ $nameClass }}">{{ $storeName ?? setting('store.name', 'متجر العلامات') }}</span>
@endif
