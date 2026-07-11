@props(['brand', 'seo' => []])

@php
  $shareUrl = $seo['url'] ?? brand_page_url($brand->slug);
  $shareText = $seo['share_text'] ?? "تسوّق من {$brand->name}: {$shareUrl}";
  $waShare = 'https://wa.me/?text=' . rawurlencode($shareText);
  $fbShare = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareUrl);
@endphp

<div class="max-w-[1180px] mx-auto px-4 sm:px-5 py-3 reveal"
     x-data="{
       url: @js($shareUrl),
       copied: false,
       async copyLink() {
         try {
           await navigator.clipboard.writeText(this.url);
           this.copied = true;
           setTimeout(() => this.copied = false, 2200);
         } catch (e) {
           prompt('انسخ الرابط:', this.url);
         }
       }
     }">
  <div class="relative overflow-hidden rounded-2xl border border-line bg-gradient-to-br from-paper via-paper2 to-paper3 p-4 sm:p-5 shadow-soft">
    <div class="absolute -top-10 -end-10 w-40 h-40 rounded-full bg-accent/10 blur-2xl pointer-events-none"></div>
    <div class="relative flex flex-col sm:flex-row sm:items-center gap-4">
      <div class="flex items-center gap-3 min-w-0 flex-1">
        <span class="w-11 h-11 rounded-2xl bg-ink text-white grid place-items-center shrink-0 shadow-lg">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
        </span>
        <div class="min-w-0">
          <p class="text-[11px] font-bold text-accentDark uppercase tracking-wide">شارك متجرك</p>
          <p class="text-sm font-extrabold truncate mt-0.5" dir="ltr">{{ $shareUrl }}</p>
        </div>
      </div>
      <div class="flex flex-wrap gap-2 shrink-0">
        <button type="button" @click="copyLink()"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-bold bg-ink text-white hover:bg-ink2 transition-all hover:-translate-y-0.5">
          <span x-show="!copied">نسخ الرابط</span>
          <span x-show="copied" x-cloak class="text-emerald-300">تم النسخ ✓</span>
        </button>
        <a href="{{ $waShare }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-bold bg-accent text-white hover:bg-accentDark transition-all hover:-translate-y-0.5">واتساب</a>
        <a href="{{ $fbShare }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-bold border border-line bg-paper hover:bg-paper2 transition-all">فيسبوك</a>
      </div>
    </div>
  </div>
</div>
