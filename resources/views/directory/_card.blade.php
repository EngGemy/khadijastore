{{-- بطاقة إدراج — دليل الأطباء / الحضانات --}}
@php
  $cover = $listing->getFirstMediaUrl('cover', 'thumb');
  $sub   = $type === 'doctor'
      ? ($listing->data['specialty'] ?? $listing->serviceCategory?->name ?? '')
      : $listing->age_range_text;
@endphp
<a href="{{ route('directory.show', [$type, $listing->slug]) }}"
   class="group dir-card flex flex-col reveal">

  <div class="relative aspect-[4/3] overflow-hidden bg-paper3">
    @if($listing->is_featured)
    <span class="absolute top-3 start-3 z-10 bg-ink text-paper text-[10px] font-bold rounded-full px-2.5 py-0.5 shadow-sm">مميّز</span>
    @endif
    @if($cover)
    <img src="{{ $cover }}" alt="{{ $listing->name }}"
         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
         loading="lazy" width="400" height="300">
    @else
    <div class="w-full h-full bg-gradient-to-br from-paper2 to-paper3 flex items-center justify-center text-5xl font-extrabold text-ink/15">
      {{ mb_substr($listing->name, 0, 1) }}
    </div>
    @endif
    <div class="absolute inset-0 pointer-events-none" style="background:linear-gradient(to top,rgba(11,29,54,.28),transparent 50%)"></div>
  </div>

  <div class="flex flex-col gap-2 p-5 flex-1">
    <div class="flex items-start justify-between gap-2">
      <h3 class="font-extrabold text-[16px] leading-tight tracking-tight text-ink group-hover:text-brand transition-colors">{{ $listing->name }}</h3>
      @if($listing->rating > 0)
      <span class="inline-flex items-center gap-0.5 text-[11px] font-bold text-brand bg-brandSoft rounded-full px-2 py-0.5 shrink-0">{{ $listing->rating }}★</span>
      @endif
    </div>
    @if($sub)
    <p class="text-[13px] text-brand font-bold">{{ $sub }}</p>
    @endif
    @if($listing->governorate)
    <p class="text-[12px] text-ink/40 flex items-center gap-1">
      <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
      {{ $listing->governorate }}
    </p>
    @endif
    @if($type === 'nursery' && $listing->fees_range_text)
    <p class="text-[12px] text-ink/50 font-medium">{{ $listing->fees_range_text }}</p>
    @endif

    <div class="mt-auto pt-3 border-t border-line flex gap-2">
      @if($listing->whatsapp_url)
      <a href="{{ $listing->whatsapp_url }}" target="_blank" rel="noopener"
         onclick="event.stopPropagation()"
         class="flex-1 text-center text-[12px] font-bold rounded-xl py-2.5 transition"
         style="background:#25D366;color:#fff">
        واتساب
      </a>
      @endif
      @if($listing->phone)
      <a href="tel:{{ $listing->phone }}"
         onclick="event.stopPropagation()"
         class="flex-1 text-center text-[12px] font-bold border border-line rounded-xl py-2.5 hover:bg-paper2 transition">
        اتصال
      </a>
      @endif
      <span class="flex-1 text-center text-[12px] font-bold border border-ink/15 text-ink rounded-xl py-2.5 group-hover:bg-ink group-hover:text-paper transition">
        التفاصيل
      </span>
    </div>
  </div>
</a>
