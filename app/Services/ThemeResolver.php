<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Theme;
use Illuminate\Support\Facades\Cache;

/**
 * يحدّد الثيم الفعّال ويحوّله إلى متغيرات CSS.
 * الأولوية: ثيم البراند الفعّال > ثيم عام فعّال > الافتراضي.
 * الثيم الأعلى priority يفوز عند التساوي.
 *
 * الواجهة الأمامية (Tailwind الحالي) لا تتغير — فقط تُحقن
 * قيم متغيرات CSS (--ink, --accent, ...) في وسم <style>.
 */
class ThemeResolver
{
    public function resolve(?Brand $brand = null): array
    {
        $cacheKey = 'theme.active.'.($brand?->id ?? 'global');

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($brand) {
            $tokens = Theme::defaultTokens();

            // 1) ثيم عام فعّال
            $global = Theme::query()
                ->currentlyActive()
                ->where('scope', 'global')
                ->orderByDesc('priority')
                ->first();

            if ($global) {
                $tokens = array_merge($tokens, $global->tokens ?? []);
            }

            // 2) ثيم البراند يطغى على العام
            if ($brand) {
                $brandTheme = Theme::query()
                    ->currentlyActive()
                    ->where('scope', 'brand')
                    ->where('brand_id', $brand->id)
                    ->orderByDesc('priority')
                    ->first();

                if ($brandTheme) {
                    $tokens = array_merge($tokens, $brandTheme->tokens ?? []);
                }
            }

            return $tokens;
        });
    }

    /** يحوّل التوكنات إلى نص متغيرات CSS جاهز للحقن في :root */
    public function toCssVariables(array $tokens): string
    {
        $map = [
            'ink' => ['--ink', '--navy'],
            'paper' => ['--paper'],
            'paper2' => ['--paper2', '--paper-2'],
            'accent' => ['--accent', '--orange'],
            'accentDark' => ['--accentDark'],
            'brand' => ['--brand', '--orange-bright'],
        ];

        $lines = [];
        foreach ($map as $token => $cssVars) {
            if (empty($tokens[$token])) {
                continue;
            }
            foreach ($cssVars as $cssVar) {
                $lines[] = "{$cssVar}:{$tokens[$token]}";
            }
        }

        return ':root{'.implode(';', $lines).'}';
    }

    public function clearCache(): void
    {
        Cache::forget('theme.active.global');
        Brand::pluck('id')->each(fn ($id) => Cache::forget("theme.active.{$id}"));
    }
}
