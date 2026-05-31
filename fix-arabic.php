<?php

/**
 * إصلاح النص العربي المكسور (mojibake) في ملفات Filament.
 * النص اتحوّل غلط من UTF-8 -> Windows-1252، وده بيعكسه.
 *
 * التشغيل من جذر المشروع:
 *   php fix-arabic.php
 */

$dirs = [
    __DIR__ . '/app/Filament',
];

/**
 * يعكس الـ mojibake على مقاطع النص العربي المكسور فقط،
 * ويترك الكود الإنجليزي (ASCII) سليمًا.
 */
function fixMojibake(string $text): string
{
    // اجمع مقاطع الأحرف غير-ASCII المتتالية وحوّلها
    return preg_replace_callback('/[\x{0080}-\x{00FF}\x{0152}\x{0153}\x{0160}\x{0161}\x{0178}\x{017D}\x{017E}\x{0192}\x{02C6}\x{02DC}\x{2013}\x{2014}\x{2018}-\x{201E}\x{2020}-\x{2022}\x{2026}\x{2030}\x{2039}\x{203A}\x{20AC}\x{2122}]+/u', function ($m) {
        $seg = $m[0];
        // حوّل المقطع من تمثيله الحالي إلى البايتات الأصلية ثم فسّرها UTF-8
        $bytes = @mb_convert_encoding($seg, 'Windows-1252', 'UTF-8');
        if ($bytes === false) {
            return $seg;
        }
        // تحقّق أن الناتج UTF-8 عربي صالح
        if (mb_check_encoding($bytes, 'UTF-8') && preg_match('/\p{Arabic}/u', $bytes)) {
            return $bytes;
        }

        return $seg;
    }, $text);
}

$count = 0;
foreach ($dirs as $dir) {
    if (! is_dir($dir)) {
        continue;
    }
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($rii as $file) {
        if ($file->isDir() || $file->getExtension() !== 'php') {
            continue;
        }
        $path = $file->getPathname();
        $original = file_get_contents($path);

        // شيل أي BOM
        $clean = preg_replace('/^\x{FEFF}/u', '', $original);
        $clean = ltrim($clean, "\xEF\xBB\xBF");

        $fixed = fixMojibake($clean);

        if ($fixed !== $original) {
            file_put_contents($path, $fixed);
            echo "Fixed: " . str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $path) . PHP_EOL;
            $count++;
        }
    }
}

echo PHP_EOL . "Done. {$count} file(s) fixed." . PHP_EOL;
