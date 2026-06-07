{{-- Block: image_cta — banner with background image --}}
@php
  $data    = $block->data ?? [];
  $eyebrow = $data['eyebrow']   ?? '';
  $title   = $data['title']     ?? '';
  $para    = $data['paragraph'] ?? '';
  $btnText = $data['btn_text']  ?? '';
  $btnLink = $data['btn_link']  ?? '#';
  $image   = $data['image']     ?? '';
@endphp
<section class="max-w-[1180px] mx-auto px-5 pb-18" style="padding-bottom:72px">
  <div class="reveal-scale relative overflow-hidden rounded-[28px] bg-ink text-white text-center" style="padding:56px 48px;{{ $image ? 'background-image:url('.e($image).');background-size:cover;background-position:center;' : '' }}">
    @if($image)<div class="absolute inset-0 bg-ink/70"></div>@endif
    <div class="relative z-10">
      @if($eyebrow)<span class="text-xs font-bold tracking-[.14em] uppercase text-accent">{{ $eyebrow }}</span>@endif
      @if($title)<h2 class="font-extrabold tracking-tight mt-3 mb-3.5" style="font-size:clamp(24px,3.5vw,36px)">{{ $title }}</h2>@endif
      @if($para)<p class="text-white/60 max-w-[440px] mx-auto mb-7">{{ $para }}</p>@endif
      @if($btnText)
        <a href="{{ $btnLink }}" class="shine animate-ring inline-block bg-accent text-white font-bold rounded-2xl shadow-cta hover:bg-accentDark transition-all" style="padding:16px 28px">{{ $btnText }}</a>
      @endif
    </div>
  </div>
</section>
