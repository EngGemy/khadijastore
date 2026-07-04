@props(['brand', 'compact' => false, 'productCount' => 0])

@php $logo = $brand->getFirstMediaUrl('logo', 'thumb'); @endphp

<section class="relative overflow-hidden bg-ink text-white {{ $compact ? 'py-6 sm:py-8' : 'max-sm:py-8 sm:py-12 md:py-14' }}">
  <div class="absolute -top-2/5 -end-[5%] w-[480px] h-[480px] animate-spinSlow pointer-events-none" style="background:radial-gradient(circle,rgba(22,163,74,.18),transparent 65%)"></div>
  <div class="absolute inset-0 opacity-[.04] pointer-events-none" style="background-image:radial-gradient(circle at 1px 1px,#fff 1px,transparent 0);background-size:28px 28px"></div>
  <div class="max-w-[1180px] mx-auto px-4 sm:px-5 relative z-10 flex items-center gap-4 sm:gap-6 max-sm:flex-col max-sm:text-center">
    <div class="w-[64px] h-[64px] sm:w-[80px] sm:h-[80px] rounded-2xl sm:rounded-3xl bg-white text-ink grid place-items-center font-extrabold text-2xl sm:text-3xl shrink-0 animate-floaty overflow-hidden shadow-2xl">
      @if($logo)
        <img src="{{ $logo }}" alt="{{ $brand->name }}" class="w-full h-full object-cover">
      @else
        {{ $brand->mark }}
      @endif
    </div>
    <div class="min-w-0 flex-1">
      @if($brand->category_label)
        <span class="inline-block bg-white/10 border border-white/15 text-[10px] sm:text-xs font-semibold rounded-full mb-2 tracking-wide px-3 py-1">{{ $brand->category_label }}</span>
      @endif
      <h1 class="font-extrabold tracking-tight truncate sm:whitespace-normal" style="font-size:clamp(20px,4.5vw,38px)">{{ $brand->name }}</h1>
      @unless($compact)
        <p class="text-white/60 text-[13px] sm:text-[15px] max-w-[480px] mt-1.5 leading-relaxed line-clamp-2">{{ $brand->description }}</p>
      @endunless
      <div class="flex gap-5 sm:gap-6 mt-3 max-sm:justify-center text-sm">
        <div><span class="font-extrabold text-lg">{{ $productCount }}</span> <span class="text-white/45">منتج</span></div>
      </div>
    </div>
  </div>
</section>
