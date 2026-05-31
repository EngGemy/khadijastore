<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Services\ShippingCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingQuoteController extends Controller
{
    public function __construct(private readonly ShippingCalculator $shipping) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'governorate' => ['required', 'string'],
            'subtotal' => ['required', 'integer', 'min:0'],
            'brand_slug' => ['nullable', 'string', 'exists:brands,slug'],
        ]);

        $brandId = null;
        if (! empty($validated['brand_slug'])) {
            $brand = Brand::where('slug', $validated['brand_slug'])->first();
            $brandId = $brand?->id;
        }

        $result = $this->shipping->calculate(
            $validated['governorate'],
            (int) $validated['subtotal'],
            $brandId
        );

        return response()->json([
            'fee' => $result['fee'],
            'free' => $result['free'],
            'reason' => $result['reason'],
        ]);
    }
}
