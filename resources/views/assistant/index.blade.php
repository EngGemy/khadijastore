@extends('layouts.app')

@section('title', 'المساعد الذكي · ' . ($storeName ?? 'متجر العلامات'))

@section('meta')
<meta name="description" content="اسأل مساعدنا الذكي عن أي منتج وسيرشّدك للأنسب.">
@endsection

@section('content')
<div class="js min-h-screen bg-paper flex flex-col">

  {{-- ─── شريط النصوص العلوي ──────────────────────────────────── --}}
  <div class="bg-ink text-paper text-[11px] text-center py-2 tracking-widest font-semibold overflow-hidden">
    <span class="inline-block animate-marquee whitespace-nowrap">
      {{ $stripText ?? 'شحن مجاني داخل القاهرة والجيزة · الدفع عند الاستلام' }}
      &nbsp;&nbsp;·&nbsp;&nbsp;
      {{ $stripText ?? 'شحن مجاني داخل القاهرة والجيزة · الدفع عند الاستلام' }}
    </span>
  </div>

  {{-- ─── هيدر ────────────────────────────────────────────────── --}}
  @include('partials.header')

  {{-- ─── Hero مختصر ──────────────────────────────────────────── --}}
  <section class="bg-ink text-paper py-12 md:py-16 relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full animate-spinSlow"
           style="background:conic-gradient(from 0deg,transparent 70%,rgba(22,163,74,.07) 100%);filter:blur(40px)"></div>
    </div>
    <div class="max-w-[1180px] mx-auto px-5 text-center relative z-10">
      <p class="text-accent font-black tracking-[.22em] text-xs mb-3 animate-heroFade" style="animation-delay:.05s">
        SMART ASSISTANT · مساعد ذكي
      </p>
      <h1 class="hl-line font-extrabold leading-tight mb-4" style="font-size:clamp(32px,5vw,52px);letter-spacing:-.025em">
        <span>اسأل</span>&nbsp;<span style="color:#22c55e">المساعد الذكي</span>
      </h1>
      <p class="text-white/60 max-w-md mx-auto text-sm leading-relaxed blur-in">
        أخبرني ما تبحث عنه وسأرشّدك لأفضل المنتجات من متجرنا بأسعارها الحقيقية.
      </p>
    </div>
  </section>

  {{-- ─── منطقة الدردشة الرئيسية ──────────────────────────────── --}}
  <main class="flex-1 max-w-[860px] w-full mx-auto px-4 py-8 flex flex-col gap-6">

    {{-- نافذة الرسائل --}}
    <div id="chat-window"
         class="flex-1 min-h-[340px] max-h-[520px] overflow-y-auto flex flex-col gap-4 rounded-2xl border border-line bg-paper2 p-4 scroll-smooth"
         style="scrollbar-width:thin">

      {{-- رسالة الترحيب --}}
      <div class="msg-bot flex gap-3 items-end" id="welcome-msg">
        <div class="w-8 h-8 rounded-full bg-ink text-paper grid place-items-center text-xs font-bold flex-shrink-0">ذ</div>
        <div class="bg-paper border border-line rounded-2xl rounded-br-sm px-4 py-3 text-sm leading-relaxed max-w-[78%] shadow-soft animate-blurReveal">
          {{ $welcome ?? 'مرحباً! أنا مساعدك الذكي. أخبرني ماذا تبحث عنه.' }}
        </div>
      </div>

    </div>

    {{-- اقتراحات سريعة --}}
    <div id="quick-chips" class="flex flex-wrap gap-2 stagger">
      @foreach(['ما هو أرخص منتج؟','الأكثر مبيعاً','قارن بين منتجين','ما المتاح في المخزون؟'] as $chip)
      <button onclick="sendChip(this)"
              class="quick-chip text-[13px] border border-line rounded-full px-3.5 py-1.5 hover:bg-ink hover:text-paper hover:border-ink transition font-medium shine">
        {{ $chip }}
      </button>
      @endforeach
    </div>

    {{-- صندوق الإدخال --}}
    <div class="flex gap-2 items-end bg-paper border border-line rounded-2xl p-2 shadow-soft reveal">
      <textarea id="user-input"
                rows="1"
                maxlength="500"
                placeholder="اكتب سؤالك هنا…"
                class="flex-1 resize-none bg-transparent text-sm outline-none py-2 px-2 leading-relaxed"
                style="min-height:40px;max-height:120px"></textarea>
      <button id="send-btn"
              onclick="sendMessage()"
              class="w-11 h-11 rounded-xl bg-ink text-paper grid place-items-center hover:bg-ink2 transition flex-shrink-0 disabled:opacity-40 shine"
              title="إرسال">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
        </svg>
      </button>
    </div>

    {{-- بطاقات المنتجات الموصى بها --}}
    <div id="product-cards" class="hidden">
      <p class="text-xs text-ink/50 font-semibold tracking-widest mb-3">المنتجات المقترحة</p>
      <div id="product-grid" class="grid grid-cols-2 md:grid-cols-4 gap-3 stagger"></div>
    </div>

    {{-- أداة المقارنة --}}
    <div id="compare-section" class="hidden rounded-2xl border border-line overflow-hidden reveal">
      <div class="bg-ink text-paper px-5 py-3 flex items-center justify-between">
        <span class="text-sm font-bold">مقارنة المنتجات</span>
        <button onclick="clearCompare()" class="text-white/50 hover:text-white text-xs transition">مسح</button>
      </div>
      <div id="compare-table" class="overflow-x-auto"></div>
    </div>

  </main>

  {{-- ─── Footer مبسّط ──────────────────────────────────────────── --}}
  <footer class="border-t border-line text-center text-xs text-ink/40 py-6">
    © {{ date('Y') }} {{ $storeName ?? 'متجر العلامات' }} · المساعد الذكي مدعوم بـ Gemini · الأسعار والتوفر من قاعدة البيانات الحقيقية
  </footer>

