<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DirectoryController extends Controller
{
    /** صفحة قائمة الدليل (أطباء أو حضانات) */
    public function index(string $type): View
    {
        $typeLabel = Listing::types()[$type] ?? $type;

        $query = Listing::withoutGlobalScopes()
            ->where('type', $type)
            ->where('is_active', true)
            ->with(['serviceCategory', 'media']);

        $this->applyFilters($query, request()->only(['search', 'category', 'governorate', 'specialty']));

        $listings = $query->orderByDesc('is_featured')->orderBy('sort')->paginate(12)->withQueryString();

        $base = Listing::withoutGlobalScopes()->where('type', $type)->where('is_active', true);

        $stats = [
            'count' => (clone $base)->count(),
            'governorates' => (clone $base)->whereNotNull('governorate')->distinct()->pluck('governorate')->filter()->values(),
        ];

        if ($type === Listing::TYPE_DOCTOR) {
            $stats['specialties'] = (clone $base)->whereNotNull('data')->get()
                ->map(fn ($l) => $l->data['specialty'] ?? null)
                ->filter()
                ->unique()
                ->values();
        }

        $categories = ServiceCategory::withoutGlobalScopes()
            ->where('type', $type)
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $seo = [
            'title' => ($type === Listing::TYPE_DOCTOR ? 'دليل الأطباء' : 'دليل الحضانات').' · متجر العلامات',
            'description' => $type === Listing::TYPE_DOCTOR
                ? 'ابحث عن أفضل الأطباء في منطقتك — تواصل مباشر بدون حجز مسبق'
                : 'اكتشف أفضل الحضانات المعتمدة لطفلك — معلومات مفصّلة وتواصل مباشر',
        ];

        return view('directory.index', compact(
            'type', 'typeLabel', 'listings', 'categories', 'stats', 'seo'
        ));
    }

    /** صفحة تفاصيل إدراج */
    public function show(string $type, string $slug): View
    {
        $listing = Listing::withoutGlobalScopes()
            ->where('type', $type)
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with(['serviceCategory', 'media'])
            ->firstOrFail();

        $listing->incrementViews();

        $gallery = $listing->getMedia('gallery')->map(
            fn ($m) => ['url' => $m->getUrl('large'), 'thumb' => $m->getUrl('thumb')]
        )->values()->all();

        $related = $this->relatedListings($listing);

        $jsonLd = $this->buildJsonLd($listing);

        $seo = [
            'title' => $listing->meta_title ?: $listing->name.' · متجر العلامات',
            'description' => $listing->meta_description ?: $listing->summary,
            'image' => $listing->getFirstMediaUrl('cover', 'large'),
            'url' => route('directory.show', [$type, $listing->slug]),
        ];

        return view('directory.show', compact('listing', 'gallery', 'related', 'jsonLd', 'seo', 'type'));
    }

    /** API حي للفلترة */
    public function apiList(string $type, Request $request): JsonResponse
    {
        $query = Listing::withoutGlobalScopes()
            ->where('type', $type)
            ->where('is_active', true)
            ->with(['serviceCategory:id,name', 'media']);

        $this->applyFilters($query, $request->only(['search', 'category', 'governorate', 'specialty']));

        $items = $query->orderByDesc('is_featured')->orderBy('sort')->take(48)->get()
            ->map(fn (Listing $l) => [
                'id' => $l->id,
                'name' => $l->name,
                'name_en' => $l->name_en,
                'slug' => $l->slug,
                'summary' => $l->summary,
                'governorate' => $l->governorate,
                'category' => $l->serviceCategory?->name,
                'rating' => $l->rating,
                'is_featured' => $l->is_featured,
                'specialty' => $l->data['specialty'] ?? null,
                'age_range' => $l->age_range_text,
                'fees_range' => $l->fees_range_text,
                'cover_thumb' => $l->getFirstMediaUrl('cover', 'thumb'),
                'url' => route('directory.show', [$type, $l->slug]),
                'whatsapp_url' => $l->whatsapp_url,
                'phone' => $l->phone,
            ]);

        return response()->json(['data' => $items]);
    }

    private function applyFilters($query, array $filters): void
    {
        if ($search = trim((string) ($filters['search'] ?? ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('name_en', 'like', '%'.$search.'%')
                    ->orWhere('summary', 'like', '%'.$search.'%')
                    ->orWhere('governorate', 'like', '%'.$search.'%')
                    ->orWhere('address', 'like', '%'.$search.'%')
                    ->orWhere('data->specialty', 'like', '%'.$search.'%')
                    ->orWhere('data->specialty_en', 'like', '%'.$search.'%');
            });
        }

        if ($cat = $filters['category'] ?? null) {
            $query->where('service_category_id', $cat);
        }

        if ($gov = $filters['governorate'] ?? null) {
            $query->where('governorate', $gov);
        }

        if ($specialty = trim((string) ($filters['specialty'] ?? ''))) {
            $query->where('data->specialty', $specialty);
        }
    }

    private function relatedListings(Listing $listing)
    {
        $base = Listing::withoutGlobalScopes()
            ->where('type', $listing->type)
            ->where('is_active', true)
            ->where('id', '!=', $listing->id)
            ->with(['serviceCategory', 'media']);

        $related = (clone $base)
            ->where(function ($q) use ($listing) {
                if ($listing->service_category_id) {
                    $q->where('service_category_id', $listing->service_category_id);
                }
                if ($listing->governorate) {
                    $q->orWhere('governorate', $listing->governorate);
                }
            })
            ->orderByDesc('is_featured')
            ->orderBy('sort')
            ->take(3)
            ->get();

        if ($related->isEmpty()) {
            $related = $base->orderByDesc('is_featured')->orderBy('sort')->take(3)->get();
        }

        return $related;
    }

    private function buildJsonLd(Listing $listing): string
    {
        if ($listing->type === Listing::TYPE_DOCTOR) {
            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'Physician',
                'name' => $listing->name,
                'url' => route('directory.show', [$listing->type, $listing->slug]),
                'image' => $listing->getFirstMediaUrl('cover', 'large'),
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $listing->address,
                    'addressLocality' => $listing->governorate,
                    'addressCountry' => 'EG',
                ],
                'telephone' => $listing->phone,
                'medicalSpecialty' => $listing->data['specialty_en'] ?? $listing->data['specialty'] ?? '',
            ];
        } else {
            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'ChildCare',
                'name' => $listing->name,
                'url' => route('directory.show', [$listing->type, $listing->slug]),
                'image' => $listing->getFirstMediaUrl('cover', 'large'),
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $listing->address,
                    'addressLocality' => $listing->governorate,
                    'addressCountry' => 'EG',
                ],
                'telephone' => $listing->phone,
            ];
        }

        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
