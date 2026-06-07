{{-- Block: banner — dark CTA banner --}}
@php
  $data    = $block->data ?? [];
  $eyebrow = $data['eyebrow']   ?? setting('home.cta.eyebrow',   'جاهز تطلب؟ · READY?');
  $title   = $data['title']     ?? setting('home.cta.title',     'اطلب الآن وادفع عند الاستلام');
  $para    = $data['paragraph'] ?? setting('home.cta.paragraph', 'توصيل سريع لكل المحافظات.');
  $btnText = $data['btn_text']  ?? setting('home.cta.btn_text',  'ابدأ التسوّق');
  $btnLink = $data['btn_link']  ?? setting('home.cta.btn_link',  '#products');
@endphp
<section class="max-w-[1180px] mx-auto px-5 pb-18" style="padding-bottom:72px">
  <div class="reveal-scale relative overflow-hidden rounded-[28px] bg-ink text-white text-center" style="padding:56px 48px">
    <div class="absolute -top-3/5 -start-[10%] w-[500px] h-[500px] animate-spinSlow"
         style="background:radial-gradient(circle,rgba(22,163,74,.22),transparent 65%)"></div>
    <div class="relative z-10">
      <span class="text-xs font-bold tracking-[.14em] uppercase text-accent">{{ $eyebrow }}</span>
      <h2 class="font-extrabold tracking-tight mt-3 mb-3.5" style="font-size:clamp(24px,3.5vw,36px)">{{ $title }}</h2>
      <p class="text-white/60 max-w-[440px] mx-auto mb-7">{{ $para }}</p>
      @if($btnText)
        <a href="{{ $btnLink }}" class="shine animate-ring inline-block bg-accent text-white font-bold rounded-2xl shadow-cta hover:bg-accentDark transition-all" style="padding:16px 28px">{{ $btnText }}</a>
      @endif
    </div>
  </div>
</section>
