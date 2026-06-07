<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // خريطة التحويل: [الاسم => ['old_slug' => ..., 'new_slug' => ...]]
        // يمكن تعديل new_slug حسب رغبة المالك قبل التشغيل
        $map = [
            'براند العناية' => [
                'old_slug' => 'brand-alaanay',
                'new_slug' => 'brand-alanaya',
            ],
            'موبايل ستور' => [
                'old_slug' => 'mobayl-stor',
                'new_slug' => 'mobile-store',
            ],
            'عطور النخبة' => [
                'old_slug' => 'aator-alnkhb',
                'new_slug' => 'elite-perfumes',
            ],
        ];

        foreach ($map as $name => $slugs) {
            $brand = DB::table('brands')->where('name', $name)->first();
            if (! $brand) {
                continue;
            }

            // تجنب التصادم: إذا كان new_slug مستخدمًا من قبل brand آخر
            $exists = DB::table('brands')->where('slug', $slugs['new_slug'])->where('id', '!=', $brand->id)->exists();
            if ($exists) {
                $slugs['new_slug'] = $slugs['new_slug'] . '-' . $brand->id;
            }

            $oldSlugs = json_decode($brand->old_slugs ?? '[]', true) ?: [];
            $oldSlugs[] = $brand->slug;
            $oldSlugs = array_values(array_unique($oldSlugs));

            DB::table('brands')->where('id', $brand->id)->update([
                'slug' => $slugs['new_slug'],
                'old_slugs' => json_encode($oldSlugs),
            ]);
        }
    }

    public function down(): void
    {
        // لا يوجد rollback آمن للـ slugs دون خريطة عكسية؛ يُترك فارغًا أو يُعالج يدويًا
    }
};
