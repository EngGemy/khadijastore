@props([
  'currentSlug' => null,
  'saveProduct' => null,
  'title' => 'شاهدت مؤخرًا',
])

@php
  $savePayload = null;
  if ($saveProduct) {
    $savePayload = [
      'slug' => $saveProduct->slug,
      'name' => $saveProduct->name,
      'price' => $saveProduct->price,
      'image' => product_cover_url($saveProduct, true) ?: $saveProduct->getFirstMediaUrl('cover'),
      'mark' => $saveProduct->brand->mark ?? 'ع',
    ];
  }
@endphp

<section id="recently-viewed" class="recently-viewed hidden" hidden aria-label="منتجات شاهدتها مؤخرًا"
         @if($currentSlug) data-exclude-slug="{{ $currentSlug }}" @endif
         @if($savePayload) data-save="{{ e(json_encode($savePayload, JSON_UNESCAPED_UNICODE)) }}" @endif>
  <div class="recently-viewed__inner">
    <div class="recently-viewed__head">
      <h2 class="recently-viewed__title">{{ $title }}</h2>
    </div>
    <div class="recently-viewed__track" data-recent-track></div>
  </div>
</section>

@once
@push('head')
<style>
  .recently-viewed{padding:18px 0 8px}
  .recently-viewed__inner{max-width:1180px;margin:0 auto;padding:0 16px}
  @media(min-width:640px){.recently-viewed__inner{padding:0 20px}}
  .recently-viewed__head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
  .recently-viewed__title{font-size:17px;font-weight:900;color:#0B1D36;margin:0}
  .recently-viewed__track{
    display:flex;gap:10px;overflow-x:auto;overscroll-behavior-x:contain;
    scroll-snap-type:x proximity;-webkit-overflow-scrolling:touch;scrollbar-width:none;
    padding-bottom:4px;
  }
  .recently-viewed__track::-webkit-scrollbar{display:none}
  .recent-card{
    flex-shrink:0;scroll-snap-align:start;width:132px;text-decoration:none;color:inherit;
    background:#fff;border:1px solid rgba(11,29,54,.08);border-radius:16px;overflow:hidden;
    box-shadow:0 6px 18px rgba(11,29,54,.05);
  }
  .recent-card__media{aspect-ratio:1;background:#F5F7FA}
  .recent-card__media img{width:100%;height:100%;object-fit:cover}
  .recent-card__body{padding:8px 10px 10px}
  .recent-card__name{
    font-size:12px;font-weight:800;line-height:1.35;color:#0B1D36;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.6em;
  }
  .recent-card__price{font-size:13px;font-weight:900;color:#E85D04;margin-top:4px}
  @media(min-width:640px){.recent-card{width:148px}.recently-viewed__title{font-size:19px}}
</style>
@endpush
@endonce

@once
@push('scripts')
<script>
(function () {
  const KEY = 'alamat_recently_viewed';
  const MAX = 12;
  window.AlamatRecent = {
    read() {
      try { return JSON.parse(localStorage.getItem(KEY) || '[]'); } catch (_) { return []; }
    },
    write(items) {
      localStorage.setItem(KEY, JSON.stringify(items.slice(0, MAX)));
    },
    save(item) {
      if (!item || !item.slug) return;
      const items = this.read().filter(i => i.slug !== item.slug);
      items.unshift(item);
      this.write(items);
    },
    list(excludeSlug) {
      return this.read().filter(i => i.slug !== excludeSlug);
    }
  };

  function renderRecent() {
    const root = document.getElementById('recently-viewed');
    const track = root && root.querySelector('[data-recent-track]');
    if (!root || !track || !window.AlamatRecent) return;
    if (root.dataset.save) {
      try { window.AlamatRecent.save(JSON.parse(root.dataset.save)); } catch (_) {}
    }
    const exclude = root.dataset.excludeSlug || '';
    const items = window.AlamatRecent.list(exclude);
    if (!items.length) return;
    track.innerHTML = items.slice(0, 10).map(item => {
      const name = esc(item.name || '');
      const mark = esc(item.mark || 'ع');
      const href = safeProductUrl(item);
      const price = item.price ? Number(item.price).toLocaleString('ar-EG') + ' ج.م' : '';
      const img = item.image
        ? `<img src="${esc(item.image)}" alt="" loading="lazy">`
        : `<span style="display:grid;place-items:center;height:100%;font-weight:900;color:rgba(11,29,54,.18)">${mark}</span>`;
      return `<a class="recent-card" href="${href}">
        <div class="recent-card__media">${img}</div>
        <div class="recent-card__body">
          <p class="recent-card__name">${name}</p>
          ${price ? `<p class="recent-card__price">${price}</p>` : ''}
        </div>
      </a>`;
    }).join('');
    root.hidden = false;
    root.classList.remove('hidden');
  }

  function esc(value) {
    return String(value).replace(/[&<>"']/g, ch => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[ch]));
  }

  function safeProductUrl(item) {
    if (item.slug && /^[a-z0-9]+(?:-[a-z0-9]+)*$/i.test(item.slug)) {
      return '/product/' + item.slug;
    }
    return '/products';
  }

  document.querySelectorAll('#recently-viewed').forEach(el => {
    if (@json($currentSlug)) el.dataset.excludeSlug = @json($currentSlug);
  });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', renderRecent);
  } else {
    renderRecent();
  }
})();
</script>
@endpush
@endonce
