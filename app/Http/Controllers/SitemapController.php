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

        $content = view('sitemap', compact('brands', 'products'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}
