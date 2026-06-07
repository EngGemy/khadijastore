{{-- بطاقة إدراج — مشتركة بين SSR وJS re-render --}}
@php
  $cover = $listing->getFirstMediaUrl('cover', 'thumb');
  $sub   = $type === 'doctor'
      ? ($listing->data['specialty'] ?? $listing->serviceCategory?->name ?? '')
      : $listing->age_range_text;
@endphp
<a href="{{ route('directory.show', [$type, $listing->slug]) }}"
   class="group card-shine border border-line rounded-[20px] overflow-hidden bg-paper hover:-translate-y-1.5 hover:shadow-lg2 transition-all duration-500 flex flex-col reveal">

  {{-- صورة --}}
  <div class="relative aspect-[4/3] overflow-hidden bg-paper3">
    @if($listing->is_featured)
    <span class="absolute top-3 start-3 z-10 bg-ink text-paper text-[10px] font-bold rounded-full px-2 py-0.5">مميّز</span>
    @endif
    @if($cover)
    <img src="{{ $cover }}" alt="{{ $listing->name }}"
         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
         loading="lazy" width="400" height="300">
    @else
    <div class="w-full h-full flex items-center justify-center text-5xl font-extrabold text-ink/15">
      {{ mb_substr($listing->name, 0, 1) }}
    </div>
    @endif
  </div>

  {{-- محتوى --}}
  <div class="flex flex-col gap-2 p-5 flex-1">
    <div class="flex items-start justify-between gap-2">
      <h3 class="font-extrabold text-[17px] leading-tight tracking-tight">{{ $listing->name }}</h3>
      @if($listing->rating > 0)
      <span class="text-xs font-bold text-ink/60 shrink-0">{{ $listing->rating }}★</span>
      @endif
    </div>
    @if($sub)
    <p class="text-[13px] text-ink/55 font-semibold">{{ $sub }}</p>
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
    <p class="text-[12px] text-ink/55 font-medium">{{ $listing->fees_range_text }}</p>
    @endif

    {{-- أزرار تواصل --}}
    <div class="mt-auto pt-3 border-t border-line flex gap-2">
      @if($listing->whatsapp_url)
      <a href="{{ $listing->whatsapp_url }}" target="_blank"
         onclick="event.stopPropagation()"
         class="flex-1 text-center text-[12px] font-bold bg-accent text-white rounded-lg py-2 hover:bg-accentDark transition">
        واتساب
      </a>
      @endif
      @if($listing->phone)
      <a href="tel:{{ $listing->phone }}"
         onclick="event.stopPropagation()"
         class="flex-1 text-center text-[12px] font-bold border border-line rounded-lg py-2 hover:bg-paper2 transition">
        اتصال
      </a>
      @endif
    </div>
  </div>
</a>
