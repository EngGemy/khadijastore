<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $brands = Brand::query()
            ->where('is_active', true)
            ->select('slug', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        $products = Product::withoutGlobalScopes()
            ->where('is_active', true)
            ->select('slug', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        return response($this->buildXml($brands, $products), 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    /**
     * Build sitemap XML in PHP — never use Blade for XML (<?xml breaks compilation).
     *
     * @param  Collection<int, Brand>  $brands
     * @param  Collection<int, Product>  $products
     */
    private function buildXml(Collection $brands, Collection $products): string
    {
        $lines = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
            $this->urlEntry(url('/'), changefreq: 'daily', priority: '1.0'),
        ];

        foreach ($brands as $brand) {
            $lastmod = $brand->updated_at?->toAtomString();

            $lines[] = $this->urlEntry(
                route('brand.show', $brand->slug),
                lastmod: $lastmod,
                changefreq: 'weekly',
                priority: '0.9',
            );
            $lines[] = $this->urlEntry(
                route('brand.shop', $brand->slug),
                lastmod: $lastmod,
                changefreq: 'daily',
                priority: '0.85',
            );
        }

        foreach ($products as $product) {
            $lines[] = $this->urlEntry(
                route('product.show', $product->slug),
                lastmod: $product->updated_at?->toAtomString(),
                changefreq: 'weekly',
                priority: '0.7',
            );
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines)."\n";
    }

    private function urlEntry(
        string $loc,
        ?string $lastmod = null,
        ?string $changefreq = null,
        ?string $priority = null,
    ): string {
        $parts = [
            '  <url>',
            '    <loc>'.htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</loc>',
        ];

        if ($lastmod) {
            $parts[] = '    <lastmod>'.htmlspecialchars($lastmod, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</lastmod>';
        }

        if ($changefreq) {
            $parts[] = '    <changefreq>'.htmlspecialchars($changefreq, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</changefreq>';
        }

        if ($priority) {
            $parts[] = '    <priority>'.htmlspecialchars($priority, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</priority>';
        }

        $parts[] = '  </url>';

        return implode("\n", $parts);
    }
}
