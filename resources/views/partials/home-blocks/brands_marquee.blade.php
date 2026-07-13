{{-- Block: brands_marquee — scrolling brand strip --}}
@php $blockBrands = $block->resolvedBrands ?? collect(); @endphp
@if($blockBrands->isNotEmpty())
<section @if($assignBrandsAnchor ?? false) id="brands" @endif class="py-6 border-y border-line bg-paper2 overflow-hidden marq-mask">
  <div class="flex gap-4 w-max animate-marquee">
    @foreach($blockBrands->concat($blockBrands) as $b)
      <a href="{{ route('brand.show', $b->slug) }}" class="brand-marquee-pill">
        @include('partials.brand-avatar', ['brand' => $b, 'size' => 'sm'])
        <span>{{ $b->name }}</span>
        @if($b->category_label)
        <span class="en text-[11px] text-ink/45 font-semibold">· {{ \Illuminate\Support\Str::after($b->category_label, '· ') }}</span>
        @endif
      </a>
    @endforeach
  </div>
</section>
@endif
