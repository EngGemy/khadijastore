@props(['brand', 'seo' => []])

@php
  $shareUrl = $seo['url'] ?? brand_page_url($brand->slug);
  $shareText = $seo['share_text'] ?? "تسوّق من {$brand->name}: {$shareUrl}";
  $waShare = 'https://wa.me/?text=' . rawurlencode($shareText);
  $fbShare = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareUrl);
@endphp

<div class="brand-share-icons flex items-center gap-2 shrink-0"
     x-data="{
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
       }
     }">
  <button type="button" @click="copyLink()" title="نسخ الرابط"
          class="brand-icon-btn" :class="copied && 'is-success'">
    <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
    <svg x-show="copied" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
  </button>
  <a href="{{ $waShare }}" target="_blank" rel="noopener" title="مشاركة واتساب" class="brand-icon-btn brand-icon-btn--wa">
    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
  </a>
  <a href="{{ $fbShare }}" target="_blank" rel="noopener" title="مشاركة فيسبوك" class="brand-icon-btn brand-icon-btn--fb">
    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
  </a>
</div>
