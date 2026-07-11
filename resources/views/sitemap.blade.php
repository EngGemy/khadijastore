{{-- XML prolog is prepended in SitemapController — never put it in Blade templates. --}}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>{{ url('/') }}</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  @foreach($brands as $brand)
  <url>
    <loc>{{ route('brand.show', $brand->slug) }}</loc>
    <lastmod>{{ $brand->updated_at->toAtomString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>
  <url>
    <loc>{{ route('brand.shop', $brand->slug) }}</loc>
    <lastmod>{{ $brand->updated_at->toAtomString() }}</lastmod>
    <changefreq>daily</changefreq>
    <priority>0.85</priority>
  </url>
  @endforeach
  @foreach($products as $product)
  <url>
    <loc>{{ route('product.show', $product->slug) }}</loc>
    <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>
  @endforeach
</urlset>
