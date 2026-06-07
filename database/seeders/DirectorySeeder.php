<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DirectorySeeder extends Seeder
{
    public function run(): void
    {
        // ── أقسام الأطباء ────────────────────────────────────────────────────
        $docCats = [
            ['name' => 'باطنة وقلب', 'name_en' => 'Internal Medicine & Cardiology', 'sort' => 1],
            ['name' => 'أطفال',       'name_en' => 'Pediatrics',                      'sort' => 2],
            ['name' => 'عيون',        'name_en' => 'Ophthalmology',                   'sort' => 3],
            ['name' => 'نساء وتوليد', 'name_en' => 'Gynecology & Obstetrics',        'sort' => 4],
        ];

        foreach ($docCats as $cat) {
            ServiceCategory::withoutGlobalScopes()->firstOrCreate(
                ['type' => Listing::TYPE_DOCTOR, 'brand_id' => null, 'slug' => Str::slug($cat['name_en'])],
                array_merge($cat, ['type' => Listing::TYPE_DOCTOR, 'is_active' => true])
            );
        }

        // ── أقسام الحضانات ───────────────────────────────────────────────────
        $nursCats = [
            ['name' => 'حضانات يومية',  'name_en' => 'Day Care',       'sort' => 1],
            ['name' => 'مراكز مونتيسوري', 'name_en' => 'Montessori',   'sort' => 2],
        ];

        foreach ($nursCats as $cat) {
            ServiceCategory::withoutGlobalScopes()->firstOrCreate(
                ['type' => Listing::TYPE_NURSERY, 'brand_id' => null, 'slug' => Str::slug($cat['name_en'])],
                array_merge($cat, ['type' => Listing::TYPE_NURSERY, 'is_active' => true])
            );
        }

        $docInternalCat  = ServiceCategory::withoutGlobalScopes()->where('type', 'doctor')->where('slug', 'internal-medicine-cardiology')->first();
        $docChildrenCat  = ServiceCategory::withoutGlobalScopes()->where('type', 'doctor')->where('slug', 'pediatrics')->first();
        $nursDayCat      = ServiceCategory::withoutGlobalScopes()->where('type', 'nursery')->where('slug', 'day-care')->first();
        $nursMonteCat    = ServiceCategory::withoutGlobalScopes()->where('type', 'nursery')->where('slug', 'montessori')->first();

        // ── أطباء نموذجيون ───────────────────────────────────────────────────
        $doctors = [
            [
                'name'                => 'د. أحمد محمود السيد',
                'name_en'             => 'Dr. Ahmed Mahmoud El-Sayed',
                'slug'                => 'dr-ahmed-mahmoud-'.Str::random(4),
                'type'                => Listing::TYPE_DOCTOR,
                'service_category_id' => $docInternalCat?->id,
                'summary'             => 'استشاري باطنة وأمراض القلب — خبرة أكثر من 18 عامًا في تشخيص وعلاج أمراض القلب والضغط والسكر.',
                'summary_en'          => 'Consultant Internist & Cardiologist with 18+ years of clinical experience.',
                'phone'               => '01001234567',
                'whatsapp'            => '201001234567',
                'address'             => 'ش الهرم، بجوار محطة مترو جامعة القاهرة',
                'governorate'         => 'الجيزة',
                'is_active'           => true,
                'is_featured'         => true,
                'rating'              => 4.8,
                'data'                => [
                    'title'            => 'استشاري',
                    'specialty'        => 'باطنة وقلب',
                    'specialty_en'     => 'Cardiology & Internal Medicine',
                    'clinic_name'      => 'عيادة السيد القلبية',
                    'experience_years' => 18,
                    'consultation_fee' => 350,
                    'working_hours'    => 'السبت – الأربعاء 4 م – 9 م',
                    'services'         => ['رسم قلب', 'قسطرة تشخيصية', 'ضغط الدم', 'سكر النوع الثاني'],
                    'languages'        => ['العربية', 'English'],
                ],
            ],
            [
                'name'                => 'د. سارة علي عبدالرحمن',
                'name_en'             => 'Dr. Sara Ali AbdelRahman',
                'slug'                => 'dr-sara-ali-'.Str::random(4),
                'type'                => Listing::TYPE_DOCTOR,
                'service_category_id' => $docChildrenCat?->id,
                'summary'             => 'أخصائية طب أطفال وتغذية — تُقدّم رعاية متكاملة للأطفال من الولادة حتى المراهقة.',
                'phone'               => '01109876543',
                'whatsapp'            => '201109876543',
                'address'             => 'مدينة نصر، بجوار المستشفى التخصصي',
                'governorate'         => 'القاهرة',
                'is_active'           => true,
                'is_featured'         => false,
                'rating'              => 4.6,
                'data'                => [
                    'title'            => 'أخصائي',
                    'specialty'        => 'طب أطفال وتغذية',
                    'specialty_en'     => 'Pediatrics & Nutrition',
                    'clinic_name'      => 'عيادة الأطفال البهيجة',
                    'experience_years' => 12,
                    'consultation_fee' => 250,
                    'working_hours'    => 'الأحد – الخميس 3 م – 8 م',
                    'services'         => ['تطعيمات', 'متابعة النمو', 'تغذية الرضّع', 'أمراض الجهاز التنفسي'],
                    'languages'        => ['العربية'],
                ],
            ],
            [
                'name'                => 'د. محمد فوزي حسن',
                'name_en'             => 'Dr. Mohamed Fawzy Hassan',
                'slug'                => 'dr-fawzy-'.Str::random(4),
                'type'                => Listing::TYPE_DOCTOR,
                'service_category_id' => $docInternalCat?->id,
                'summary'             => 'استشاري مخ وأعصاب — خبرة 20 عامًا في علاج الصداع النصفي والجلطات والصرع.',
                'phone'               => '01205556789',
                'whatsapp'            => '201205556789',
                'address'             => 'المعادي، برج الشفاء الطبي',
                'governorate'         => 'القاهرة',
                'is_active'           => true,
                'is_featured'         => false,
                'rating'              => 4.9,
                'data'                => [
                    'title'            => 'استشاري',
                    'specialty'        => 'مخ وأعصاب',
                    'specialty_en'     => 'Neurology',
                    'clinic_name'      => 'مركز فوزي للمخ والأعصاب',
                    'experience_years' => 20,
                    'consultation_fee' => 400,
                    'working_hours'    => 'الأحد والثلاثاء والخميس 5 م – 9 م',
                    'services'         => ['صداع نصفي', 'جلطات دماغية', 'صرع', 'رعاش بارنكسون'],
                    'languages'        => ['العربية', 'English', 'Français'],
                ],
            ],
        ];

        foreach ($doctors as $doc) {
            Listing::withoutGlobalScopes()->firstOrCreate(
                ['slug' => $doc['slug']],
                $doc
            );
        }

        // ── حضانات نموذجية ────────────────────────────────────────────────────
        $nurseries = [
            [
                'name'                => 'حضانة نجوم المستقبل',
                'name_en'             => 'Future Stars Nursery',
                'slug'                => 'future-stars-'.Str::random(4),
                'type'                => Listing::TYPE_NURSERY,
                'service_category_id' => $nursDayCat?->id,
                'summary'             => 'بيئة تعليمية متكاملة وآمنة لأطفالك من سن 6 أشهر حتى 4 سنوات، مع تغذية صحية ومراقبة على مدار اليوم.',
                'summary_en'          => 'A safe, nurturing environment for children aged 6 months to 4 years.',
                'phone'               => '0221234567',
                'whatsapp'            => '201001112222',
                'address'             => 'الرحاب، المرحلة الثانية، بجوار مسجد الرحمة',
                'governorate'         => 'القاهرة',
                'is_active'           => true,
                'is_featured'         => true,
                'rating'              => 4.7,
                'data'                => [
                    'age_from_months'  => 6,
                    'age_to_months'    => 48,
                    'capacity'         => 80,
                    'monthly_fee_from' => 2500,
                    'monthly_fee_to'   => 4000,
                    'working_days'     => 'السبت – الخميس',
                    'working_hours'    => '7:30 ص – 5 م',
                    'programs'         => ['تعليم مبكّر', 'رياضيات تفاعلية', 'فنون وأشغال', 'لغة إنجليزية'],
                    'programs_en'      => ['Early Learning', 'Interactive Math', 'Arts & Crafts', 'English Language'],
                    'facilities'       => ['حمام سباحة آمن', 'ملعب خارجي', 'غرفة نوم', 'كافيتيريا صحية', 'كاميرات مراقبة', 'باص مجهّز'],
                ],
            ],
            [
                'name'                => 'روضة الأزهار المونتيسوري',
                'name_en'             => 'Al-Azhaar Montessori School',
                'slug'                => 'azhaar-montessori-'.Str::random(4),
                'type'                => Listing::TYPE_NURSERY,
                'service_category_id' => $nursMonteCat?->id,
                'summary'             => 'روضة مرخّصة تعتمد المنهج المونتيسوري لتنمية شخصية طفلك وثقته بنفسه من سن سنتين.',
                'phone'               => '0223456789',
                'whatsapp'            => '201003334444',
                'address'             => 'المقطم، القطعة 7',
                'governorate'         => 'القاهرة',
                'is_active'           => true,
                'is_featured'         => false,
                'rating'              => 4.5,
                'data'                => [
                    'age_from_months'  => 24,
                    'age_to_months'    => 72,
                    'capacity'         => 40,
                    'monthly_fee_from' => 3500,
                    'monthly_fee_to'   => 5500,
                    'working_days'     => 'الأحد – الخميس',
                    'working_hours'    => '8 ص – 2 م',
                    'programs'         => ['منهج مونتيسوري', 'قرآن كريم', 'برمجة أطفال', 'موسيقى'],
                    'programs_en'      => ['Montessori Curriculum', 'Quran', 'Kids Coding', 'Music'],
                    'facilities'       => ['فصول منتيسوري مجهّزة', 'مكتبة أطفال', 'نشاط رياضي', 'وجبات متوازنة'],
                ],
            ],
            [
                'name'                => 'حضانة براعم الإسكندرية',
                'name_en'             => 'Baraaim Alexandria Nursery',
                'slug'                => 'baraaim-alex-'.Str::random(4),
                'type'                => Listing::TYPE_NURSERY,
                'service_category_id' => $nursDayCat?->id,
                'summary'             => 'حضانة مجهّزة بأحدث الوسائل التعليمية في قلب الإسكندرية — رعاية يومية متكاملة.',
                'phone'               => '034567890',
                'whatsapp'            => '201555666777',
                'address'             => 'ش فؤاد، سيدي جابر',
                'governorate'         => 'الإسكندرية',
                'is_active'           => true,
                'is_featured'         => false,
                'rating'              => 4.3,
                'data'                => [
                    'age_from_months'  => 3,
                    'age_to_months'    => 60,
                    'capacity'         => 60,
                    'monthly_fee_from' => 1800,
                    'monthly_fee_to'   => 3000,
                    'working_days'     => 'السبت – الخميس',
                    'working_hours'    => '7 ص – 4:30 م',
                    'programs'         => ['تعليم مبكّر', 'لغات', 'رياضة أطفال'],
                    'programs_en'      => ['Early Education', 'Languages', 'Kids Sports'],
                    'facilities'       => ['حديقة لعب', 'غرف مكيّفة', 'وجبات يومية', 'نقل مدرسي'],
                ],
            ],
        ];

        foreach ($nurseries as $nurs) {
            Listing::withoutGlobalScopes()->firstOrCreate(
                ['slug' => $nurs['slug']],
                $nurs
            );
        }

        $this->command->info('✅ DirectorySeeder: ' . count($doctors) . ' أطباء + ' . count($nurseries) . ' حضانات أُضيفوا');
    }
}
