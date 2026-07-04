@php $seoMeta = $seo ?? []; @endphp
@if(!empty($seoMeta['description']))<meta name="description" content="{{ $seoMeta['description'] }}">@endif
<link rel="canonical" href="{{ $seoMeta['url'] ?? url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $seoMeta['title'] ?? $brand->name }}">
<meta property="og:description" content="{{ $seoMeta['description'] ?? '' }}">
<meta property="og:url" content="{{ $seoMeta['url'] ?? url()->current() }}">
@if(!empty($seoMeta['image']))<meta property="og:image" content="{{ $seoMeta['image'] }}">@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoMeta['title'] ?? $brand->name }}">

<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BreadcrumbList",
  "itemListElement": [
    {"@@type":"ListItem","position":1,"name":"الرئيسية","item":"{{ url('/') }}"},
    {"@@type":"ListItem","position":2,"name":"{{ e($brand->name) }}","item":"{{ route('brand.show', $brand->slug) }}"}
    @if(!empty($breadcrumbLabel))
    ,{"@@type":"ListItem","position":3,"name":"{{ e($breadcrumbLabel) }}","item":"{{ $seoMeta['url'] ?? url()->current() }}"}
    @endif
  ]
}
</script>

<x-facebook-pixel
    :brand-id="$brand->id"
    :page-view-event-id="$fbPageView['event_id'] ?? null"
/>
