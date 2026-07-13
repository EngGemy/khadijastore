@extends('layouts.app')
@section('title', ($seo['title'] ?? ($product->name . ' · ' . ($product->brand->name ?? 'متجر العلامات'))))

@section('meta')
@php $seoMeta = $seo ?? []; @endphp
@if(!empty($seoMeta['description']))<meta name="description" content="{{ $seoMeta['description'] }}">@endif
<link rel="canonical" href="{{ $seoMeta['url'] ?? url()->current() }}">
<meta property="og:type" content="product">
<meta property="og:title" content="{{ $seoMeta['title'] ?? $product->name }}">
<meta property="og:description" content="{{ $seoMeta['description'] ?? '' }}">
<meta property="og:url" content="{{ $seoMeta['url'] ?? url()->current() }}">
@if(!empty($seoMeta['image']))<meta property="og:image" content="{{ $seoMeta['image'] }}">@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoMeta['title'] ?? $product->name }}">

<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Product",
  "name": "{{ e($product->name) }}",
  "description": "{{ e($product->short_description ?? '') }}",
  "image": "{{ e($seoMeta['image'] ?? '') }}",
  "sku": "{{ e($product->slug) }}",
  "brand": {"@type":"Brand","name":"{{ e($seoMeta['brand'] ?? '') }}"},
  "offers": {
    "@type": "Offer",
    "price": "{{ $seoMeta['price'] ?? $product->price }}",
    "priceCurrency": "EGP",
    "availability": "https://schema.org/{{ $seoMeta['availability'] ?? 'InStock' }}",
    "url": "{{ $seoMeta['url'] ?? url()->current() }}"
  }
  @php $reviews = $product->approvedReviews ?? collect(); @endphp
  @if($reviews->count() > 0)
  ,"aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "{{ number_format($product->rating, 1) }}",
    "reviewCount": "{{ $reviews->count() }}"
  }
  @endif
}
</script>

<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BreadcrumbList",
  "itemListElement": [
    {"@type":"ListItem","position":1,"name":"الرئيسية","item":"{{ url('/') }}"},
    {"@type":"ListItem","position":2,"name":"{{ e($product->brand->name ?? '') }}","item":"{{ route('brand.show', $product->brand->slug ?? '#') }}"},
    {"@type":"ListItem","position":3,"name":"{{ e($product->name) }}","item":"{{ $seoMeta['url'] ?? url()->current() }}"}
  ]
}
</script>

<x-facebook-pixel
    :brand-id="$product->brand_id"
    :page-view-event-id="$fbPageView['event_id'] ?? null"
/>

@endsection

