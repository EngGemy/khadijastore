@props(['mfg', 'brand'])

<a href="{{ route('brand.shop', [$brand->slug, 'manufacturer' => $mfg->slug]) }}"
   class="brand-mfg-chip snap-start flex-shrink-0 group">
  <div class="brand-mfg-chip__icon" style="--from:{{ $mfg->gradient_from }};--to:{{ $mfg->gradient_to }}">
    @if($mfg->image)
      <img src="{{ $mfg->image }}" alt="{{ $mfg->name }}" class="w-[70%] h-[70%] object-contain relative z-10" loading="lazy">
    @else
      <span class="relative z-10 font-extrabold text-white text-sm">{{ mb_substr($mfg->name, 0, 2) }}</span>
    @endif
  </div>
  <span class="brand-mfg-chip__label">{{ $mfg->name }}</span>
</a>
