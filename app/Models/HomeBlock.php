<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class HomeBlock extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = ['type', 'title', 'subtitle', 'is_active', 'sort', 'data'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'data' => 'array',
            'sort' => 'integer',
        ];
    }

    public static function typeLabels(): array
    {
        return [
            'categories' => 'فئات (Cards)',
            'banner' => 'بانر نصي (CTA)',
            'rich_text' => 'نص حر (HTML)',
            'products_grid' => 'شبكة منتجات',
            'image_cta' => 'بانر بصورة',
            'brands_marquee' => 'براندات متحركة (Marquee)',
            'brands_grid' => 'شبكة براندات',
            'brands_filter' => 'فلتر البراندات (للمنتجات)',
        ];
    }
}