@section('content')
@push('head')
<style>
  .product-page .product-breadcrumb{font-size:12px;line-height:1.5;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
  @media(max-width:639px){
    .product-page #mainMedia{max-height:min(56vh,420px);border-radius:20px}
    .product-page .product-breadcrumb{white-space:normal;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}
  }
  .product-sticky-cta{
    padding-bottom:calc(12px + env(safe-area-inset-bottom,0px));
  }
  body:has(.product-page) #ai-fab{bottom:calc(78px + env(safe-area-inset-bottom,0px))}
  @media(min-width:1024px){body:has(.product-page) #ai-fab{bottom:24px}}
</style>
@endpush

@include('partials.strip')

@include('partials.header')

<div class="product-page max-w-[1180px] mx-auto px-4 sm:px-5">

  <!-- breadcrumb -->
  <div class="product-breadcrumb text-[13px] text-ink/52 py-4 sm:py-5 font-medium">
    <a href="{{ route('home') }}" class="hover:text-ink">الرئيسية</a> / <span id="crumbBrand">براند العناية</span> / <span id="crumbName" class="text-ink font-bold">قلم حواجب</span>
  </div>

  <!-- MAIN -->
  <div class="grid lg:grid-cols-[1.05fr_.95fr] gap-6 lg:gap-12 items-start pb-28 lg:pb-12">
    @php
      $mediaItems = [];
      if (!empty($productData['cover'])) $mediaItems[] = ['type' => 'image', 'url' => $productData['cover']];
      foreach ($productData['gallery'] ?? [] as $gUrl) $mediaItems[] = ['type' => 'image', 'url' => $gUrl];
      if (!empty($productData['video_url'])) $mediaItems[] = ['type' => 'video', 'url' => $productData['video_url']];
      $hasMedia = count($mediaItems) > 0;
    @endphp
    <div class="lg:sticky lg:top-[90px] reveal-scale">
      <div id="mainMedia" class="aspect-square max-h-[60vh] lg:max-h-none rounded-3xl bg-gradient-to-br from-paper2 to-paper3 grid place-items-center relative overflow-hidden border border-line">
        <div id="mainMediaInner" class="w-full h-full grid place-items-center">
          @if($hasMedia)
            <img src="{{ $mediaItems[0]['url'] }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
          @else
            <span class="mtxt font-extrabold text-3xl text-ink/10">{{ $product->brand->mark ?? 'ع' }}</span>
          @endif
        </div>
        <span class="absolute top-4 start-4 bg-accent text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-cta">عرض ٣×١</span>
        <span class="absolute top-4 end-4 w-10 h-10 rounded-full bg-white/85 backdrop-blur grid place-items-center hover:scale-110 transition cursor-pointer text-ink"><svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg></span>
      </div>
      @if($hasMedia)
      <div class="flex gap-3 mt-3.5 overflow-x-auto hide-scroll">
        @foreach($mediaItems as $idx => $item)
          @if($item['type'] === 'image')
            <button onclick="setMedia({{ $idx }},this)" data-thumb data-type="image" data-url="{{ $item['url'] }}" class="{{ $idx === 0 ? 'border-ink' : 'border-transparent' }} shrink-0 w-20 h-20 rounded-2xl border-2 overflow-hidden bg-paper grid place-items-center hover:-translate-y-0.5 transition">
              <img src="{{ $item['url'] }}" alt="" class="w-full h-full object-cover" loading="lazy">
            </button>
          @else
            <button onclick="setMedia({{ $idx }},this)" data-thumb data-type="video" data-url="{{ $item['url'] }}" class="border-transparent shrink-0 w-20 h-20 rounded-2xl border-2 bg-ink text-white grid place-items-center hover:-translate-y-0.5 transition relative">
              <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5.14v13.72a1 1 0 001.54.84l10.78-6.86a1 1 0 000-1.68L9.54 4.3A1 1 0 008 5.14z"/></svg>
            </button>
          @endif
        @endforeach
      </div>
      @endif
    </div>

    <!-- INFO + FORM -->
    <div class="reveal-l">
      @php
        $rating = $product->rating ?? 0;
        $reviewCount = $product->approvedReviews()->count();
        $salesCount = $product->sales_count ?? 0;
        $fullStars = min(5, max(0, round($rating)));
        $emptyStars = 5 - $fullStars;
        $discount = $product->discount_percent;
        $displayPrice = $product->variants->first()?->price ?? $product->price;
      @endphp
      <div class="flex items-center gap-3 mb-3 flex-wrap">
        <span class="text-accent tracking-widest text-[15px]">{{ str_repeat('★', $fullStars) }}{{ str_repeat('☆', $emptyStars) }}</span>
        <span class="text-[13.5px] text-ink/52 font-semibold">{{ number_format($rating, 1) }} · {{ $reviewCount }} تقييم</span>
        <span class="text-xs bg-accent/[.08] text-accentDark font-bold px-2.5 py-1 rounded-full">+{{ number_format($salesCount) }} مبيعات</span>
      </div>

      <h1 id="pName" class="font-extrabold text-3xl sm:text-[34px] leading-tight tracking-tight mb-2.5">{{ $product->name }}</h1>
      <p id="pDesc" class="text-ink/52 mb-5 text-[15.5px] leading-relaxed">{{ $product->short_description }}</p>

      <div class="flex items-baseline gap-3 mb-6 flex-wrap">
        <span id="priceNow" class="font-extrabold text-[40px] tracking-tight">{{ number_format($displayPrice) }}</span>
        <span class="text-lg font-bold">ج.م</span>
        @if($product->compare_price)
        <span id="priceOld" class="text-base text-ink/38 line-through">{{ number_format($product->compare_price) }} ج.م</span>
        @endif
        @if($discount)
        <span class="bg-ink text-white text-xs font-bold px-2.5 py-1.5 rounded-lg">وفّر {{ $discount }}%</span>
        @endif
      </div>

      @if($product->priceTiers->isNotEmpty())
      <div id="tierTable" class="mb-6">
        <div class="text-[13px] font-bold text-ink/70 mb-2">اشترِ أكثر، وفّر أكثر</div>
        <div class="border border-line rounded-2xl overflow-hidden">
          @foreach($product->priceTiers as $t)
          <div data-tier-min="{{ $t->min_qty }}" class="tier-row flex justify-between items-center px-4 py-3 text-[13.5px] border-b border-line last:border-b-0">
            <span>من {{ $t->min_qty }}+ قطعة → <span data-tier-price>{{ number_format($t->price) }}</span> ج.م/قطعة</span>
            <span class="font-bold">{{ $t->label ?? '' }}</span>
          </div>
          @endforeach
        </div>
      </div>
      @endif

      @php
        $variantAttributes = [];
        foreach ($product->variants as $v) {
            foreach ($v->option_values ?? [] as $ov) {
                $attrId = $ov['attribute_id'] ?? null;
                $valId = $ov['value_id'] ?? null;
                if (!$attrId || !$valId) continue;
                if (!isset($variantAttributes[$attrId])) {
                    $attr = \App\Models\Attribute::find($attrId);
                    if (!$attr) continue;
                    $variantAttributes[$attrId] = [
                        'id' => $attrId,
                        'name' => $attr->name,
                        'code' => $attr->code,
                        'input_type' => $attr->input_type,
                        'values' => [],
                    ];
                }
                if (!isset($variantAttributes[$attrId]['values'][$valId])) {
                    $val = \App\Models\AttributeValue::find($valId);
                    if (!$val) continue;
                    $variantAttributes[$attrId]['values'][$valId] = [
                        'id' => $valId,
                        'label' => $val->label,
                        'color_hex' => $val->color_hex,
                    ];
                }
            }
        }
      @endphp
      @php $hasAttributes = count($variantAttributes) > 0; @endphp
      @if($hasAttributes)
      <div id="attributeSelectors" class="space-y-4 mb-6">
        @foreach($variantAttributes as $attr)
        <div data-attr-id="{{ $attr['id'] }}">
          <div class="text-[13px] font-bold text-ink/70 mb-2">{{ $attr['name'] }}</div>
          <div class="flex flex-wrap gap-2.5">
            @foreach($attr['values'] as $val)
              @if($attr['input_type'] === 'color')
                <button
                  data-attr="{{ $attr['id'] }}"
                  data-val="{{ $val['id'] }}"
                  onclick="selectAttributeValue(this)"
                  class="attr-btn w-9 h-9 rounded-full border-[1.5px] border-line hover:scale-110 transition relative"
                  style="background-color: {{ $val['color_hex'] ?? '#ccc' }}"
                  title="{{ $val['label'] }}"
                ><span class="attr-ring absolute inset-0 rounded-full border-2 border-transparent pointer-events-none transition-colors"></span></button>
              @else
                <button
                  data-attr="{{ $attr['id'] }}"
                  data-val="{{ $val['id'] }}"
                  onclick="selectAttributeValue(this)"
                  class="attr-btn border-[1.5px] border-line rounded-xl px-4 py-2 text-[13px] font-bold hover:border-ink transition"
                >{{ $val['label'] }}</button>
              @endif
            @endforeach
          </div>
        </div>
        @endforeach
      </div>

      <!-- Selected Variant Result (attributes mode) -->
      <div id="variantResult" class="mb-6 border-[1.5px] border-ink rounded-2xl p-4 bg-paper flex items-center justify-between transition" style="display:none;">
        <div class="flex items-center gap-3">
          <span class="w-8 h-8 rounded-full bg-ink text-paper grid place-items-center shrink-0 text-sm">✓</span>
          <div>
            <div id="resultName" class="font-bold text-[15px]"></div>
            <div id="resultStock" class="text-[12px] font-bold text-accentDark"></div>
          </div>
        </div>
        <div id="resultPrice" class="font-extrabold text-[19px]"></div>
      </div>
      @endif

      <!-- VARIANTS (package picker — hidden when using color/size attributes) -->
      @if($product->variants->isNotEmpty() && ! $hasAttributes)
      <label class="block text-sm font-bold mb-3.5">اختر الباقة · <span class="en text-ink/45">SELECT PACKAGE</span></label>
      <div class="space-y-2.5 mb-6">
        @php
          $firstActiveId = $product->variants->first(fn($v) => !$v->isOutOfStock())?->id
              ?? $product->variants->first()?->id;
        @endphp
        @foreach($product->variants as $v)
        @php $isOOS = $v->isOutOfStock(); $isLow = $v->isLowStock(); @endphp
        <button
          {{ $isOOS ? 'disabled' : '' }}
          @if(!$isOOS) onclick="setVariant(this,{{ $v->price }},{{ $v->id }})" @endif
          data-variant
          data-variant-id="{{ $v->id }}"
          data-variant-options='@json($v->option_values ?? [])'
          class="{{ $v->id == $firstActiveId ? 'variant-active' : '' }} {{ $isOOS ? 'opacity-60 cursor-not-allowed' : 'hover:-translate-x-0.5 hover:border-ink/35' }} w-full text-start border-[1.5px] border-line rounded-2xl p-4 flex items-center justify-between transition relative bg-paper"
        >
          {{-- شارة نفد المخزون --}}
          @if($isOOS)
            <span class="vbadge absolute -top-2.5 end-4 bg-red-500 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">نفد المخزون</span>
          @elseif($v->is_popular)
            <span class="vbadge absolute -top-2.5 end-4 bg-ink text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">الأكثر طلبًا</span>
          @endif
          {{-- شارة مخزون منخفض --}}
          @if($isLow)
            <span class="vbadge absolute -top-2.5 start-4 bg-orange-500 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">متبقي {{ $v->stock }} فقط</span>
          @endif
          <span class="flex items-center gap-3">
            <span class="w-[21px] h-[21px] rounded-full border-2 border-current grid place-items-center shrink-0"><span class="radio-dot w-[11px] h-[11px] rounded-full bg-current scale-0 transition-transform"></span></span>
            <span><span class="font-bold text-[14.5px]">{{ $v->name }}</span><br><span class="vsub text-[12.5px] text-ink/52">{{ $v->subtitle }}</span></span>
          </span>
          <span class="font-extrabold text-[17px]">{{ number_format($v->price) }} ج.م</span>
        </button>
        @endforeach
      </div>
      @elseif($product->variants->isEmpty())
      {{-- مخزون المنتج بدون باقات --}}
      @if($product->isOutOfStock())
        <div class="mb-5 inline-flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm font-bold px-4 py-2.5 rounded-xl">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
          نفد المخزون حاليًا
        </div>
      @elseif($product->isLowStock())
        <div class="mb-5 inline-flex items-center gap-2 bg-orange-50 border border-orange-200 text-orange-700 text-sm font-bold px-4 py-2.5 rounded-xl">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
          متبقي {{ $product->stock }} قطعة فقط
        </div>
      @endif
      @endif

      <!-- ORDER FORM -->
      <div id="orderForm" class="border border-line rounded-3xl p-5 sm:p-6 bg-paper shadow-soft">
        <div class="flex items-center gap-2.5 mb-5">
          <span class="w-[30px] h-[30px] rounded-full bg-accent text-white grid place-items-center font-extrabold text-sm">١</span>
          <h2 class="font-extrabold text-lg">أكمل بياناتك للطلب</h2>
        </div>
        <div class="space-y-3">
          <input id="f_name" type="text" placeholder="الاسم بالكامل" class="w-full border-[1.5px] border-line rounded-xl px-4 py-3.5 text-[14.5px] bg-paper2 focus:bg-paper focus:border-ink transition">
          <input id="f_phone" type="tel" placeholder="رقم الموبايل (01XXXXXXXXX)" class="w-full border-[1.5px] border-line rounded-xl px-4 py-3.5 text-[14.5px] bg-paper2 focus:bg-paper focus:border-ink transition">
          <select id="f_gov" class="w-full border-[1.5px] border-line rounded-xl px-4 py-3.5 text-[14.5px] bg-paper2 focus:bg-paper focus:border-ink transition text-ink/70"><option value="">اختر المحافظة</option></select>
          <textarea id="f_address" rows="2" placeholder="العنوان بالتفصيل (الحي، الشارع، رقم العقار)" class="w-full border-[1.5px] border-line rounded-xl px-4 py-3.5 text-[14.5px] bg-paper2 focus:bg-paper focus:border-ink transition resize-none"></textarea>
          <div class="flex items-center justify-between bg-paper2 border-[1.5px] border-line rounded-xl px-4 py-3">
            <span class="text-sm font-bold">الكمية</span>
            <div class="flex items-center gap-4">
              <button onclick="changeQty(-1)" class="w-[34px] h-[34px] rounded-[10px] border-[1.5px] border-line bg-paper grid place-items-center text-lg font-bold hover:bg-ink hover:text-white hover:border-ink transition">−</button>
              <span id="qty" class="font-extrabold text-lg min-w-[26px] text-center">1</span>
              <button onclick="changeQty(1)" class="w-[34px] h-[34px] rounded-[10px] border-[1.5px] border-line bg-paper grid place-items-center text-lg font-bold hover:bg-ink hover:text-white hover:border-ink transition">+</button>
            </div>
          </div>
        </div>
        <!-- summary -->
        <div class="mt-4 pt-4 border-t border-dashed border-line text-sm">
          <div class="flex justify-between mb-2 text-ink/52 font-medium"><span>إجمالي المنتج</span><span id="sumProduct">١٤٩ ج.م</span></div>
          <div class="flex justify-between mb-2 text-ink/52 font-medium"><span>الشحن</span><span id="sumShip" class="text-accentDark font-bold">مجاني</span></div>
          <div class="flex justify-between font-extrabold text-[17px] pt-2"><span>الإجمالي</span><span id="sumTotal" class="text-[19px]">١٤٩ ج.م</span></div>
        </div>
        <!-- CTAs -->
        <div class="mt-5 space-y-2.5">
          @if($checkout['cod_enabled'])
          <button onclick="submitCOD()" class="shine animate-ring w-full bg-accent text-white font-bold py-4 rounded-xl text-base shadow-cta hover:bg-accentDark hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2.5">
            <svg class="w-[19px] h-[19px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.3a1 1 0 00.9 1.7h11.6M17 17a2 2 0 100 4 2 2 0 000-4zM9 19a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            اطلب الآن · الدفع عند الاستلام
          </button>
          @endif
          @if($checkout['whatsapp_enabled'] || $checkout['transfer_enabled'])
          <div class="grid grid-cols-2 gap-2.5">
            @if($checkout['whatsapp_enabled'])
            <button onclick="orderWhatsapp()" class="bg-ink text-white font-bold py-3.5 rounded-xl text-sm hover:bg-ink2 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
              <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>واتساب
            </button>
            @endif
            @if($checkout['transfer_enabled'])
            <button onclick="openTransfer()" class="border-[1.5px] border-ink text-ink font-bold py-3.5 rounded-xl text-sm hover:bg-ink hover:text-white hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 10h18M6 19h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>تحويل
            </button>
            @endif
          </div>
          @endif
          @if($checkout['terms_text'])
          <p class="text-[11.5px] text-ink/40 text-center pt-1">{{ $checkout['terms_text'] }}</p>
          @endif
        </div>
        <div class="flex justify-center gap-4 mt-4 text-[11.5px] text-ink/52 font-semibold flex-wrap">
          <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5 fill-accent" viewBox="0 0 20 20"><path d="M10 1l7 3v6c0 4-3 7-7 9-4-2-7-5-7-9V4z"/></svg>دفع آمن</span>
          <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5 fill-accent" viewBox="0 0 20 20"><path d="M10 1l7 3v6c0 4-3 7-7 9-4-2-7-5-7-9V4z"/></svg>استبدال 14 يوم</span>
          <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5 fill-accent" viewBox="0 0 20 20"><path d="M10 1l7 3v6c0 4-3 7-7 9-4-2-7-5-7-9V4z"/></svg>شحن سريع</span>
        </div>
      </div>
    </div>
  </div>

  @php
    $brandProducts = $product->brand->products;
    $brandRating = $brandProducts->avg('rating') ?? 0;
    $brandSales = $brandProducts->sum('sales_count') ?? 0;
    $shippingSpeed = setting('store.shipping_speed', '24 ساعة', $product->brand_id);
    $returnDays = setting('store.return_days', '14 يوم', $product->brand_id);
  @endphp
  <!-- TRUST BAR -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-3.5 my-8 md:my-6 stagger">
    <div class="border border-line rounded-[18px] p-5 text-center bg-paper hover:-translate-y-1 hover:shadow-soft transition"><div class="font-extrabold text-[25px] tracking-tight">+{{ number_format($brandSales) }}</div><div class="text-xs text-ink/52 mt-1 font-semibold">مبيعات</div></div>
    <div class="border border-line rounded-[18px] p-5 text-center bg-paper hover:-translate-y-1 hover:shadow-soft transition"><div class="font-extrabold text-[25px] tracking-tight">{{ number_format($brandRating, 1) }}★</div><div class="text-xs text-ink/52 mt-1 font-semibold">متوسط التقييم</div></div>
    <div class="border border-line rounded-[18px] p-5 text-center bg-paper hover:-translate-y-1 hover:shadow-soft transition"><div class="font-extrabold text-[25px] tracking-tight">{{ $shippingSpeed }}</div><div class="text-xs text-ink/52 mt-1 font-semibold">سرعة الشحن</div></div>
    <div class="border border-line rounded-[18px] p-5 text-center bg-paper hover:-translate-y-1 hover:shadow-soft transition"><div class="font-extrabold text-[25px] tracking-tight">{{ $returnDays }}</div><div class="text-xs text-ink/52 mt-1 font-semibold">ضمان الاستبدال</div></div>
  </div>

  @php
    $features = $product->features ?? [];
    $usageSteps = $product->usage_steps ?? [];
  @endphp
  <!-- DESC -->
  <section id="desc" class="grid md:grid-cols-2 gap-9 md:gap-12 mt-12 md:mt-14">
    <div class="reveal">
      <h2 class="font-extrabold text-xl mb-5 flex items-center gap-3"><span class="w-1.5 h-7 bg-accent rounded-full"></span>لماذا هذا المنتج؟</h2>
      <div class="space-y-4">
        @forelse($features as $f)
          @php
            $fTitle = is_array($f) ? ($f['title'] ?? $f[0] ?? '') : $f;
            $fDesc = is_array($f) ? ($f['description'] ?? $f[1] ?? '') : '';
          @endphp
          @if($fTitle)
          <div class="flex gap-3.5"><span class="shrink-0 rounded-full bg-ink text-white grid place-items-center" style="width:26px;height:26px"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg></span><div><div class="font-bold text-[14.5px]">{{ $fTitle }}</div>@if($fDesc)<div class="text-[13.5px] text-ink/52">{{ $fDesc }}</div>@endif</div></div>
          @endif
        @empty
          @if($product->short_description || $product->description)
            <div class="text-[13.5px] text-ink/52 leading-relaxed">{!! $product->short_description ?: $product->description !!}</div>
          @endif
        @endforelse
      </div>
    </div>
    <div class="reveal">
      <h2 class="font-extrabold text-xl mb-5 flex items-center gap-3"><span class="w-1.5 h-7 bg-accent rounded-full"></span>طريقة الاستخدام</h2>
      <div class="space-y-4">
        @forelse($usageSteps as $i => $step)
          @php $stepText = is_array($step) ? ($step['text'] ?? $step[0] ?? '') : $step; @endphp
          @if($stepText)
          <div class="flex gap-4"><span class="font-extrabold text-3xl text-ink/[.13] leading-none">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span><p class="text-[14.5px] text-ink/52 pt-1">{{ $stepText }}</p></div>
          @endif
        @empty
        @endforelse
      </div>
    </div>
  </section>

  <!-- REVIEWS -->
  <section id="reviews" class="mt-12 md:mt-16 mb-16 md:mb-20">
    <h2 class="reveal font-extrabold text-xl mb-6 flex items-center gap-3"><span class="w-1.5 h-7 bg-accent rounded-full"></span>آراء العملاء · <span class="en text-ink/45">REVIEWS</span></h2>
    <div class="grid md:grid-cols-3 gap-4 stagger">
      @php $approved = $product->approvedReviews; @endphp
      @if($approved->count())
        @foreach($approved as $r)
          <div class="border border-line rounded-[18px] bg-paper hover:-translate-y-1 hover:shadow-soft transition" style="padding:22px">
            <div class="text-accent tracking-widest mb-2.5">{{ str_repeat('★', $r->rating) }}{{ str_repeat('☆', 5 - $r->rating) }}</div>
            <p class="text-sm mb-3.5 leading-relaxed">«{{ $r->comment }}»</p>
            <div class="text-[12.5px] font-bold text-ink/52">{{ $r->customer_name }} · {{ $r->governorate }}</div>
          </div>
        @endforeach
      @else
        <div class="border border-line rounded-[18px] p-5.5 bg-paper hover:-translate-y-1 hover:shadow-soft transition" style="padding:22px"><div class="text-accent tracking-widest mb-2.5">★★★★★</div><p class="text-sm mb-3.5 leading-relaxed">«نتيجة طبيعية جدًا وثابت طول اليوم، أنصح بيه بشدة.»</p><div class="text-[12.5px] font-bold text-ink/52">منى س. · القاهرة</div></div>
        <div class="border border-line rounded-[18px] bg-paper hover:-translate-y-1 hover:shadow-soft transition" style="padding:22px"><div class="text-accent tracking-widest mb-2.5">★★★★★</div><p class="text-sm mb-3.5 leading-relaxed">«وصل بسرعة والدفع عند الاستلام سهّل عليّا كتير.»</p><div class="text-[12.5px] font-bold text-ink/52">داليا م. · الجيزة</div></div>
        <div class="border border-line rounded-[18px] bg-paper hover:-translate-y-1 hover:shadow-soft transition" style="padding:22px"><div class="text-accent tracking-widest mb-2.5">★★★★☆</div><p class="text-sm mb-3.5 leading-relaxed">«اللون حلو ومناسب، التغليف ممتاز.»</p><div class="text-[12.5px] font-bold text-ink/52">سارة ع. · الإسكندرية</div></div>
      @endif
    </div>
  </section>

  <!-- WRITE REVIEW -->
  <section class="mb-16 md:mb-20">
    <h2 class="reveal font-extrabold text-xl mb-6 flex items-center gap-3"><span class="w-1.5 h-7 bg-accent rounded-full"></span>اكتب مراجعة · <span class="en text-ink/45">WRITE A REVIEW</span></h2>
    <form action="{{ route('product.review', $product->slug) }}" method="POST" class="reveal border border-line rounded-3xl p-5 sm:p-6 bg-paper shadow-soft max-w-2xl">
      @csrf
      <div class="space-y-3">
        <input type="text" name="customer_name" placeholder="اسمك" required maxlength="120" class="w-full border-[1.5px] border-line rounded-xl px-4 py-3.5 text-[14.5px] bg-paper2 focus:bg-paper focus:border-ink transition">
        <div class="flex items-center gap-3">
          <label class="text-sm font-bold text-ink/70">التقييم:</label>
          <select name="rating" required class="border-[1.5px] border-line rounded-xl px-4 py-2.5 text-[14.5px] bg-paper2 focus:bg-paper focus:border-ink transition">
            <option value="5">★★★★★</option>
            <option value="4">★★★★☆</option>
            <option value="3">★★★☆☆</option>
            <option value="2">★★☆☆☆</option>
            <option value="1">★☆☆☆☆</option>
          </select>
        </div>
        <textarea name="comment" rows="3" placeholder="تعليقك (اختياري)" maxlength="1000" class="w-full border-[1.5px] border-line rounded-xl px-4 py-3.5 text-[14.5px] bg-paper2 focus:bg-paper focus:border-ink transition resize-none"></textarea>
        <input type="text" name="governorate" placeholder="المحافظة (اختياري)" maxlength="100" class="w-full border-[1.5px] border-line rounded-xl px-4 py-3.5 text-[14.5px] bg-paper2 focus:bg-paper focus:border-ink transition">
      </div>
      <button type="submit" class="mt-4 w-full bg-accent text-white font-bold py-3.5 rounded-xl shadow-cta hover:bg-accentDark hover:-translate-y-0.5 transition-all">إرسال المراجعة</button>
    </form>
  </section>
</div>

<!-- STICKY MOBILE CTA -->
<div class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-paper/92 backdrop-blur-2xl border-t border-line px-4 py-3 product-sticky-cta" style="box-shadow:0 -8px 30px rgba(0,0,0,.06)">
  <div class="flex items-center gap-3">
    <div><div class="text-[10px] text-ink/52 font-semibold">الإجمالي</div><div id="stickyPrice" class="font-extrabold text-lg">١٤٩ ج.م</div></div>
    <button onclick="document.getElementById('orderForm').scrollIntoView({behavior:'smooth'})" class="animate-ring flex-1 bg-accent text-white font-bold py-3.5 rounded-xl text-[15px]">اطلب الآن</button>
    <button onclick="orderWhatsapp()" class="shrink-0 w-[50px] h-[50px] bg-ink text-white rounded-xl grid place-items-center"><svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg></button>
  </div>
</div>

<!-- TRANSFER MODAL -->
<div id="transferModal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-ink/55 backdrop-blur-md">
  <div class="bg-paper rounded-[28px] max-w-[440px] w-full relative animate-scaleIn max-h-[92vh] overflow-y-auto" style="padding:26px">
    <button onclick="closeTransfer()" class="absolute top-[18px] start-[18px] w-9 h-9 rounded-full bg-paper2 grid place-items-center hover:bg-paper3 transition z-10"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
    <h3 class="font-extrabold text-xl mb-1">الدفع بالتحويل</h3>
    <p class="text-sm text-ink/52 mb-4">حوّل المبلغ ثم ارفع صورة الإيصال لتأكيد الطلب.</p>

    <!-- amount due -->
    <div class="flex items-center justify-between bg-ink text-white rounded-2xl px-4 py-3 mb-4">
      <span class="text-[13px] font-semibold text-white/70">المبلغ المطلوب تحويله</span>
      <span id="transferAmount" class="font-extrabold text-lg">١٤٩ ج.م</span>
    </div>

    <!-- Vodafone Cash -->
    <div class="border-[1.5px] border-line rounded-2xl p-4 flex items-center justify-between mb-2.5 hover:border-ink transition">
      <div class="flex items-center gap-3">
        <span class="w-[42px] h-[42px] rounded-xl grid place-items-center text-white shrink-0" style="background:#e60000"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm4.36 6.42c-.13 2.9-2.18 5.62-4.95 6.5-1.85.58-3.86.3-5.18-.85a4.9 4.9 0 01-1.7-3.43c-.05-1.9.95-3.78 2.6-4.74 1.04-.6 2.2-.78 3.32-.6-.5.9-.74 1.96-.62 3 .14 1.26.94 2.4 2.1 2.9 1.06.46 2.3.4 3.32-.1.4-.2.78-.47 1.1-.8.01.2.01.41.01.62z"/></svg></span>
        <div><div class="font-bold text-[14.5px]">فودافون كاش</div><div id="vfNum" class="text-[13px] text-ink/52 font-semibold ltr-num" style="direction:ltr">010 1234 5678</div></div>
      </div>
      <button onclick="copyNum('vfNum',this)" class="text-[12.5px] font-bold border-[1.5px] border-line px-3.5 py-2 rounded-[10px] hover:bg-ink hover:text-white hover:border-ink transition shrink-0">نسخ</button>
    </div>

    <!-- InstaPay -->
    <div class="border-[1.5px] border-line rounded-2xl p-4 flex items-center justify-between mb-2.5 hover:border-ink transition">
      <div class="flex items-center gap-3">
        <span class="w-[42px] h-[42px] rounded-xl grid place-items-center text-white shrink-0" style="background:linear-gradient(135deg,#7c3aed,#db2777)"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 9h16M4 9l2.5-5h11L20 9M4 9v9a2 2 0 002 2h12a2 2 0 002-2V9M9 14h6"/></svg></span>
        <div><div class="font-bold text-[14.5px]">إنستاباي</div><div id="ipNum" class="text-[13px] text-ink/52 font-semibold" style="direction:ltr">brand@instapay</div></div>
      </div>
      <button onclick="copyNum('ipNum',this)" class="text-[12.5px] font-bold border-[1.5px] border-line px-3.5 py-2 rounded-[10px] hover:bg-ink hover:text-white hover:border-ink transition shrink-0">نسخ</button>
    </div>

    <!-- upload + preview -->
    <label id="uploadBox" class="block border-2 border-dashed border-line rounded-2xl text-center cursor-pointer mb-4 hover:border-ink transition overflow-hidden" style="padding:22px">
      <div id="upPlaceholder">
        <svg class="mx-auto mb-2 stroke-ink/52" style="width:30px;height:30px" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
        <span class="text-sm font-bold block">ارفع صورة الإيصال</span>
        <span class="text-[12px] text-ink/45 block mt-0.5">PNG · JPG حتى 5MB</span>
      </div>
      <div id="upPreview" class="hidden relative">
        <img id="upImg" alt="إيصال التحويل" class="w-full max-h-44 object-contain rounded-xl">
        <span class="inline-flex items-center gap-1.5 text-[13px] font-bold text-accentDark mt-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>تم رفع الإيصال — اضغط للتغيير</span>
      </div>
      <input id="receiptInput" type="file" accept="image/*" class="hidden" onchange="handleReceipt(this)">
    </label>

    <button onclick="confirmTransfer()" class="w-full bg-accent text-white font-bold py-3.5 rounded-xl hover:bg-accentDark transition flex items-center justify-center gap-2">
      <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
      تأكيد الطلب وإرسال الإيصال
    </button>
  </div>
</div>

<!-- FOOTER -->
<footer class="bg-ink text-paper py-7 mt-0">
  <div class="max-w-[1180px] mx-auto px-5 text-center text-[13px] text-white/40">© 2026 متجر العلامات · جميع الحقوق محفوظة</div>
</footer>

<!-- TOAST -->
<div id="toast" class="fixed bottom-7 left-1/2 -translate-x-1/2 translate-y-5 z-[200] bg-ink text-paper px-6 py-3.5 rounded-2xl text-sm font-semibold opacity-0 pointer-events-none transition-all duration-300 shadow-lg2"></div>
@endsection

<style>
  .variant-active .radio-dot.scale-0 { transform: scale(1) !important; }
  .variant-active { border-color: #0a0a0a !important; }
  .attr-btn:disabled { pointer-events: none; }
</style>
@push('scripts')
<script>
document.documentElement.classList.add('js');
const ar = n => Number(n).toLocaleString('ar-EG');
const setText = (id, text) => { const el = document.getElementById(id); if (el) el.textContent = text; };
const setHref = (id, href) => { const el = document.getElementById(id); if (el) el.href = href; };

const P = @json($productData);
const GOVS = @json($govs);
const CHECKOUT = @json($checkout);

// ── Populate governorates (must run before any user interaction) ──
const govSel = document.getElementById('f_gov');
GOVS.forEach(g => {
  const o = document.createElement('option');
  o.value = g.name;
  o.textContent = g.name;
  govSel.appendChild(o);
});

// ── State ──
let basePrice = {{ $product->variants->first(fn ($v) => ! $v->isOutOfStock())?->price ?? $product->variants->first()?->price ?? $product->price }};
let qty = 1;
let shipping = 0;
let selectedVariantId = P.variant_id ?? null;
let selectedAttributes = {};
const hasAttributes = document.getElementById('attributeSelectors') !== null;
const requiredAttrCount = P.attribute_ids?.length || document.querySelectorAll('[data-attr-id]')?.length || 0;

function tierUnitPriceFor(quantity, unitBase = basePrice) {
  if (!P.price_tiers?.length) return unitBase;
  const sorted = [...P.price_tiers].sort((a, b) => a.min - b.min);
  const reference = sorted[0];
  let matched = null;
  for (const t of sorted) {
    if (t.min <= quantity) matched = t;
  }
  if (!matched) return unitBase;
  if (matched.min === reference.min) return unitBase;
  const discount = reference.price - matched.price;
  return Math.max(0, unitBase - discount);
}

function updateTierHighlight(quantity) {
  if (!P.price_tiers?.length) return;
  let matchedMin = -1;
  for (const t of P.price_tiers) {
    if (t.min <= quantity && t.min > matchedMin) matchedMin = t.min;
  }
  document.querySelectorAll('.tier-row').forEach(r => {
    const min = parseInt(r.dataset.tierMin, 10);
    const active = min === matchedMin;
    r.classList.toggle('bg-ink', active);
    r.classList.toggle('text-paper', active);
    r.classList.toggle('bg-paper', !active);
    r.classList.toggle('text-ink', !active);
  });
}

function updateTierTablePrices() {
  if (!P.price_tiers?.length) return;
  const sorted = [...P.price_tiers].sort((a, b) => a.min - b.min);
  const reference = sorted[0];
  document.querySelectorAll('.tier-row').forEach(row => {
    const min = parseInt(row.dataset.tierMin, 10);
    const tier = sorted.find(t => t.min === min);
    if (!tier) return;
    let unit = basePrice;
    if (tier.min !== reference.min) {
      unit = Math.max(0, basePrice - (reference.price - tier.price));
    }
    const priceEl = row.querySelector('[data-tier-price]');
    if (priceEl) priceEl.textContent = ar(unit);
  });
}

function updateShippingUI(fee, reason) {
  const sh = document.getElementById('sumShip');
  if (!sh) return;
  if (fee === 0) {
    sh.textContent = reason ? 'مجاني — ' + reason : 'مجاني';
    sh.className = 'text-accentDark font-bold';
  } else {
    sh.textContent = reason ? ar(fee) + ' ج.م — ' + reason : ar(fee) + ' ج.م';
    sh.className = 'font-semibold';
  }
}

async function recalc() {
  const unitPrice = tierUnitPriceFor(qty);
  const gov = govSel.value;
  if (!gov) {
    shipping = 0;
    updateShippingUI(0, null);
  } else {
    try {
      const res = await fetch(@json(route('shipping.quote')), {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ governorate: gov, subtotal: unitPrice * qty, brand_slug: @json($product->brand->slug) }),
      });
      const data = await res.json();
      shipping = data.fee ?? 0;
      updateShippingUI(shipping, data.reason);
    } catch (_) {
      const g = GOVS.find(x => x.name === gov);
      shipping = g?.fee ?? 0;
      updateShippingUI(shipping, null);
    }
  }
  const productTotal = unitPrice * qty;
  const total = productTotal + shipping;
  setText('priceNow', ar(unitPrice));
  setText('sumProduct', ar(productTotal) + ' ج.م');
  setText('sumTotal', ar(total) + ' ج.م');
  setText('stickyPrice', ar(total) + ' ج.م');
  updateTierTablePrices();
}
govSel.addEventListener('change', recalc);

function matchVariant() {
  const entries = Object.entries(selectedAttributes);
  if (!entries.length || entries.length < requiredAttrCount) return null;

  for (const v of P.variants) {
    if (v.is_out_of_stock) continue;
    const opts = v.option_values || [];
    if (opts.length !== entries.length) continue;

    const ok = entries.every(([attrId, valId]) =>
      opts.some(o => String(o.attribute_id) === String(attrId) && String(o.value_id) === String(valId))
    );
    if (ok) return v;
  }
  return null;
}

function updateVariantResult(variant) {
  const card = document.getElementById('variantResult');
  if (!card) return;
  if (variant) {
    card.style.display = 'flex';
    setText('resultName', variant.name);
    setText('resultPrice', ar(variant.price) + ' ج.م');
    const stockEl = document.getElementById('resultStock');
    if (!stockEl) return;
    const threshold = variant.low_stock_threshold ?? 5;
    if (variant.track_stock && variant.stock <= 0) {
      stockEl.textContent = 'نفد المخزون';
      stockEl.className = 'text-[12px] font-bold text-red-500';
    } else if (variant.track_stock && variant.stock <= threshold) {
      stockEl.textContent = 'متبقي ' + variant.stock + ' فقط';
      stockEl.className = 'text-[12px] font-bold text-orange-500';
    } else {
      stockEl.textContent = variant.track_stock ? 'متوفر — ' + variant.stock + ' في المخزون' : 'متوفر';
      stockEl.className = 'text-[12px] font-bold text-accentDark';
    }
  } else {
    card.style.display = 'none';
  }
}

function updateAttributeUI() {
  document.querySelectorAll('.attr-btn').forEach(btn => {
    const attrId = btn.dataset.attr;
    const valId = btn.dataset.val;
    const isSelected = selectedAttributes[attrId] === valId;
    const ring = btn.querySelector('.attr-ring');
    if (ring) {
      ring.classList.toggle('border-ink', isSelected);
      ring.classList.toggle('border-transparent', !isSelected);
    } else {
      btn.classList.toggle('bg-ink', isSelected);
      btn.classList.toggle('text-paper', isSelected);
      btn.classList.toggle('border-ink', isSelected);
      btn.classList.toggle('border-line', !isSelected);
    }
  });

  document.querySelectorAll('.attr-btn').forEach(btn => {
    const test = { ...selectedAttributes, [btn.dataset.attr]: btn.dataset.val };
    const testEntries = Object.entries(test);
    let hasMatch = false;
    for (const v of P.variants) {
      if (v.is_out_of_stock) continue;
      const opts = v.option_values || [];
      if (opts.length !== testEntries.length) continue;
      const ok = testEntries.every(([aid, vid]) =>
        opts.some(o => String(o.attribute_id) === String(aid) && String(o.value_id) === String(vid))
      );
      if (ok) { hasMatch = true; break; }
    }
    btn.classList.toggle('opacity-40', !hasMatch);
    btn.disabled = !hasMatch;
  });
}

function applyVariant(variant) {
  if (!variant) {
    selectedVariantId = null;
    updateVariantResult(null);
    return;
  }
  basePrice = variant.price;
  selectedVariantId = variant.id;
  updateVariantResult(variant);
  recalc();
}

function selectAttributeValue(el) {
  if (el.disabled) return;
  selectedAttributes[el.dataset.attr] = el.dataset.val;
  updateAttributeUI();
  applyVariant(matchVariant());
}

async function setVariant(el, price, variantId) {
  document.querySelectorAll('[data-variant]').forEach(v => {
    v.classList.remove('variant-active');
    const dot = v.querySelector('.radio-dot');
    if (dot) { dot.classList.remove('scale-100'); dot.classList.add('scale-0'); }
  });
  el.classList.add('variant-active');
  const dot = el.querySelector('.radio-dot');
  if (dot) { dot.classList.remove('scale-0'); dot.classList.add('scale-100'); }
  basePrice = price;
  selectedVariantId = variantId;
  await recalc();
}

async function changeQty(d) {
  qty = Math.max(1, qty + d);
  setText('qty', qty);
  updateTierHighlight(qty);
  await recalc();
  if (d > 0 && typeof trackFbEvent === 'function') trackFbEvent('AddToCart');
}

// ── Page text (optional elements — never crash if missing) ──
setText('pName', P.name);
setText('pDesc', P.desc);
setText('crumbName', P.name);
setText('crumbBrand', P.brand);
setText('hdrBrand', P.brand);
setText('hdrMark', P.mark);
setHref('waHeader', P.wa ? `https://wa.me/${P.wa}` : '#');
if (P.old) setText('priceOld', ar(P.old) + ' ج.م');
document.title = P.name + ' · ' + (@json($product->brand->name));

// ── Init variant / attributes ──
if (hasAttributes && P.variants.length > 0) {
  const firstValid = P.variants.find(v => !v.is_out_of_stock);
  if (firstValid?.option_values) {
    firstValid.option_values.forEach(ov => {
      selectedAttributes[String(ov.attribute_id)] = String(ov.value_id);
    });
    updateAttributeUI();
    applyVariant(firstValid);
  }
} else {
  const firstBtn = document.querySelector('[data-variant]:not([disabled])');
  if (firstBtn) {
    firstBtn.classList.add('variant-active');
    const dot = firstBtn.querySelector('.radio-dot');
    if (dot) { dot.classList.remove('scale-0'); dot.classList.add('scale-100'); }
    selectedVariantId = parseInt(firstBtn.dataset.variantId, 10) || selectedVariantId;
    const priceText = firstBtn.querySelector('.font-extrabold')?.textContent?.replace(/[^\d]/g, '') || String(basePrice);
    basePrice = parseInt(priceText, 10) || basePrice;
  }
  recalc();
}

// ── Gallery ──
function setMedia(i, el) {
  document.querySelectorAll('[data-thumb]').forEach(t => {
    t.classList.remove('border-ink');
    t.classList.add('border-transparent');
  });
  el.classList.remove('border-transparent');
  el.classList.add('border-ink');
  const mm = document.getElementById('mainMediaInner');
  const type = el.dataset.type;
  const url = el.dataset.url;
  if (type === 'video') {
    mm.innerHTML = '<iframe src="' + url + '" class="w-full h-full" frameborder="0" allowfullscreen></iframe>';
  } else {
    mm.innerHTML = '<img src="' + url + '" alt="" class="w-full h-full object-cover">';
  }
}

const _thumbs = document.querySelectorAll('[data-thumb]');
let _slideTimer;
function _startSlide() {
  if (_thumbs.length < 2) return;
  let cur = 0;
  _slideTimer = setInterval(() => { cur = (cur + 1) % _thumbs.length; _thumbs[cur].click(); }, 3500);
}
function _stopSlide() { clearInterval(_slideTimer); }
_startSlide();
document.getElementById('mainMedia')?.addEventListener('mouseenter', _stopSlide);
document.getElementById('mainMedia')?.addEventListener('mouseleave', _startSlide);

// ── Validate + orders ──
const FB_BRAND_ID = {{ (int) $product->brand_id }};
const FB_CURRENCY = @json($currency ?? 'EGP');

function fbCommerceParams() {
  const unitPrice = tierUnitPriceFor(qty);
  return {
    content_ids: [String(P.id)],
    content_type: 'product',
    content_name: P.name,
    value: unitPrice * qty + shipping,
    currency: FB_CURRENCY,
    num_items: qty,
  };
}

function fireFbPixel(payload) {
  if (!payload || !window.__fbPixelTrack) return;
  window.__fbPixelTrack(payload.event_id, payload.event_name, payload.params || {});
}

async function trackFbEvent(eventName, userData) {
  try {
    const res = await fetch(@json(route('facebook-pixel.track')), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
      },
      body: JSON.stringify({
        event_name: eventName,
        brand_id: FB_BRAND_ID,
        custom_data: fbCommerceParams(),
        user_data: userData || null,
      }),
    });
    const json = await res.json();
    fireFbPixel(json.fb_pixel);
    return json.fb_pixel;
  } catch (_) {}
}

