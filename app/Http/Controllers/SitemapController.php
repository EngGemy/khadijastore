<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $brands = Brand::where('is_active', true)
            ->select('slug', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        $products = Product::withoutGlobalScopes()
            ->where('is_active', true)
            ->select('slug', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        $body = view('sitemap', compact('brands', 'products'))->render();

        // Keep the XML declaration in PHP — Blade treats "<?xml" as PHP and breaks compilation.
        $content = '<?xml version="1.0" encoding="UTF-8"?>'."\n".$body;

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}
