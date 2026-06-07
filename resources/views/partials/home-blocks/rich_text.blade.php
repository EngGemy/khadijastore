{{-- Block: rich_text — free HTML content --}}
@php
  $data = $block->data ?? [];
  $html = $data['html'] ?? '';
@endphp
@if($html)
<section class="max-w-[1180px] mx-auto px-5 py-12">
  @if($block->title)
    <h2 class="font-extrabold tracking-tight mb-6" style="font-size:clamp(22px,3vw,32px)">{{ $block->title }}</h2>
  @endif
  <div class="prose prose-lg max-w-none text-ink/80 leading-relaxed">
    {!! $html !!}
  </div>
</section>
@endif
