@php
  $seoMeta = $seo ?? [];
  $pageUrl = $seoMeta['url'] ?? brand_page_url($brand->slug);
  $pageTitle = $seoMeta['title'] ?? $brand->name;
  $pageDesc = $seoMeta['description'] ?? $brand->description ?? '';
  $pageImage = $seoMeta['image'] ?? $brand->getFirstMediaUrl('logo', 'thumb');
  if ($pageImage !== '' && ! str_starts_with($pageImage, 'http')) {
      $pageImage = url($pageImage);
  }
  $storeName = setting('store.name', 'متجر العلامات', $brand->id);
@endphp
@if($pageDesc !== '')<meta name="description" content="{{ $pageDesc }}">@endif
<link rel="canonical" href="{{ $pageUrl }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $storeName }}">
<meta property="og:locale" content="ar_EG">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDesc }}">
<meta property="og:url" content="{{ $pageUrl }}">
@if($pageImage !== '')<meta property="og:image" content="{{ $pageImage }}">@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDesc }}">
@if($pageImage !== '')<meta name="twitter:image" content="{{ $pageImage }}">@endif

<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BreadcrumbList",
  "itemListElement": [
    {"@@type":"ListItem","position":1,"name":"الرئيسية","item":"{{ url('/') }}"},
    {"@@type":"ListItem","position":2,"name":"{{ e($brand->name) }}","item":"{{ brand_page_url($brand->slug) }}"}
    @if(!empty($breadcrumbLabel))
    ,{"@@type":"ListItem","position":3,"name":"{{ e($breadcrumbLabel) }}","item":"{{ $pageUrl }}"}
    @endif
  ]
}
</script>

<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Store",
  "name": "{{ e($brand->name) }}",
  "description": "{{ e($pageDesc) }}",
  "url": "{{ $pageUrl }}",
  @if($pageImage !== '')"image": "{{ $pageImage }}",@endif
  @if($brand->whatsapp)"telephone": "+{{ $brand->whatsapp }}",@endif
  "parentOrganization": {
    "@@type": "Organization",
    "name": "{{ e($storeName) }}",
    "url": "{{ url('/') }}"
  }
}
</script>

<x-facebook-pixel
    :brand-id="$brand->id"
    :page-view-event-id="$fbPageView['event_id'] ?? null"
/>
