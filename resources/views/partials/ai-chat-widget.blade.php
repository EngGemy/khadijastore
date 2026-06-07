{{-- ═══ AI Chat Widget — يظهر على كل الصفحات ══════════════════════════════ --}}
@if(config('ai.enabled', true))
<style>
#ai-fab{position:fixed;bottom:24px;inset-inline-start:24px;z-index:9990;width:58px;height:58px;border-radius:50%;background:#0a0a0a;color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 24px rgba(0,0,0,.22);transition:transform .25s cubic-bezier(.16,1,.3,1),box-shadow .25s}
#ai-fab:hover{transform:scale(1.1);box-shadow:0 12px 32px rgba(0,0,0,.28)}
#ai-fab .fab-ring{position:absolute;inset:-6px;border-radius:50%;border:2px solid rgba(22,163,74,.45);animation:ring 2.4s cubic-bezier(.65,.05,.36,1) infinite}
#ai-fab .fab-badge{position:absolute;top:-4px;inset-inline-end:-4px;width:18px;height:18px;border-radius:50%;background:#16a34a;color:#fff;font-size:10px;font-weight:800;display:none;align-items:center;justify-content:center;border:2px solid #fff}
#ai-fab.has-msg .fab-badge{display:flex}

/* ── Panel ─────────────────────────────────────────────────────────── */
#ai-panel{
  position:fixed;bottom:0;inset-inline-start:0;z-index:9991;
  width:100%;max-width:420px;height:min(680px,92dvh);
  background:#fff;border-radius:24px 24px 0 0;
  box-shadow:0 -8px 40px rgba(0,0,0,.14);
  display:flex;flex-direction:column;
  transform:translateY(110%);transition:transform .4s cubic-bezier(.16,1,.3,1);
  overflow:hidden;font-family:'Cairo',sans-serif;
}
@media(min-width:640px){
  #ai-panel{bottom:92px;inset-inline-start:24px;border-radius:20px;height:min(680px,88dvh)}
}
#ai-panel.open{transform:translateY(0)}

/* ── Panel Header ───────────────────────────────────────────────────── */
#ai-header{
  background:#0a0a0a;color:#fff;padding:16px 18px;
  display:flex;align-items:center;gap:12px;flex-shrink:0;
}
#ai-header .ai-avatar{width:38px;height:38px;border-radius:50%;background:rgba(22,163,74,.18);border:1px solid rgba(22,163,74,.35);display:flex;align-items:center;justify-content:center;flex-shrink:0}
#ai-header .ai-info{flex:1}
#ai-header .ai-name{font-weight:800;font-size:14px;line-height:1.2}
#ai-header .ai-status{font-size:11px;color:rgba(255,255,255,.5);display:flex;align-items:center;gap:4px}
#ai-header .ai-dot{width:7px;height:7px;border-radius:50%;background:#22c55e;animation:blink 2s infinite}
#ai-close{background:rgba(255,255,255,.08);border:none;color:#fff;width:32px;height:32px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .2s}
#ai-close:hover{background:rgba(255,255,255,.18)}

/* ── Messages ───────────────────────────────────────────────────────── */
#ai-messages{flex:1;overflow-y:auto;padding:14px 14px 6px;display:flex;flex-direction:column;gap:10px;scroll-behavior:smooth;scrollbar-width:thin}