</div>
@endsection

@push('scripts')
<script>
(function(){
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  const chatWin   = document.getElementById('chat-window');
  const userInput = document.getElementById('user-input');
  const sendBtn   = document.getElementById('send-btn');
  let history     = [];
  let comparing   = [];

  // ─── تلقائي: ارتفاع textarea ───────────────────────────────────
  userInput.addEventListener('input', function(){
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
  });
  userInput.addEventListener('keydown', function(e){
    if(e.key === 'Enter' && !e.shiftKey){ e.preventDefault(); sendMessage(); }
  });

  // ─── إرسال الرسالة ─────────────────────────────────────────────
  window.sendMessage = async function(){
    const msg = userInput.value.trim();
    if(!msg || sendBtn.disabled) return;

    appendMsg('user', msg);
    history.push({role:'user', content:msg});
    userInput.value = '';
    userInput.style.height = 'auto';
    sendBtn.disabled = true;

    // إخفاء chips بعد أول رسالة
    document.getElementById('quick-chips').style.display = 'none';

    // مؤشر «يكتب»
    const typingId = appendTyping();

    try {
      const res = await fetch('{{ route('assistant.chat') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ message: msg, history: history.slice(-6) }),
      });

      removeTyping(typingId);

      if(!res.ok && res.status === 429){
        const d = await res.json();
        appendMsg('assistant', d.reply || 'تجاوزت الحد المسموح. انتظر قليلاً.');
        return;
      }

      const data = await res.json();
      appendMsg('assistant', data.reply || 'لم أتلق ردًا. جرّب مرة أخرى.');
      history.push({role:'assistant', content: data.reply || ''});

      if(data.products && data.products.length > 0){
        renderProductCards(data.products);
      }

    } catch(e) {
      removeTyping(typingId);
      appendMsg('assistant', 'حدث خطأ في الاتصال. تحقق من الإنترنت وحاول مجددًا.');
    } finally {
      sendBtn.disabled = false;
    }
  };

  // ─── chip سريع ─────────────────────────────────────────────────
  window.sendChip = function(btn){
    userInput.value = btn.textContent.trim();
    sendMessage();
  };

  // ─── إضافة رسالة ───────────────────────────────────────────────
  function appendMsg(role, text){
    const isUser = role === 'user';
    const div    = document.createElement('div');
    div.className = 'flex gap-3 items-end ' + (isUser ? 'flex-row-reverse' : '');

    const avatar = isUser
      ? '<div class="w-8 h-8 rounded-full bg-paper2 border border-line grid place-items-center text-xs font-bold flex-shrink-0">أ</div>'
      : '<div class="w-8 h-8 rounded-full bg-ink text-paper grid place-items-center text-xs font-bold flex-shrink-0">ذ</div>';

    const bubble = document.createElement('div');
    bubble.className = 'rounded-2xl px-4 py-3 text-sm leading-relaxed max-w-[78%] shadow-soft '
      + (isUser ? 'bg-ink text-paper rounded-bl-sm' : 'bg-paper border border-line rounded-br-sm');
    bubble.style.cssText = 'animation:blurReveal .5s cubic-bezier(.16,1,.3,1) both';
    bubble.textContent = text;

    div.innerHTML = avatar;
    div.appendChild(bubble);
    chatWin.appendChild(div);
    scrollToBottom();
  }

  // ─── مؤشر الكتابة ──────────────────────────────────────────────
  function appendTyping(){
    const id  = 'typing-' + Date.now();
    const div = document.createElement('div');
    div.id    = id;
    div.className = 'flex gap-3 items-end';
    div.innerHTML = '<div class="w-8 h-8 rounded-full bg-ink text-paper grid place-items-center text-xs font-bold flex-shrink-0">ذ</div>'
      + '<div class="bg-paper border border-line rounded-2xl rounded-br-sm px-4 py-3 flex gap-1.5 items-center">'
      + '<span class="w-2 h-2 rounded-full bg-ink/40 animate-blink" style="animation-delay:0s"></span>'
      + '<span class="w-2 h-2 rounded-full bg-ink/40 animate-blink" style="animation-delay:.2s"></span>'
      + '<span class="w-2 h-2 rounded-full bg-ink/40 animate-blink" style="animation-delay:.4s"></span>'
      + '</div>';
    chatWin.appendChild(div);
    scrollToBottom();
    return id;
  }

  function removeTyping(id){
    const el = document.getElementById(id);
    if(el) el.remove();
  }

  // ─── بطاقات المنتجات ────────────────────────────────────────────
  function renderProductCards(products){
    const section = document.getElementById('product-cards');
    const grid    = document.getElementById('product-grid');
    grid.innerHTML = '';

    products.forEach(function(p, i){
      const card = document.createElement('a');
      card.href  = p.url;
      card.target= '_blank';
      card.className = 'card-shine block border border-line rounded-xl overflow-hidden hover:shadow-lg2 transition group';
      card.style.cssText = 'animation:blurReveal .6s cubic-bezier(.16,1,.3,1) ' + (i * 0.1) + 's both';
      card.innerHTML = '<div class="aspect-square bg-paper3 overflow-hidden">'
        + (p.thumb
          ? '<img src="'+p.thumb+'" alt="'+escHtml(p.name)+'" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">'
          : '<div class="w-full h-full grid place-items-center text-ink/20 text-3xl">🛍</div>'
        )
        + '</div>'
        + '<div class="p-3">'
        + '<p class="text-xs font-bold leading-snug mb-1 line-clamp-2">'+escHtml(p.name)+'</p>'
        + '<div class="flex items-center justify-between gap-2">'
        + '<span class="text-accent font-extrabold text-sm en">'+p.price+' ج</span>'
        + (p.stock === 'نفد' ? '<span class="text-[10px] text-red-500 font-semibold">نفد</span>' : '<span class="text-[10px] text-accent font-semibold">متاح</span>')
        + '</div>'
        + '<button onclick="toggleCompare(event,'+p.id+',\''+escHtml(p.name)+'\',\''+p.url+'\')" '
        + 'id="cmp-'+p.id+'" '
        + 'class="mt-2 w-full text-[11px] border border-line rounded-lg py-1 hover:bg-ink hover:text-paper hover:border-ink transition font-medium">+ قارن</button>'
        + '</div>';
      grid.appendChild(card);
    });

    section.classList.remove('hidden');
    // تشغيل stagger
    setTimeout(() => grid.classList.add('in'), 50);
  }

  // ─── المقارنة ───────────────────────────────────────────────────
  window.toggleCompare = function(e, id, name, url){
    e.preventDefault(); e.stopPropagation();
    const btn = document.getElementById('cmp-'+id);
    const idx = comparing.findIndex(c => c.id === id);
    if(idx === -1){
      if(comparing.length >= 4){ alert('حد أقصى 4 منتجات للمقارنة'); return; }
      comparing.push({id, name, url});
      if(btn){ btn.textContent = '✓ مقارنة'; btn.classList.add('bg-ink','text-paper','border-ink'); }
    } else {
      comparing.splice(idx,1);
      if(btn){ btn.textContent = '+ قارن'; btn.classList.remove('bg-ink','text-paper','border-ink'); }
    }
    if(comparing.length >= 2) fetchCompare();
    else {
      document.getElementById('compare-section').classList.add('hidden');
    }
  };

  async function fetchCompare(){
    const res  = await fetch('{{ route('assistant.compare') }}', {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':csrfToken, 'Accept':'application/json' },
      body: JSON.stringify({ ids: comparing.map(c => c.id) }),
    });
    if(!res.ok) return;
    const data = await res.json();
    renderCompareTable(data.products || []);
  }

  function renderCompareTable(products){
    if(!products.length) return;
    const section = document.getElementById('compare-section');
    const table   = document.getElementById('compare-table');

    let html = '<table class="w-full text-sm"><thead><tr class="bg-paper2">'
      + '<th class="text-xs text-ink/50 font-semibold py-2.5 px-4 text-start">الخاصية</th>';
    products.forEach(p => {
      html += '<th class="py-2.5 px-4 text-center font-bold text-xs">'
        + '<a href="'+p.url+'" class="hover:text-accent transition">'+escHtml(p.name)+'</a></th>';
    });
    html += '</tr></thead><tbody>';

    const rows = [
      ['السعر', p => '<span class="en font-extrabold text-accent">'+p.price+'</span> ج'],
      ['التقييم', p => p.rating ? '★ '+p.rating : '—'],
      ['التوفر', p => p.in_stock ? '<span class="text-accent font-semibold">متاح</span>' : '<span class="text-red-500">نفد</span>'],
      ['الصورة', p => p.thumb ? '<img src="'+p.thumb+'" class="w-16 h-16 object-cover rounded-lg mx-auto">' : '—'],
    ];

    rows.forEach(([label, fn], ri) => {
      html += '<tr class="'+(ri%2===0?'bg-paper':'bg-paper2')+' border-t border-line">'
        + '<td class="py-2.5 px-4 text-xs text-ink/60 font-semibold whitespace-nowrap">'+label+'</td>';
      products.forEach(p => { html += '<td class="py-2.5 px-4 text-center text-xs">'+fn(p)+'</td>'; });
      html += '</tr>';
    });

    html += '</tbody></table>';
    table.innerHTML = html;
    section.classList.remove('hidden');
  }

  window.clearCompare = function(){
    comparing = [];
    document.getElementById('compare-section').classList.add('hidden');
    document.querySelectorAll('[id^="cmp-"]').forEach(b => {
      b.textContent = '+ قارن';
      b.classList.remove('bg-ink','text-paper','border-ink');
    });
  };

  // ─── مساعد ─────────────────────────────────────────────────────
  function scrollToBottom(){
    chatWin.scrollTop = chatWin.scrollHeight;
  }

  function escHtml(s){
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  // ─── IntersectionObserver ───────────────────────────────────────
  const io = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if(e.isIntersecting){ e.target.classList.add('in','visible'); io.unobserve(e.target); }
    });
  }, {threshold:.12});
  document.querySelectorAll('.reveal,.reveal-scale,.stagger,.blur-in').forEach(el => io.observe(el));

})();
</script>
@endpush
