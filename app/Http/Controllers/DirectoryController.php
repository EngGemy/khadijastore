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

        // فلاتر query-string
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                  ->orWhere('name_en', 'like', '%'.$search.'%')
                  ->orWhere('summary', 'like', '%'.$search.'%');
            });
        }

        if ($cat = request('category')) {
            $query->where('service_category_id', $cat);
        }

        if ($gov = request('governorate')) {
            $query->where('governorate', $gov);
        }

        $listings = $query->orderByDesc('is_featured')->orderBy('sort')->paginate(12)->withQueryString();

        // إحصاءات
        $base = Listing::withoutGlobalScopes()->where('type', $type)->where('is_active', true);

        $stats = [
            'count'       => $base->count(),
            'governorates'=> $base->whereNotNull('governorate')->distinct('governorate')->pluck('governorate'),
        ];

        if ($type === Listing::TYPE_DOCTOR) {
            $stats['specialties'] = $base->whereNotNull('data->specialty')->get()
                ->map(fn ($l) => ($l->data['specialty'] ?? null))
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
            'title'       => ($type === Listing::TYPE_DOCTOR ? 'دليل الأطباء' : 'دليل الحضانات') . ' · متجر العلامات',
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

        $jsonLd = $this->buildJsonLd($listing);

        $seo = [
            'title'       => $listing->meta_title ?: $listing->name . ' · متجر العلامات',
            'description' => $listing->meta_description ?: $listing->summary,
            'image'       => $listing->getFirstMediaUrl('cover', 'large'),
            'url'         => route('directory.show', [$type, $listing->slug]),
        ];

        return view('directory.show', compact('listing', 'gallery', 'jsonLd', 'seo', 'type'));
    }

    /** API حي للفلترة */
    public function apiList(string $type, Request $request): JsonResponse
    {
        $query = Listing::withoutGlobalScopes()
            ->where('type', $type)
            ->where('is_active', true)
            ->with(['serviceCategory:id,name', 'media']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                  ->orWhere('name_en', 'like', '%'.$search.'%')
                  ->orWhere('governorate', 'like', '%'.$search.'%');
            });
        }

        if ($cat = $request->input('category')) {
            $query->where('service_category_id', $cat);
        }

        if ($gov = $request->input('governorate')) {
            $query->where('governorate', $gov);
        }

        $items = $query->orderByDesc('is_featured')->orderBy('sort')->take(48)->get()
            ->map(fn (Listing $l) => [
                'id'           => $l->id,
                'name'         => $l->name,
                'name_en'      => $l->name_en,
                'slug'         => $l->slug,
                'summary'      => $l->summary,
                'governorate'  => $l->governorate,
                'category'     => $l->serviceCategory?->name,
                'rating'       => $l->rating,
                'is_featured'  => $l->is_featured,
                'specialty'    => $l->data['specialty'] ?? null,
                'age_range'    => $l->age_range_text,
                'fees_range'   => $l->fees_range_text,
                'cover_thumb'  => $l->getFirstMediaUrl('cover', 'thumb'),
                'url'          => route('directory.show', [$type, $l->slug]),
                'whatsapp_url' => $l->whatsapp_url,
                'phone'        => $l->phone,
            ]);

        return response()->json(['data' => $items]);
    }

    private function buildJsonLd(Listing $listing): string
    {
        if ($listing->type === Listing::TYPE_DOCTOR) {
            $schema = [
                '@context' => 'https://schema.org',
                '@type'    => 'Physician',
                'name'     => $listing->name,
                'url'      => route('directory.show', [$listing->type, $listing->slug]),
                'image'    => $listing->getFirstMediaUrl('cover', 'large'),
                'address'  => [
                    '@type'           => 'PostalAddress',
                    'streetAddress'   => $listing->address,
                    'addressLocality' => $listing->governorate,
                    'addressCountry'  => 'EG',
                ],
                'telephone'          => $listing->phone,
                'medicalSpecialty'   => $listing->data['specialty_en'] ?? $listing->data['specialty'] ?? '',
            ];
        } else {
            $schema = [
                '@context' => 'https://schema.org',
                '@type'    => 'ChildCare',
                'name'     => $listing->name,
                'url'      => route('directory.show', [$listing->type, $listing->slug]),
                'image'    => $listing->getFirstMediaUrl('cover', 'large'),
                'address'  => [
                    '@type'           => 'PostalAddress',
                    'streetAddress'   => $listing->address,
                    'addressLocality' => $listing->governorate,
                    'addressCountry'  => 'EG',
                ],
                'telephone' => $listing->phone,
            ];
        }

        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