.ai-bubble{display:flex;gap:8px;align-items:flex-end}
.ai-bubble.user{flex-direction:row-reverse}
.ai-bubble .av{width:30px;height:30px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800}
.ai-bubble.bot .av{background:#0a0a0a;color:#fff}
.ai-bubble.user .av{background:#f0fdf4;color:#16a34a;border:1px solid #dcfce7}
.ai-bubble .bbl{
  max-width:82%;padding:10px 13px;border-radius:16px;font-size:13px;line-height:1.6;
  animation:blurReveal .4s cubic-bezier(.16,1,.3,1) both;
  white-space:pre-line;word-break:break-word;
}
.ai-bubble.bot .bbl{background:#f6f6f4;border:1px solid rgba(10,10,10,.08);border-radius:16px 16px 16px 4px}
.ai-bubble.user .bbl{background:#0a0a0a;color:#fff;border-radius:16px 16px 4px 16px}

/* ── Typing indicator ───────────────────────────────────────────────── */
.ai-typing .bbl{background:#f6f6f4;border:1px solid rgba(10,10,10,.08);padding:12px 14px;border-radius:16px 16px 16px 4px}
.ai-typing .dot{width:7px;height:7px;border-radius:50%;background:#0a0a0a;opacity:.35;display:inline-block;margin:0 2px;animation:blink 1.2s infinite}
.ai-typing .dot:nth-child(2){animation-delay:.2s}.ai-typing .dot:nth-child(3){animation-delay:.4s}

/* ── Product Cards ──────────────────────────────────────────────────── */
.ai-products{display:flex;gap:10px;overflow-x:auto;padding:4px 0 8px;scrollbar-width:none}
.ai-products::-webkit-scrollbar{display:none}
.ai-pcard{
  flex-shrink:0;width:130px;border:1px solid rgba(10,10,10,.1);border-radius:14px;overflow:hidden;
  cursor:pointer;transition:box-shadow .2s,transform .2s;background:#fff;
}
.ai-pcard:hover{box-shadow:0 6px 20px rgba(0,0,0,.1);transform:translateY(-2px)}
.ai-pcard img{width:100%;height:90px;object-fit:cover;display:block}
.ai-pcard .ai-pinfo{padding:8px 8px 10px}
.ai-pcard .ai-pname{font-size:12px;font-weight:700;line-height:1.3;margin-bottom:4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.ai-pcard .ai-pprice{font-size:11px;font-weight:800;color:#16a34a}
.ai-pcard .ai-pstock{font-size:10px;color:rgba(10,10,10,.4)}
.ai-pbtn{width:100%;margin-top:6px;padding:5px 0;background:#0a0a0a;color:#fff;border:none;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;font-family:'Cairo',sans-serif;transition:background .2s}
.ai-pbtn:hover{background:#16a34a}

/* ── Variants ───────────────────────────────────────────────────────── */
.ai-variants{display:flex;flex-wrap:wrap;gap:7px;padding:4px 0 6px}
.ai-vbtn{padding:6px 14px;border:1.5px solid rgba(10,10,10,.15);border-radius:20px;background:#fff;font-size:12px;font-weight:700;cursor:pointer;font-family:'Cairo',sans-serif;transition:all .2s}
.ai-vbtn:hover{border-color:#0a0a0a;background:#0a0a0a;color:#fff}

/* ── Governorate chips ──────────────────────────────────────────────── */
.ai-govs{display:flex;flex-wrap:wrap;gap:6px;padding:4px 0 6px}
.ai-govbtn{padding:5px 12px;border:1px solid rgba(10,10,10,.12);border-radius:20px;background:#fff;font-size:12px;cursor:pointer;font-family:'Cairo',sans-serif;transition:all .2s}
.ai-govbtn:hover{border-color:#16a34a;color:#16a34a}

/* ── Summary card ───────────────────────────────────────────────────── */
.ai-summary{background:#f6f6f4;border:1px solid rgba(10,10,10,.1);border-radius:14px;padding:12px 14px;font-size:12.5px}
.ai-summary .ai-srow{display:flex;justify-content:space-between;padding:3px 0;border-bottom:1px solid rgba(10,10,10,.06)}
.ai-summary .ai-srow:last-child{border-bottom:none}
.ai-summary .ai-slabel{color:rgba(10,10,10,.5);font-weight:600}
.ai-summary .ai-sval{font-weight:700;text-align:start}
.ai-summary .ai-stotal{color:#16a34a;font-size:14px;font-weight:800}

/* ── Success card ───────────────────────────────────────────────────── */
.ai-success{background:#f0fdf4;border:1.5px solid #86efac;border-radius:14px;padding:14px 16px;text-align:center}
.ai-success .ai-sno{font-size:22px;font-weight:900;color:#15803d;letter-spacing:-.03em}
.ai-success .ai-smsg{font-size:12px;color:#166534;margin-top:4px}
.ai-success .ai-swa{display:inline-flex;align-items:center;gap:6px;margin-top:10px;background:#16a34a;color:#fff;padding:7px 16px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;transition:background .2s}
.ai-success .ai-swa:hover{background:#15803d}

/* ── Quick replies ──────────────────────────────────────────────────── */
#ai-quick{display:flex;flex-wrap:wrap;gap:6px;padding:8px 14px 6px;flex-shrink:0;border-top:1px solid rgba(10,10,10,.06)}
.ai-qr{padding:6px 14px;border:1.5px solid rgba(10,10,10,.15);border-radius:20px;background:#fff;font-size:12px;font-weight:700;cursor:pointer;font-family:'Cairo',sans-serif;white-space:nowrap;transition:all .2s}
.ai-qr:hover{background:#0a0a0a;color:#fff;border-color:#0a0a0a}

/* ── Input ──────────────────────────────────────────────────────────── */
#ai-input-bar{padding:10px 12px 14px;flex-shrink:0;border-top:1px solid rgba(10,10,10,.08);display:flex;gap:8px;align-items:flex-end;background:#fff}
#ai-text{flex:1;resize:none;border:1.5px solid rgba(10,10,10,.12);border-radius:12px;padding:9px 12px;font-size:13px;font-family:'Cairo',sans-serif;outline:none;line-height:1.5;min-height:42px;max-height:100px;transition:border-color .2s}
#ai-text:focus{border-color:#0a0a0a}
#ai-send-btn{width:42px;height:42px;border-radius:12px;background:#0a0a0a;color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .2s,transform .15s}
#ai-send-btn:hover{background:#16a34a;transform:scale(1.05)}
#ai-send-btn:disabled{opacity:.4;cursor:not-allowed;transform:none}

@media(prefers-reduced-motion:reduce){#ai-fab .fab-ring,#ai-header .ai-dot{animation:none!important}}
</style>

{{-- ─── زر العائم ────────────────────────────────────────────────── --}}
<button id="ai-fab" aria-label="المساعد الذكي" onclick="aiWidget.toggle()">
  <span class="fab-ring"></span>
  <span class="fab-badge" id="ai-badge">1</span>
  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    <circle cx="9" cy="10" r="1" fill="currentColor" stroke="none"/>
    <circle cx="12" cy="10" r="1" fill="currentColor" stroke="none"/>
    <circle cx="15" cy="10" r="1" fill="currentColor" stroke="none"/>
  </svg>
</button>

{{-- ─── لوحة الدردشة ─────────────────────────────────────────────── --}}
<div id="ai-panel" role="dialog" aria-label="المساعد الذكي">

  {{-- Header --}}
  <div id="ai-header">
    <div class="ai-avatar">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1" fill="#22c55e" stroke="none"/>
      </svg>
    </div>
    <div class="ai-info">
      <div class="ai-name">المساعد الذكي</div>
      <div class="ai-status"><span class="ai-dot"></span> متصل الآن</div>
    </div>
    <button id="ai-close" aria-label="إغلاق" onclick="aiWidget.toggle()">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>

  {{-- Messages --}}
  <div id="ai-messages"></div>

  {{-- Quick Replies --}}
  <div id="ai-quick"></div>

  {{-- Input --}}
  <div id="ai-input-bar">
    <textarea id="ai-text" rows="1" placeholder="اكتب رسالتك…" maxlength="600"></textarea>
    <button id="ai-send-btn" onclick="aiWidget.send()" aria-label="إرسال">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2" fill="currentColor" stroke="none"/>
      </svg>
    </button>
  </div>

</div>

<script>
(function(){
  const CSRF   = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
  const WIDGET = '{{ route('assistant.widget') }}';
  const msgs   = document.getElementById('ai-messages');
  const quick  = document.getElementById('ai-quick');
  const input  = document.getElementById('ai-text');
  const btn    = document.getElementById('ai-send-btn');
  const fab    = document.getElementById('ai-fab');
  const panel  = document.getElementById('ai-panel');
  const badge  = document.getElementById('ai-badge');
  let   history= [];
  let   opened = false;

  // ─── Open/Close ────────────────────────────────────────────────────
  window.aiWidget = {
    toggle(){
      opened = !opened;
      panel.classList.toggle('open', opened);
      fab.classList.toggle('has-msg', false);
      if(opened && msgs.children.length === 0) aiWidget.welcome();
    },
    welcome(){
      addBot('مرحباً! أنا مساعدك الذكي. يمكنني مساعدتك في:', 'chat');
      setQuick(['🛒 اطلب منتج', '🔍 تصفح المنتجات', '❓ استفسار عام', '🔄 إلغاء الطلب']);
    },
    send(msg){
      const text = (msg ?? input.value).trim();
      if(!text || btn.disabled) return;
      addUser(text);
      history.push({role:'user', content:text});
      input.value = '';
      input.style.height = 'auto';
      btn.disabled = true;
      clearQuick();
      const tid = showTyping();
      fetch(WIDGET, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
        body: JSON.stringify({message: text, history: history.slice(-6)}),
      })
      .then(r => r.json())
      .then(data => {
        hideTyping(tid);
        handleResponse(data);
      })
      .catch(() => {
        hideTyping(tid);
        addBot('حدث خطأ في الاتصال. جرّب مرة أخرى.', 'chat');
      })
      .finally(()=>{ btn.disabled=false; });
    }
  };

  // ─── Response Handler ────────────────────────────────────────────────
  function handleResponse(data){
    const action = data.action ?? 'chat';
    const text   = data.text  ?? '';
    const d      = data.data  ?? {};
    const q      = data.quick ?? [];

    if(text) addBot(text, action);

    history.push({role:'assistant', content: text});

    switch(action){
      case 'products':
        if(d.products?.length) renderProducts(d.products);
        break;
      case 'variants':
        if(d.variants?.length) renderVariants(d.variants);
        break;
      case 'gov_select':
        if(d.govs?.length) renderGovs(d.govs);
        break;
      case 'summary':
        renderSummary(d);
        break;
      case 'order_done':
        renderSuccess(d);
        break;
      case 'chat':
        if(d.products?.length) renderProducts(d.products);
        break;
    }

    if(q.length) setQuick(q);
    scrollDown();
  }

  // ─── Renderers ───────────────────────────────────────────────────────
  function renderProducts(products){
    const wrap = document.createElement('div');
    wrap.className = 'ai-products';
    products.forEach(p => {
      const card = document.createElement('div');
      card.className = 'ai-pcard';
      card.innerHTML =
        (p.thumb ? `<img src="${esc(p.thumb)}" alt="${esc(p.name)}" loading="lazy">` : `<div style="height:90px;background:#f0f0ee;display:flex;align-items:center;justify-content:center;font-size:28px">🛍</div>`)
        + `<div class="ai-pinfo">
             <div class="ai-pname">${esc(p.name)}</div>
             <div class="ai-pprice">${p.price} ج.م</div>
             <div class="ai-pstock">${p.stock === 'نفد' ? '⚠ نفد المخزون' : '✓ متاح'}</div>
             <button class="ai-pbtn" onclick="aiWidget.send('__SELECT_PRODUCT__:${p.id}')">اطلب</button>
           </div>`;
      wrap.appendChild(card);
    });
    msgs.appendChild(wrap);
  }

  function renderVariants(variants){
    const wrap = document.createElement('div');
    wrap.className = 'ai-variants';
    variants.forEach(v => {
      const b = document.createElement('button');
      b.className = 'ai-vbtn';
      b.textContent = v.name + (v.price ? ` — ${v.price} ج` : '');
      b.onclick = () => aiWidget.send('__SELECT_VARIANT__:' + v.id);
      wrap.appendChild(b);
    });
    msgs.appendChild(wrap);
  }

  function renderGovs(govs){
    const wrap = document.createElement('div');
    wrap.className = 'ai-govs';
    govs.forEach(g => {
      const b = document.createElement('button');
      b.className = 'ai-govbtn';
      b.textContent = g;
      b.onclick = () => aiWidget.send('__SELECT_GOV__:' + g);
      wrap.appendChild(b);
    });
    msgs.appendChild(wrap);
  }

  function renderSummary(d){
    const el = document.createElement('div');
    el.className = 'ai-summary';
    el.innerHTML = `
      <div class="ai-srow"><span class="ai-slabel">المنتج</span><span class="ai-sval">${esc(d.product??'')}</span></div>
      <div class="ai-srow"><span class="ai-slabel">الاسم</span><span class="ai-sval">${esc(d.name??'')}</span></div>
      <div class="ai-srow"><span class="ai-slabel">الموبايل</span><span class="ai-sval en">${esc(d.phone??'')}</span></div>
      <div class="ai-srow"><span class="ai-slabel">المحافظة</span><span class="ai-sval">${esc(d.gov??'')}</span></div>
      <div class="ai-srow"><span class="ai-slabel">العنوان</span><span class="ai-sval">${esc(d.address??'')}</span></div>
      <div class="ai-srow"><span class="ai-slabel">سعر المنتج</span><span class="ai-sval">${d.price??0} ج.م</span></div>
      <div class="ai-srow"><span class="ai-slabel">الشحن</span><span class="ai-sval">${d.shipping} ج.م</span></div>
      <div class="ai-srow"><span class="ai-slabel">الإجمالي</span><span class="ai-sval ai-stotal">${d.total} ج.م</span></div>
    `;
    msgs.appendChild(el);
  }

  function renderSuccess(d){
    const el = document.createElement('div');
    el.className = 'ai-success';
    el.innerHTML = `
      <div style="font-size:28px">🎉</div>
      <div class="ai-sno">${esc(d.order_no??'')}</div>
      <div class="ai-smsg">تم استلام طلبك! سنتواصل معك للتأكيد.</div>
      ${d.whatsapp_url ? `<a href="${d.whatsapp_url}" target="_blank" class="ai-swa">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.112 1.525 5.842L.057 23.117a.75.75 0 00.925.925l5.275-1.468A11.942 11.942 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75a9.712 9.712 0 01-4.95-1.352l-.356-.21-3.686 1.026 1.027-3.686-.21-.356A9.712 9.712 0 012.25 12C2.25 6.615 6.615 2.25 12 2.25S21.75 6.615 21.75 12 17.385 21.75 12 21.75z"/></svg>
        تواصل عبر واتساب
      </a>` : ''}
    `;
    msgs.appendChild(el);
  }

  // ─── Bubbles ─────────────────────────────────────────────────────────
  function addBot(text){
    const d = document.createElement('div');
    d.className = 'ai-bubble bot';
    d.innerHTML = `<div class="av">ذ</div><div class="bbl">${esc(text).replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>')}</div>`;
    msgs.appendChild(d);
    scrollDown();
  }

  function addUser(text){
    const d = document.createElement('div');
    d.className = 'ai-bubble user';
    d.innerHTML = `<div class="av">أ</div><div class="bbl">${esc(text)}</div>`;
    msgs.appendChild(d);
    scrollDown();
  }

  function showTyping(){
    const id = 'typ-' + Date.now();
    const d  = document.createElement('div');
    d.id = id; d.className = 'ai-bubble bot ai-typing';
    d.innerHTML = `<div class="av">ذ</div><div class="bbl"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>`;
    msgs.appendChild(d);
    scrollDown();
    return id;
  }

  function hideTyping(id){
    document.getElementById(id)?.remove();
  }

  // ─── Quick replies ────────────────────────────────────────────────────
  function setQuick(list){
    clearQuick();
    list.forEach(label => {
      const b = document.createElement('button');
      b.className = 'ai-qr';
      b.textContent = label;
      b.onclick = () => {
        clearQuick();
        // Map labels to commands
        const map = {
          '🛒 اطلب منتج': '__START_ORDER__',
          '🔄 إلغاء الطلب': '__CANCEL__',
          'إلغاء': '__CANCEL__',
          'تأكيد الطلب': 'نعم',
        };
        aiWidget.send(map[label] ?? label);
      };
      quick.appendChild(b);
    });
  }

  function clearQuick(){
    quick.innerHTML = '';
  }

  // ─── Helpers ──────────────────────────────────────────────────────────
  function scrollDown(){
    setTimeout(() => { msgs.scrollTop = msgs.scrollHeight; }, 60);
  }

  function esc(s){
    return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  // ─── Textarea auto-height ─────────────────────────────────────────────
  input.addEventListener('input', function(){
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 100) + 'px';
  });
  input.addEventListener('keydown', function(e){
    if(e.key === 'Enter' && !e.shiftKey){ e.preventDefault(); aiWidget.send(); }
  });

  // ─── Show badge after 4 seconds if not opened ─────────────────────────
  setTimeout(() => {
    if(!opened) fab.classList.add('has-msg');
  }, 4000);

})();
</script>
@endif
