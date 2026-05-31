<?php

namespace App\Console\Commands;

use App\Services\ThemeResolver;
use Illuminate\Console\Command;

/**
 * يحدّث كاش الثيم — يُجدول كل ساعة ليلتقط الثيمات المجدولة
 * (مثل ثيم العيد) فور دخول/خروج نافذتها الزمنية.
 */
class RefreshThemeCache extends Command
{
    protected $signature = 'themes:refresh';

    protected $description = 'تحديث كاش الثيم لالتقاط الثيمات المجدولة';

    public function handle(ThemeResolver $resolver): int
    {
        $resolver->clearCache();
        $this->info('تم تحديث كاش الثيم.');

        return self::SUCCESS;
    }
}
