<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', ($storeName ?? 'متجر العلامات') . ' · BRANDS')</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<script>
tailwind.config={theme:{extend:{
  fontFamily:{sans:['Cairo','sans-serif']},
  colors:{ink:'#0a0a0a',ink2:'#1a1a1a',paper:'#ffffff',paper2:'#f6f6f4',paper3:'#ededea',accent:'#16a34a',accentDark:'#15803d'},
  borderColor:{line:'rgba(10,10,10,.10)'},
  boxShadow:{soft:'0 4px 24px rgba(0,0,0,.06)',lg2:'0 24px 60px -12px rgba(0,0,0,.18)',cta:'0 8px 22px -6px rgba(22,163,74,.5)'},
  keyframes:{
    floatUp:{'0%':{opacity:0,transform:'translateY(60px)'},'100%':{opacity:1,transform:'translateY(0)'}},
    floaty:{'0%,100%':{transform:'translateY(0)'},'50%':{transform:'translateY(-12px)'}},
    ring:{'0%':{boxShadow:'0 0 0 0 rgba(22,163,74,.5)'},'70%':{boxShadow:'0 0 0 16px rgba(22,163,74,0)'},'100%':{boxShadow:'0 0 0 0 rgba(22,163,74,0)'}},
    blink:{'0%,100%':{opacity:1},'50%':{opacity:.5}},
    marquee:{'100%':{transform:'translateX(-50%)'}},
    spinSlow:{'100%':{transform:'rotate(360deg)'}},
  },
  animation:{floaty:'floaty 6s ease-in-out infinite',ring:'ring 2.4s cubic-bezier(.65,.05,.36,1) infinite',blink:'blink 1.8s infinite',marquee:'marquee 32s linear infinite',spinSlow:'spinSlow 30s linear infinite'},
}}}
</script>
<style>
  html{scroll-behavior:smooth}
  body{font-family:'Cairo',sans-serif;letter-spacing:-.01em}
  .en{letter-spacing:0}
  ::selection{background:#0a0a0a;color:#fff}
  body::after{content:'';position:fixed;inset:0;pointer-events:none;z-index:9999;opacity:.018;mix-blend-mode:multiply;background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 240 240' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E")}
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
  @media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}.js .reveal,.js .reveal-scale,.js .stagger>*,.js .hl-line>span{opacity:1!important;transform:none!important}}
  {{-- حقن ألوان الثيم الفعّال (المناسبات) server-side دون كسر التصميم --}}
  @if(!empty($themeCss)){!! $themeCss !!}@endif
</style>
@stack('head')
</head>
<body class="bg-paper text-ink antialiased">

@yield('content')

@stack('scripts')
</body>
</html>
