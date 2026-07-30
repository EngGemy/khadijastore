{{-- Single share overflow sheet — used from hero, not beside section titles --}}
@props(['brand', 'seo' => []])

@php
  $shareUrl = $seo['url'] ?? brand_page_url($brand->slug);
  $shareText = $seo['share_text'] ?? "تسوّق من {$brand->name}: {$shareUrl}";
  $waShare = 'https://wa.me/?text=' . rawurlencode($shareText);
  $fbShare = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareUrl);
@endphp

<div class="brand-share"
     x-data="{
       open: false,
       url: @js($shareUrl),
       copied: false,
       async copyLink() {
         try {
           await navigator.clipboard.writeText(this.url);
           this.copied = true;
           setTimeout(() => this.copied = false, 1800);
         } catch (e) {
           prompt('انسخ الرابط:', this.url);
         }
       },
       async nativeShare() {
         if (navigator.share) {
           try {
             await navigator.share({ title: @js($brand->name), text: @js($shareText), url: this.url });
             this.open = false;
           } catch (e) {}
         } else {
           this.open = true;
         }
       }
     }"
     @keydown.escape.window="open = false">
  <button type="button"
          class="brand-share__trigger"
          @click="nativeShare()"
          aria-haspopup="dialog"
          :aria-expanded="open.toString()"
          title="مشاركة المتجر">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
      <path stroke-linecap="round" stroke-linejoin="round" d="M4 12v8a2 2 0 002 2h12a2 2 0 002-2v-8M16 6l-4-4-4 4M12 2v13"/>
    </svg>
    <span class="brand-share__trigger-label">مشاركة</span>
  </button>

  <div class="brand-share__sheet" x-show="open" x-cloak
       role="dialog" aria-modal="true" aria-label="مشاركة المتجر"
       @click.self="open = false">
    <div class="brand-share__panel" @click.stop
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full">
      <div class="brand-share__handle" aria-hidden="true"></div>
      <p class="brand-share__title">شارك {{ $brand->name }}</p>
      <div class="brand-share__actions">
        <button type="button" class="brand-share__action" @click="copyLink()">
          <span class="brand-share__icon brand-share__icon--copy">
            <svg x-show="!copied" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            <svg x-show="copied" x-cloak width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          </span>
          <span x-text="copied ? 'تم النسخ' : 'نسخ الرابط'"></span>
        </button>
        <a href="{{ $waShare }}" target="_blank" rel="noopener" class="brand-share__action" @click="open = false">
          <span class="brand-share__icon brand-share__icon--wa">
            <svg width="20" height="20" class="fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
          </span>
          <span>واتساب</span>
        </a>
        <a href="{{ $fbShare }}" target="_blank" rel="noopener" class="brand-share__action" @click="open = false">
          <span class="brand-share__icon brand-share__icon--fb">
            <svg width="20" height="20" class="fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
          </span>
          <span>فيسبوك</span>
        </a>
      </div>
      <button type="button" class="brand-share__cancel" @click="open = false">إغلاق</button>
    </div>
  </div>
</div>