function validate() {
  if (hasAttributes && !selectedVariantId) {
    toast('من فضلك اختر اللون والحجم');
    return null;
  }
  const n = document.getElementById('f_name').value.trim();
  const p = document.getElementById('f_phone').value.trim();
  const g = govSel.value;
  const a = document.getElementById('f_address').value.trim();
  if (!n) { toast('من فضلك أدخل الاسم'); return null; }
  if (!/^01[0-9]{9}$/.test(p)) { toast('رقم موبايل غير صحيح'); return null; }
  if (!g) { toast('اختر المحافظة'); return null; }
  if (!a) { toast('أدخل العنوان بالتفصيل'); return null; }
  return { n, p, g, a };
}

async function postOrder(d, method, receipt) {
  const fd = new FormData();
  fd.append('product_id', P.id);
  if (selectedVariantId) fd.append('variant_id', selectedVariantId);
  fd.append('qty', qty);
  fd.append('customer_name', d.n);
  fd.append('customer_phone', d.p);
  fd.append('governorate', d.g);
  fd.append('address', d.a);
  fd.append('payment_method', method);
  if (receipt) fd.append('receipt', receipt);
  const res = await fetch(@json(route('order.store')), {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
      'Accept': 'application/json',
    },
    body: fd,
  });
  const json = await res.json();
  if (!res.ok && json.errors) {
    throw new Error(Object.values(json.errors).flat()[0]);
  }
  return json;
}

