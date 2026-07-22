@extends('layouts.app')
@section('title', 'البراندات · ' . ($storeName ?? 'متجر العلامات'))

@section('meta')
<meta name="description" content="تصفّح جميع البراندات في {{ $storeName ?? 'متجر العلامات' }}.">
<link rel="canonical" href="{{ route('brands.index') }}">
@endsection

@section('content')
@include('partials.strip')
@include('partials.header')

<section class="bg-paper2/70 border-b border-line">
  <div class="max-w-[1180px] mx-auto px-4 sm:px-5 py-10 sm:py-12">
    <span class="inline-flex items-center gap-2 text-[11px] font-black tracking-[.16em] uppercase text-brand mb-2.5">
      <span class="w-1.5 h-1.5 rounded-full bg-brand"></span>البراندات
    </span>
    <h1 class="font-extrabold tracking-tight text-ink" style="font-size:clamp(28px,4vw,40px)">تصفّح البراندات</h1>
    <p class="text-muted mt-2 max-w-xl font-medium">{{ $brands->count() }} براند — اختر حرفاً أو تصفّح الكل.</p>
    <p class="mt-3 text-[12px] font-semibold text-ink/45 bg-white border border-line inline-flex rounded-full px-3 py-1.5">
      حجم شعار البراند الموصى به: <span class="en ms-1">512×512px</span> (PNG/WebP شفاف) · مصغّر <span class="en">200×200</span>
    </p>
  </div>
</section>

@include('partials.home-blocks.brands_alphabet', ['alphabetBrands' => $brands])

<section class="max-w-[1180px] mx-auto px-4 sm:px-5 pb-16">
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
    @foreach($brands as $b)
    <a href="{{ route('brand.show', $b->slug) }}" class="brand-store-card group" title="شعار موصى به: 512×512px">
      <div class="flex items-center gap-3.5">
        @include('partials.brand-avatar', ['brand' => $b, 'size' => 'lg', 'class' => 'group-hover:scale-105 transition-transform duration-500'])
        <div class="min-w-0">
          <h2 class="font-extrabold text-lg tracking-tight truncate">{{ $b->name }}</h2>
          <div class="text-xs text-ink/52 font-semibold truncate">{{ $b->category_label }}</div>
        </div>
      </div>
      @if($b->description)
      <p class="text-sm text-ink/52 leading-relaxed line-clamp-2">{{ $b->description }}</p>
      @endif
      <div class="brand-store-card__footer">
        <span class="text-[13px] text-ink/52 font-semibold">{{ $b->products_count ?? 0 }} منتج</span>
        <span class="brand-store-card__cta">زيارة المتجر <span>←</span></span>
      </div>
    </a>
    @endforeach
  </div>
</section>

@include('partials.footer')
@endsection

@push('scripts')
<script>
document.documentElement.classList.add('js');
document.querySelectorAll('.reveal').forEach(el => el.classList.add('in'));
(function () {
  const cards = Array.from(document.querySelectorAll('#alphabet-brands .alphabet-brand'));
  const empty = document.getElementById('alphabet-empty');
  const grid = document.getElementById('alphabet-brands');
  function apply(letter) {
    let n = 0;
    cards.forEach(c => {
      const ok = !letter || c.dataset.letter === letter;
      c.style.display = ok ? '' : 'none';
      if (ok) n++;
    });
    if (empty) empty.classList.toggle('hidden', n > 0);
    if (grid) grid.classList.toggle('hidden', n === 0);
  }
  document.querySelectorAll('[data-letter-bar] .letter-chip, [data-letter-bar-latin] .letter-chip').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.letter-chip').forEach(b => {
        b.classList.remove('is-active');
        b.setAttribute('aria-pressed', 'false');
      });
      btn.classList.add('is-active');
      btn.setAttribute('aria-pressed', 'true');
      apply(btn.dataset.letter || '');
    });
  });
})();
</script>
@endpush
