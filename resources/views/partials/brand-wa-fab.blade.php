@props(['brand'])

@if($brand->whatsapp)
<a href="https://wa.me/{{ $brand->whatsapp }}" target="_blank" rel="noopener" title="تواصل عبر واتساب" class="brand-wa-fab" aria-label="واتساب">
  <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
</a>
@endif
