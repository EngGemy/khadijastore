<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToBrand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Per-brand Facebook Pixel + Conversions API credentials and event toggles.
 */
class FacebookPixelSetting extends Model
{
    use BelongsToBrand;

    protected $fillable = [
        'brand_id',
        'pixel_id',
        'access_token',
        'test_event_code',
        'is_enabled',
        'track_pageview',
        'track_viewcontent',
        'track_addtocart',
        'track_initiatecheckout',
        'track_purchase',
        'track_lead',
        'capi_enabled',
    ];

    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'is_enabled' => 'boolean',
            'track_pageview' => 'boolean',
            'track_viewcontent' => 'boolean',
            'track_addtocart' => 'boolean',
            'track_initiatecheckout' => 'boolean',
            'track_purchase' => 'boolean',
            'track_lead' => 'boolean',
            'capi_enabled' => 'boolean',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function isEventEnabled(string $eventName): bool
    {
        $column = config("facebook-pixel.event_toggles.{$eventName}");

        if (! $column) {
            return true;
        }

        return (bool) $this->{$column};
    }
}