function submitCOD() {
  const d = validate(); if (!d) return;
  trackFbEvent('InitiateCheckout', { ph: d.p, fn: d.n.split(' ')[0] || '' });
  postOrder(d, 'cod').then(j => {
    if (j.success) {
      fireFbPixel(j.fb_pixel);
      toast('تم استلام طلبك رقم ' + j.data.order_no + '! هنتواصل معاك');
    } else {
      toast('حصل خطأ، حاول تاني');
    }
  }).catch(e => toast(e.message || 'تأكد من الاتصال وحاول تاني'));
}

function orderWhatsapp() {
  const d = validate();
  const unitPrice = tierUnitPriceFor(qty);
  const total = unitPrice * qty + shipping;
  let m = `طلب جديد من ${P.brand}%0Aالمنتج: ${P.name}%0Aالكمية: ${qty}%0Aالإجمالي: ${ar(total)} ج.م`;
  if (d) m += `%0Aالاسم: ${d.n}%0Aالموبايل: ${d.p}%0Aالمحافظة: ${d.g}%0Aالعنوان: ${d.a}`;
  if (d) {
    trackFbEvent('Lead', { ph: d.p, fn: d.n.split(' ')[0] || '' });
    postOrder(d, 'whatsapp').catch(() => {});
  }
  window.open(`https://wa.me/${P.wa}?text=${m}`, '_blank');
}

