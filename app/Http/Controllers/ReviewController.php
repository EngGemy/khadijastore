<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, string $slug): RedirectResponse
    {
        $product = Product::withoutGlobalScopes()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'governorate' => ['nullable', 'string', 'max:100'],
        ]);

        Review::create([
            'product_id' => $product->id,
            'brand_id' => $product->brand_id,
            'customer_name' => $validated['customer_name'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'governorate' => $validated['governorate'],
            'is_approved' => false,
        ]);

        return back()->with('review_flash', 'شكرًا! مراجعتك في انتظار المراجعة');
    }
}
