{{-- Block: brands_marquee — scrolling brand strip --}}
@php $blockBrands = $block->resolvedBrands ?? collect(); @endphp
@if($blockBrands->isNotEmpty())
<section class="py-6.5 border-y border-line bg-paper2 overflow-hidden marq-mask" style="padding:26px 0">
  <div class="flex gap-4.5 w-max animate-marquee" style="gap:18px">
    @foreach($blockBrands->concat($blockBrands) as $b)
      <a href="{{ route('brand.show', $b->slug) }}" class="flex items-center gap-2.5 border border-line rounded-full bg-paper font-bold text-[15px] whitespace-nowrap hover:-translate-y-0.5 hover:border-ink transition-all" style="padding:10px 18px">
        <span class="rounded-lg bg-ink text-paper grid place-items-center text-[13px] font-extrabold overflow-hidden" style="width:30px;height:30px">
          @php $logo = $b->getFirstMediaUrl('logo', 'thumb'); @endphp
          @if($logo)<img src="{{ $logo }}" alt="{{ $b->name }}" class="w-full h-full object-cover" loading="lazy">@else{{ $b->mark }}@endif
        </span>
        {{ $b->name }} <span class="en text-[11px] text-ink/52 font-semibold">· {{ \Illuminate\Support\Str::after($b->category_label ?? '', '· ') }}</span>
      </a>
    @endforeach
  </div>
</section>
@endif
