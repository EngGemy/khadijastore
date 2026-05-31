<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        // الثيم الافتراضي (ماكينزي أبيض/أسود) — مفعّل دائمًا كقاعدة
        Theme::updateOrCreate(['key' => 'default'], [
            'name' => 'الافتراضي',
            'scope' => 'global',
            'tokens' => Theme::defaultTokens(),
            'is_active' => true,
            'priority' => 0,
        ]);

        // ثيم العيد — ذهبي/أخضر، مجدول (يُفعّل ويُطفأ تلقائيًا في تواريخه)
        Theme::updateOrCreate(['key' => 'eid'], [
            'name' => 'ثيم العيد',
            'scope' => 'global',
            'tokens' => [
                'accent' => '#c9a227',          // ذهبي
                'accentDark' => '#a8841a',
                'ink' => '#0a0a0a',
                'paper' => '#fffdf7',           // عاجي دافئ
                'strip_text' => 'عيد سعيد · عروض العيد بأسعار خاصة · شحن مجاني',
                'badge' => 'عرض العيد',
            ],
            'is_active' => true,
            'priority' => 10,
            // مثال: فعّال خلال أيام العيد فقط — عدّل التواريخ حسب الحاجة
            'starts_at' => null,
            'ends_at' => null,
        ]);

        // ثيم رمضان — بنفسجي/ذهبي
        Theme::updateOrCreate(['key' => 'ramadan'], [
            'name' => 'ثيم رمضان',
            'scope' => 'global',
            'tokens' => [
                'accent' => '#7c3aed',
                'accentDark' => '#6d28d9',
                'ink' => '#1a1033',
                'paper' => '#faf8ff',
                'strip_text' => 'رمضان كريم · عروض حصرية طوال الشهر الكريم',
                'badge' => 'عرض رمضان',
            ],
            'is_active' => false,
            'priority' => 10,
        ]);

        // ثيم الجمعة البيضاء — أسود/أحمر
        Theme::updateOrCreate(['key' => 'black_friday'], [
            'name' => 'الجمعة البيضاء',
            'scope' => 'global',
            'tokens' => [
                'accent' => '#dc2626',
                'accentDark' => '#b91c1c',
                'ink' => '#000000',
                'paper' => '#ffffff',
                'strip_text' => 'الجمعة البيضاء · خصومات تصل إلى 70%',
                'badge' => 'خصم ضخم',
            ],
            'is_active' => false,
            'priority' => 20,
        ]);
    }
}