// ── Transfer flow ──
let receiptFile = null;

function resetReceiptUpload() {
  receiptFile = null;
  const input = document.getElementById('receiptInput');
  if (input) input.value = '';
  document.getElementById('upPlaceholder')?.classList.remove('hidden');
  document.getElementById('upPreview')?.classList.add('hidden');
  const box = document.getElementById('uploadBox');
  if (box) {
    box.classList.add('border-dashed', 'border-line');
    box.classList.remove('border-accent', 'border-solid');
  }
}

function openTransfer() {
  if (!validate()) return;
  trackFbEvent('InitiateCheckout');
  setText('vfNum', P.vf || '—');
  setText('ipNum', P.ip || '—');
  const unitPrice = tierUnitPriceFor(qty);
  setText('transferAmount', ar(unitPrice * qty + shipping) + ' ج.م');
  resetReceiptUpload();
  const m = document.getElementById('transferModal');
  m.classList.remove('hidden');
  m.classList.add('flex');
}

function closeTransfer() {
  const m = document.getElementById('transferModal');
  m.classList.add('hidden');
  m.classList.remove('flex');
}

function handleReceipt(input) {
  const f = input.files && input.files[0];
  if (!f) return;
  if (f.size > 5 * 1024 * 1024) {
    toast('حجم الصورة كبير (أقصى 5MB)');
    input.value = '';
    return;
  }
  receiptFile = f;
  document.getElementById('upImg').src = URL.createObjectURL(f);
  document.getElementById('upPlaceholder').classList.add('hidden');
  document.getElementById('upPreview').classList.remove('hidden');
  const box = document.getElementById('uploadBox');
  box.classList.remove('border-dashed', 'border-line');
  box.classList.add('border-accent', 'border-solid');
}

