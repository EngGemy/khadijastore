<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>@yield('title', ($storeName ?? 'متجر العلامات') . ' · BRANDS')</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
@yield('meta')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<script>
tailwind.config={theme:{extend:{
  fontFamily:{sans:['Cairo','sans-serif']},
  colors:{
    ink:'#0B1D36',ink2:'#152A45',ink3:'#1E3A5F',
    paper:'#ffffff',paper2:'#F5F7FA',paper3:'#E8ECF2',
    accent:'#E85D04',accentDark:'#C2410C',
    brand:'#F97316',brandSoft:'#FFF0E6',
    muted:'#6B7A90',wa:'#25D366'
  },
  borderColor:{line:'rgba(11,29,54,.10)'},
  boxShadow:{
    soft:'0 4px 24px rgba(11,29,54,.06)',
    lg2:'0 24px 60px -12px rgba(11,29,54,.18)',
    cta:'0 10px 28px -8px rgba(232,93,4,.45)',
    card:'0 8px 28px rgba(11,29,54,.08)'
  },
  keyframes:{
    floatUp:{'0%':{opacity:0,transform:'translateY(60px)'},'100%':{opacity:1,transform:'translateY(0)'}},
    floaty:{'0%,100%':{transform:'translateY(0)'},'50%':{transform:'translateY(-12px)'}},
    ring:{'0%':{boxShadow:'0 0 0 0 rgba(232,93,4,.45)'},'70%':{boxShadow:'0 0 0 16px rgba(232,93,4,0)'},'100%':{boxShadow:'0 0 0 0 rgba(232,93,4,0)'}},
    blink:{'0%,100%':{opacity:1},'50%':{opacity:.5}},
    marquee:{'100%':{transform:'translateX(-50%)'}},
    spinSlow:{'100%':{transform:'rotate(360deg)'}},
    heroImg:{'0%':{opacity:0,transform:'scale(.92) translateY(30px)'},'100%':{opacity:1,transform:'scale(1) translateY(0)'}},
    heroFade:{'0%':{opacity:0,transform:'translateY(24px)'},'100%':{opacity:1,transform:'translateY(0)'}},
    heroSlideL:{'0%':{opacity:0,transform:'translateX(-40px)'},'100%':{opacity:1,transform:'translateX(0)'}},
    heroSlideR:{'0%':{opacity:0,transform:'translateX(40px)'},'100%':{opacity:1,transform:'translateX(0)'}},
    cCard1:{'0%':{opacity:0,transform:'translateY(80px) rotateY(-35deg) rotateX(15deg) scale(.8)'},'100%':{opacity:1,transform:'translateY(0) rotateY(-12deg) rotateX(4deg) scale(1)'}},
    cCard2:{'0%':{opacity:0,transform:'translateY(60px) rotateY(30deg) rotateX(-10deg) scale(.85)'},'100%':{opacity:1,transform:'translateY(0) rotateY(8deg) rotateX(-3deg) scale(1)'}},
    cCard3:{'0%':{opacity:0,transform:'translateY(70px) rotateY(-20deg) rotateX(18deg) scale(.82)'},'100%':{opacity:1,transform:'translateY(0) rotateY(-5deg) rotateX(5deg) scale(1)'}},
    cFloat1:{'0%,100%':{transform:'translateY(0) rotateY(-12deg) rotateX(4deg)'},'50%':{transform:'translateY(-16px) rotateY(-10deg) rotateX(5deg)'}},
    cFloat2:{'0%,100%':{transform:'translateY(0) rotateY(8deg) rotateX(-3deg)'},'50%':{transform:'translateY(-12px) rotateY(10deg) rotateX(-4deg)'}},
    cFloat3:{'0%,100%':{transform:'translateY(0) rotateY(-5deg) rotateX(5deg)'},'50%':{transform:'translateY(-14px) rotateY(-3deg) rotateX(7deg)'}},
    blurReveal:{'0%':{opacity:0,filter:'blur(12px)',transform:'translateY(30px) scale(.97)'},'100%':{opacity:1,filter:'blur(0px)',transform:'translateY(0) scale(1)'}},
    lineDraw:{'0%':{width:'0%'},'100%':{width:'100%'}},
    imgZoom:{'0%':{transform:'scale(1.2)'},'100%':{transform:'scale(1)'}},
    glowPulse:{'0%,100%':{boxShadow:'0 0 0 0 rgba(11,29,54,0)'},'50%':{boxShadow:'0 0 40px -5px rgba(11,29,54,.18)'}},
    letterIn:{'0%':{opacity:0,transform:'translateY(10px) scale(.92)'},'100%':{opacity:1,transform:'translateY(0) scale(1)'}},
  },
  animation:{floaty:'floaty 6s ease-in-out infinite',ring:'ring 2.4s cubic-bezier(.65,.05,.36,1) infinite',blink:'blink 1.8s infinite',marquee:'marquee 32s linear infinite',spinSlow:'spinSlow 30s linear infinite',heroImg:'heroImg 1s cubic-bezier(.16,1,.3,1) both',heroFade:'heroFade .8s cubic-bezier(.16,1,.3,1) both',heroSlideL:'heroSlideL .9s cubic-bezier(.16,1,.3,1) both',heroSlideR:'heroSlideR .9s cubic-bezier(.16,1,.3,1) both',cCard1:'cCard1 1.3s cubic-bezier(.16,1,.3,1) both',cCard2:'cCard2 1.3s cubic-bezier(.16,1,.3,1) .18s both',cCard3:'cCard3 1.3s cubic-bezier(.16,1,.3,1) .36s both',cFloat1:'cFloat1 7s ease-in-out infinite',cFloat2:'cFloat2 8s ease-in-out infinite',cFloat3:'cFloat3 6.5s ease-in-out infinite',blurReveal:'blurReveal .9s cubic-bezier(.16,1,.3,1) both',lineDraw:'lineDraw .8s cubic-bezier(.16,1,.3,1) forwards',imgZoom:'imgZoom 8s ease-out both',glowPulse:'glowPulse 4s ease-in-out infinite',letterIn:'letterIn .45s cubic-bezier(.16,1,.3,1) both'},
}}}
</script>
<style>
  :root{
    --navy:#0B1D36;--navy-2:#152A45;--navy-3:#1E3A5F;--navy-deep:#061224;
    --orange:#E85D04;--orange-bright:#F97316;--orange-soft:#FFF0E6;
    --paper:#ffffff;--paper-2:#F5F7FA;--paper-3:#E8ECF2;
    --muted:#6B7A90;--line:rgba(11,29,54,.10);
    --ink:var(--navy);--accent:var(--orange);--accentDark:#C2410C;
    --brand:var(--orange-bright);--wa:#25D366;--success:#16a34a;
    --radius:16px;--radius-lg:20px;
    --shadow-soft:0 4px 24px rgba(11,29,54,.06);
    --shadow-card:0 8px 28px rgba(11,29,54,.08);
    --shadow-cta:0 10px 28px -8px rgba(232,93,4,.45);
  }
  html{scroll-behavior:smooth}
  body{font-family:'Cairo',sans-serif;letter-spacing:-.01em;color:var(--navy);background:var(--paper)}
  .en{letter-spacing:0}
  ::selection{background:var(--navy);color:#fff}
  body::after{content:'';position:fixed;inset:0;pointer-events:none;z-index:9999;opacity:.012;mix-blend-mode:multiply;background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 240 240' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E")}
  .js .reveal{opacity:0;transform:translateY(40px)}
  .js .reveal.in{opacity:1;transform:translateY(0);transition:opacity .9s cubic-bezier(.16,1,.3,1),transform .9s cubic-bezier(.16,1,.3,1)}
  .js .reveal-scale{opacity:0;transform:scale(.92)}
  .js .reveal-scale.in{opacity:1;transform:scale(1);transition:opacity .8s cubic-bezier(.16,1,.3,1),transform .8s cubic-bezier(.16,1,.3,1)}
  .js .stagger>*{opacity:0;transform:translateY(36px)}
  .js .stagger.in>*{opacity:1;transform:translateY(0);transition:opacity .8s cubic-bezier(.16,1,.3,1),transform .8s cubic-bezier(.16,1,.3,1)}
  .js .stagger.in>*:nth-child(1){transition-delay:.05s}.js .stagger.in>*:nth-child(2){transition-delay:.13s}.js .stagger.in>*:nth-child(3){transition-delay:.21s}.js .stagger.in>*:nth-child(4){transition-delay:.29s}.js .stagger.in>*:nth-child(5){transition-delay:.37s}.js .stagger.in>*:nth-child(6){transition-delay:.45s}.js .stagger.in>*:nth-child(7){transition-delay:.53s}.js .stagger.in>*:nth-child(8){transition-delay:.61s}
  .js .hl-line>span{display:inline-block;animation:floatUp .9s cubic-bezier(.16,1,.3,1) backwards}
  .js .hl-line:nth-child(1)>span{animation-delay:.1s}.js .hl-line:nth-child(2)>span{animation-delay:.22s}
  .shine{position:relative;overflow:hidden}
  .shine::before{content:'';position:absolute;inset:0;background:linear-gradient(120deg,transparent 35%,rgba(255,255,255,.22) 50%,transparent 65%);transform:translateX(-120%);transition:.7s}
  .shine:hover::before{transform:translateX(120%)}
  .marq-mask{mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent)}
  .hero-3d{perspective:1400px;transform-style:preserve-3d}
  .hero-3d .card-3d{transform-style:preserve-3d;will-change:transform}
  .hero-3d .card-3d::after{content:'';position:absolute;inset:0;border-radius:inherit;box-shadow:0 25px 50px -12px rgba(0,0,0,.25);opacity:0;transition:opacity .5s}
  .hero-3d .card-3d:hover::after{opacity:1}
  .hero-3d .card-3d:hover{transform:translateY(-8px) scale(1.02)!important}
  .hero-3d .card-3d .card-img{transition:transform .8s cubic-bezier(.16,1,.3,1)}
  .hero-3d .card-3d:hover .card-img{transform:scale(1.08)}
  .card-shine{position:relative;overflow:hidden}
  .card-shine::before{content:'';position:absolute;inset:0;background:linear-gradient(105deg,transparent 40%,rgba(255,255,255,.18) 50%,transparent 60%);transform:translateX(-100%);transition:none;z-index:20;pointer-events:none}
  .card-shine:hover::before{transform:translateX(100%);transition:transform .7s ease-in-out}
  .js .blur-in{opacity:0;filter:blur(10px);transform:translateY(24px)}
  .js .blur-in.visible{opacity:1;filter:blur(0);transform:translateY(0);transition:opacity .9s cubic-bezier(.16,1,.3,1),filter .9s cubic-bezier(.16,1,.3,1),transform .9s cubic-bezier(.16,1,.3,1)}
  .home-section{padding-top:clamp(48px,8vw,80px);padding-bottom:clamp(48px,8vw,80px)}
  .home-section+.home-section{padding-top:0}
  #store-brands-filter+.home-section{padding-top:clamp(32px,5vw,56px)}
  #brands,#products,#offers,#doctors,#nurseries,#cats,#store-brands-filter,#letters,#features{scroll-margin-top:96px}
  html,body{overflow-x:clip;max-width:100%}
  .btn-wa{background:var(--wa)!important;color:#fff!important}
  .btn-wa:hover{filter:brightness(.92)}
  .mob-menu{position:fixed;inset:0;z-index:60;visibility:hidden;pointer-events:none}
  .mob-menu.is-open{visibility:visible;pointer-events:auto}
  .mob-menu__bg{position:absolute;inset:0;background:rgba(11,29,54,.55);backdrop-filter:blur(4px);opacity:0;transition:opacity .3s}
  .mob-menu.is-open .mob-menu__bg{opacity:1}
  .mob-menu__panel{
    position:absolute;top:0;bottom:0;inset-inline-end:0;
    width:min(300px,86vw);background:#fff;display:flex;flex-direction:column;
    box-shadow:0 24px 60px -12px rgba(11,29,54,.18);
    transform:translateX(100%);transition:transform .38s cubic-bezier(.16,1,.3,1);
    padding-bottom:env(safe-area-inset-bottom,0px);
  }
  [dir="rtl"] .mob-menu__panel{transform:translateX(-100%)}
  .mob-menu.is-open .mob-menu__panel{transform:translateX(0)!important}
  #hdr{padding-top:env(safe-area-inset-top,0px)}
  .hdr-icon{
    width:40px;height:40px;border-radius:12px;display:grid;place-items:center;
    color:var(--navy);background:transparent;border:1px solid transparent;
    transition:background .2s ease,border-color .2s ease,transform .2s ease;
  }
  .hdr-icon:hover{background:var(--paper-2);border-color:var(--line)}
  .souqi-search{
    display:flex;align-items:center;gap:.65rem;width:100%;max-width:420px;
    background:var(--paper-2);border:1px solid var(--line);border-radius:999px;
    padding:.55rem .9rem .55rem 1rem;transition:border-color .2s ease,box-shadow .2s ease,background .2s ease;
  }
  .souqi-search:focus-within{background:#fff;border-color:rgba(11,29,54,.28);box-shadow:0 0 0 4px rgba(11,29,54,.06)}
  .souqi-search input{flex:1;min-width:0;background:transparent;outline:none;font-size:.875rem;font-weight:600;color:var(--navy)}
  .souqi-search input::placeholder{color:rgba(11,29,54,.38);font-weight:500}
  .hero-video-wrap{position:absolute;inset:0;overflow:hidden;background:var(--navy)}
  .hero-video-wrap video{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
  .hero-video-overlay{
    position:absolute;inset:0;
    background:
      linear-gradient(105deg,rgba(6,18,36,.94) 0%,rgba(11,29,54,.88) 38%,rgba(11,29,54,.55) 68%,rgba(11,29,54,.72) 100%),
      radial-gradient(ellipse at 78% 18%,rgba(249,115,22,.22),transparent 52%);
  }
  .letter-chip{
    width:44px;height:44px;border-radius:14px;display:grid;place-items:center;
    font-weight:800;font-size:.95rem;color:var(--navy);background:#fff;
    border:1px solid var(--line);cursor:pointer;user-select:none;
    transition:transform .22s cubic-bezier(.16,1,.3,1),background .2s,color .2s,border-color .2s,box-shadow .22s;
  }
  .letter-chip:hover{transform:translateY(-2px);border-color:rgba(11,29,54,.22);box-shadow:0 8px 20px rgba(11,29,54,.08)}
  .letter-chip.is-active,.letter-chip[aria-pressed="true"]{
    background:var(--navy);color:#fff;border-color:var(--navy);
    box-shadow:0 10px 24px rgba(11,29,54,.22);
  }
  .letter-chip.is-empty{opacity:.35;pointer-events:none}
  .cat-circle{
    width:78px;height:78px;border-radius:999px;display:grid;place-items:center;
    background:linear-gradient(145deg,#fff,var(--paper-2));border:1px solid var(--line);
    box-shadow:0 6px 18px rgba(11,29,54,.06);font-size:1.75rem;
    transition:transform .3s cubic-bezier(.16,1,.3,1),box-shadow .3s;
  }
  .group:hover .cat-circle{transform:translateY(-4px) scale(1.04);box-shadow:0 14px 28px rgba(11,29,54,.12)}
  .feature-pill{
    display:flex;align-items:center;gap:.85rem;padding:1.1rem 1.15rem;
    border-radius:var(--radius);background:#fff;border:1px solid var(--line);
    box-shadow:var(--shadow-soft);
    transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease;
  }
  .feature-pill:hover{transform:translateY(-3px);box-shadow:var(--shadow-card);border-color:rgba(232,93,4,.22)}
  .feature-pill__icon{
    width:46px;height:46px;border-radius:14px;display:grid;place-items:center;flex-shrink:0;
    background:var(--orange-soft);color:var(--orange);
  }
  .sec-eyebrow{display:inline-flex;align-items:center;gap:.5rem;font-size:11px;font-weight:900;letter-spacing:.16em;text-transform:uppercase;color:var(--orange)}
  .sec-eyebrow::before{content:'';width:6px;height:6px;border-radius:999px;background:var(--orange-bright);flex-shrink:0}
  @media(max-width:1023px){.hero-3d{transform:none!important}.hero-3d .card-3d{transform:none!important;animation:none!important}.hero-3d .card-3d:hover{transform:translateY(-4px)!important}}
  @media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}.js .reveal,.js .reveal-scale,.js .stagger>*,.js .hl-line>span,.js .blur-in{opacity:1!important;transform:none!important;filter:none!important}}
  @if(!empty($themeCss)){!! $themeCss !!}@endif
</style>
@include('partials.home-page-styles')
@stack('head')
@yield('page_styles')
</head>
<body class="bg-paper text-ink antialiased">

@yield('content')

@include('partials.ai-chat-widget')

@stack('scripts')
</body>
</html>
