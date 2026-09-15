@props([
  'compact' => false,
])

<div class="trust-strip {{ $compact ? 'trust-strip--compact' : '' }}" role="list" aria-label="ضمانات التسوّق">
  <div class="trust-strip__item" role="listitem">
    <span class="trust-strip__icon" aria-hidden="true">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
    </span>
    <span class="trust-strip__text">الدفع عند الاستلام</span>
  </div>
  <div class="trust-strip__item" role="listitem">
    <span class="trust-strip__icon" aria-hidden="true">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
    </span>
    <span class="trust-strip__text">شحن لكل المحافظات</span>
  </div>
  <div class="trust-strip__item" role="listitem">
    <span class="trust-strip__icon" aria-hidden="true">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
    </span>
    <span class="trust-strip__text">استرجاع سهل</span>
  </div>
</div>

@once
@push('head')
<style>
  .trust-strip{
    display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;
    max-width:1180px;margin:0 auto;padding:10px 16px 4px;
  }
  @media(min-width:640px){.trust-strip{gap:12px;padding:14px 20px 6px}}
  .trust-strip__item{
    display:flex;align-items:center;gap:8px;min-height:44px;
    padding:8px 10px;border-radius:14px;
    background:#fff;border:1px solid rgba(11,29,54,.08);
    box-shadow:0 4px 16px rgba(11,29,54,.04);
  }
  .trust-strip__icon{
    width:32px;height:32px;border-radius:10px;flex-shrink:0;
    display:grid;place-items:center;background:#FFF0E6;color:#E85D04;
  }
  .trust-strip__text{font-size:11px;font-weight:800;color:#0B1D36;line-height:1.3}
  .trust-strip--compact{padding-top:0;padding-bottom:0}
  @media(max-width:639px){
    .trust-strip{gap:6px;padding-inline:14px}
    .trust-strip__item{flex-direction:column;text-align:center;gap:4px;padding:8px 6px;min-height:64px}
    .trust-strip__text{font-size:10px}
  }
</style>
@endpush
@endonce