function confirmTransfer() {
  if (!receiptFile) { toast('من فضلك ارفع صورة الإيصال أولًا'); return; }
  const d = validate(); if (!d) return;
  postOrder(d, 'transfer', receiptFile).then(j => {
    closeTransfer();
    if (j.success) {
      fireFbPixel(j.fb_pixel);
      toast('تم تأكيد طلبك رقم ' + j.data.order_no + '! وصلنا الإيصال');
      if (j.data.whatsapp_url) setTimeout(() => window.open(j.data.whatsapp_url, '_blank'), 900);
      resetReceiptUpload();
    } else {
      toast('حصل خطأ في رفع الإيصال');
    }
  }).catch(e => toast(e.message || 'تأكد من الاتصال وحاول تاني'));
}

function copyNum(elid, btn) {
  navigator.clipboard?.writeText(document.getElementById(elid).textContent.trim());
  const o = btn.textContent;
  btn.textContent = 'تم ✓';
  setTimeout(() => { btn.textContent = o; }, 1400);
}

document.getElementById('transferModal')?.addEventListener('click', e => {
  if (e.target.id === 'transferModal') closeTransfer();
});

let _tt;
function toast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.remove('opacity-0', 'translate-y-5');
  t.classList.add('opacity-100', 'translate-y-0');
  clearTimeout(_tt);
  _tt = setTimeout(() => {
    t.classList.add('opacity-0', 'translate-y-5');
    t.classList.remove('opacity-100', 'translate-y-0');
  }, 2800);
}

const io = new IntersectionObserver(es => {
  es.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
  });
}, { threshold: .12, rootMargin: '0px 0px -40px 0px' });
document.querySelectorAll('.reveal,.reveal-l,.reveal-scale,.stagger').forEach(el => io.observe(el));

@if(session('review_flash'))
toast(@json(session('review_flash')));
@endif

updateTierHighlight(qty);
if (!hasAttributes) recalc();
</script>
@endpush
