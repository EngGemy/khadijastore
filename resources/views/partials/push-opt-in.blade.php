{{-- Polite Web Push opt-in — shown after engagement, not on first paint --}}
@php
  $vapidPublic = config('webpush.vapid.public_key');
  $pushEnabled = config('webpush.enabled') && filled($vapidPublic);
  $brandIdForPush = isset($brand) && is_object($brand) ? $brand->id : null;
@endphp

@if($pushEnabled)
<style>
  .push-optin{
    position:fixed;inset-inline:12px;bottom:calc(12px + env(safe-area-inset-bottom,0px));z-index:70;
    max-width:420px;margin-inline:auto;left:12px;right:12px;
    background:rgba(255,255,255,.97);backdrop-filter:blur(16px);
    border:1px solid rgba(11,29,54,.1);border-radius:18px;
    box-shadow:0 16px 40px rgba(11,29,54,.16);
    padding:14px 14px 12px;display:none;
  }
  .push-optin.is-visible{display:block;animation:pushIn .4s cubic-bezier(.16,1,.3,1) both}
  @keyframes pushIn{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
  .push-optin__title{font-size:14px;font-weight:900;color:#0B1D36;margin:0 0 4px}
  .push-optin__body{font-size:12px;line-height:1.5;color:rgba(11,29,54,.55);margin:0 0 12px}
  .push-optin__actions{display:flex;gap:8px}
  .push-optin__btn{
    flex:1;min-height:44px;border-radius:14px;font-weight:800;font-size:13px;
    border:none;cursor:pointer;font-family:inherit;
  }
  .push-optin__btn--yes{background:linear-gradient(135deg,#F97316,#E85D04);color:#fff}
  .push-optin__btn--no{background:rgba(11,29,54,.06);color:rgba(11,29,54,.55)}
  @media(max-width:767px){
    body.app-shell:not(.app-shell--no-tabs) .push-optin{
      bottom:calc(var(--app-nav-h,64px) + 12px + env(safe-area-inset-bottom,0px));
    }
    body.app-shell--no-tabs:has(.brand-page) .push-optin,
    body.app-shell--no-tabs:has(.product-sticky-cta) .push-optin{
      bottom:calc(76px + env(safe-area-inset-bottom,0px));
    }
  }
</style>

<div id="push-optin" class="push-optin" role="dialog" aria-label="تفعيل الإشعارات" aria-hidden="true">
  <p class="push-optin__title">فعّل الإشعارات</p>
  <p class="push-optin__body">لتتابع طلبك وعروض سند مباشرة على جهازك — يمكنك الإيقاف في أي وقت من إعدادات المتصفح.</p>
  <div class="push-optin__actions">
    <button type="button" class="push-optin__btn push-optin__btn--yes" id="push-optin-yes">تفعيل</button>
    <button type="button" class="push-optin__btn push-optin__btn--no" id="push-optin-no">لاحقاً</button>
  </div>
</div>

@once
@push('scripts')
<script>
(function () {
  const VAPID_KEY = @json($vapidPublic);
  const BRAND_ID = @json($brandIdForPush);
  const STORAGE_KEY = 'alamat_push_pref';
  const ENGAGE_KEY = 'alamat_push_engage';
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

  if (!('serviceWorker' in navigator) || !('PushManager' in window) || !VAPID_KEY) return;

  const banner = document.getElementById('push-optin');
  const pref = localStorage.getItem(STORAGE_KEY);

  function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(base64);
    const out = new Uint8Array(raw.length);
    for (let i = 0; i < raw.length; ++i) out[i] = raw.charCodeAt(i);
    return out;
  }

  async function ensureSw() {
    return navigator.serviceWorker.register('/sw.js', { scope: '/' });
  }

  async function subscribe(phone) {
    const reg = await ensureSw();
    await navigator.serviceWorker.ready;
    let sub = await reg.pushManager.getSubscription();
    if (!sub) {
      sub = await reg.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(VAPID_KEY),
      });
    }
    const body = sub.toJSON();
    if (phone) body.customer_phone = phone;
    if (BRAND_ID) body.brand_id = BRAND_ID;
    body.contentEncoding = (PushManager.supportedContentEncodings && PushManager.supportedContentEncodings[0]) || 'aesgcm';

    const res = await fetch(@json(route('push.subscribe')), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF,
      },
      body: JSON.stringify(body),
      credentials: 'same-origin',
    });
    if (!res.ok) throw new Error('subscribe failed');
    localStorage.setItem(STORAGE_KEY, 'granted');
    hideBanner();
    return sub;
  }

  async function attachPhone(phone) {
    try {
      const reg = await navigator.serviceWorker.ready;
      const sub = await reg.pushManager.getSubscription();
      if (!sub || !phone) return;
      await fetch(@json(route('push.attach-phone')), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': CSRF,
        },
        body: JSON.stringify({ endpoint: sub.endpoint, customer_phone: phone }),
        credentials: 'same-origin',
      });
    } catch (_) {}
  }

  function hideBanner() {
    if (!banner) return;
    banner.classList.remove('is-visible');
    banner.setAttribute('aria-hidden', 'true');
  }

  function showBanner() {
    if (!banner || pref === 'granted' || pref === 'denied' || Notification.permission === 'denied') return;
    if (sessionStorage.getItem(STORAGE_KEY + '_later') || pref === 'later') return;
    if (Notification.permission === 'granted') {
      subscribe().catch(() => {});
      return;
    }
    banner.classList.add('is-visible');
    banner.setAttribute('aria-hidden', 'false');
  }

  function markEngage() {
    const n = parseInt(sessionStorage.getItem(ENGAGE_KEY) || '0', 10) + 1;
    sessionStorage.setItem(ENGAGE_KEY, String(n));
    if (n >= 2 && !pref) {
      setTimeout(showBanner, 1200);
    }
  }

  document.getElementById('push-optin-yes')?.addEventListener('click', async () => {
    try {
      const permission = await Notification.requestPermission();
      if (permission !== 'granted') {
        localStorage.setItem(STORAGE_KEY, 'denied');
        hideBanner();
        return;
      }
      await subscribe();
    } catch (e) {
      localStorage.setItem(STORAGE_KEY, 'denied');
      hideBanner();
    }
  });

  document.getElementById('push-optin-no')?.addEventListener('click', () => {
    localStorage.setItem(STORAGE_KEY, 'later');
    sessionStorage.setItem(STORAGE_KEY + '_later', '1');
    hideBanner();
  });

  // Engagement signals (not first paint)
  ['scroll', 'pointerdown', 'keydown'].forEach(evt => {
    window.addEventListener(evt, markEngage, { once: true, passive: true });
  });

  // After successful order — tie phone + nudge opt-in
  window.alamatPushAfterOrder = function (phone) {
    if (phone) {
      try { sessionStorage.setItem('alamat_last_phone', String(phone).replace(/\D/g, '')); } catch (_) {}
    }
    if (Notification.permission === 'granted') {
      subscribe(phone).then(() => attachPhone(phone)).catch(() => {});
      return;
    }
    if (sessionStorage.getItem(STORAGE_KEY + '_later')) return;
    setTimeout(showBanner, 800);
    const yes = document.getElementById('push-optin-yes');
    if (yes && phone) {
      const once = async () => {
        try {
          const permission = await Notification.requestPermission();
          if (permission === 'granted') await subscribe(phone);
        } catch (_) {}
        yes.removeEventListener('click', once);
      };
      yes.addEventListener('click', once);
    }
  };

  // Soft auto-show after 25s of presence if engaged
  setTimeout(() => {
    if (parseInt(sessionStorage.getItem(ENGAGE_KEY) || '0', 10) >= 1) showBanner();
  }, 25000);
})();
</script>
@endpush
@endonce
@endif
