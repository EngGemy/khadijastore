{{-- Alphabet browser for brands (حروف) — recommended logo: 512×512 PNG/WebP, thumb 200×200 --}}
@php
  $brands = $alphabetBrands ?? collect();
  $arabicLetters = ['أ','ب','ت','ث','ج','ح','خ','د','ذ','ر','ز','س','ش','ص','ض','ط','ظ','ع','غ','ف','ق','ك','ل','م','ن','ه','و','ي'];
  $latinLetters = range('A', 'Z');

  $normalize = function (?string $name): string {
    $name = trim((string) $name);
    if ($name === '') return '';
    $ch = mb_substr($name, 0, 1);
    $map = ['ا' => 'أ', 'إ' => 'أ', 'آ' => 'أ', 'ة' => 'ه', 'ى' => 'ي'];
    if (isset($map[$ch])) return $map[$ch];
    if (preg_match('/[A-Za-z]/u', $ch)) return mb_strtoupper($ch);
    return $ch;
  };

  $present = [];
  foreach ($brands as $b) {
    $letter = $normalize($b->name);
    if ($letter !== '') {
      $present[$letter] = true;
    }
  }
@endphp
@if($brands->isNotEmpty())
<section id="letters" class="home-section max-w-[1180px] mx-auto px-4 sm:px-5">
  <div class="reveal mb-7 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
      <span class="inline-flex items-center gap-2 text-[11px] font-black tracking-[.16em] uppercase text-brand mb-2.5">
        <span class="w-1.5 h-1.5 rounded-full bg-brand"></span>تصفّح بالحروف
      </span>
      <h2 class="font-extrabold tracking-tight text-ink" style="font-size:clamp(22px,3.2vw,32px)">ابحث عن البراند بحرف</h2>
      <p class="text-muted text-[14px] mt-2 max-w-md font-medium">اختر حرفاً لتصفية البراندات بسرعة — تجربة سلسة بالعربية والإنجليزية.</p>
    </div>
    <a href="{{ route('brands.index') }}" class="text-sm font-bold text-ink hover:text-brand transition inline-flex items-center gap-1.5">كل البراندات <span>←</span></a>
  </div>

  <div class="rounded-[20px] border border-line bg-white shadow-soft p-4 sm:p-5 reveal">
    <div class="flex flex-wrap gap-2" data-letter-bar role="tablist" aria-label="حروف البراندات">
      <button type="button" class="letter-chip is-active" data-letter="" aria-pressed="true" title="الكل">الكل</button>
      @foreach($arabicLetters as $i => $letter)
      <button type="button"
              class="letter-chip {{ empty($present[$letter]) ? 'is-empty' : '' }}"
              data-letter="{{ $letter }}"
              aria-pressed="false"
              style="animation-delay:{{ min($i * 12, 240) }}ms"
              @if(empty($present[$letter])) disabled @endif>{{ $letter }}</button>
      @endforeach
    </div>

    @php $hasLatin = collect($latinLetters)->contains(fn ($l) => !empty($present[$l])); @endphp
    @if($hasLatin)
    <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-line" data-letter-bar-latin>
      @foreach($latinLetters as $letter)
        @if(!empty($present[$letter]))
        <button type="button" class="letter-chip en" data-letter="{{ $letter }}" aria-pressed="false">{{ $letter }}</button>
        @endif
      @endforeach
    </div>
    @endif

    <div id="alphabet-brands" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 mt-5">
      @foreach($brands as $b)
      @php $letter = $normalize($b->name); @endphp
      <a href="{{ route('brand.show', $b->slug) }}"
         class="alphabet-brand group flex items-center gap-3 rounded-[16px] border border-line bg-paper2/50 hover:bg-white hover:shadow-card hover:-translate-y-0.5 transition-all duration-300 p-3"
         data-letter="{{ $letter }}"
         title="شعار موصى به: 512×512px (PNG/WebP شفاف) · عرض مصغّر 200×200">
        @include('partials.brand-avatar', ['brand' => $b, 'size' => 'md'])
        <div class="min-w-0">
          <p class="font-extrabold text-[14px] text-ink truncate">{{ $b->name }}</p>
          <p class="text-[11px] font-semibold text-muted truncate">{{ $b->category_label ?: (($b->products_count ?? 0).' منتج') }}</p>
        </div>
      </a>
      @endforeach
    </div>
    <p id="alphabet-empty" class="hidden text-center text-muted text-sm font-semibold py-8">لا توجد براندات لهذا الحرف</p>
  </div>
</section>
@endif
