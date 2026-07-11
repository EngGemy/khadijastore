<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBrand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Order extends Model implements Auditable
{
    use AuditableTrait, BelongsToBrand, SoftDeletes;

    protected $fillable = [
        'brand_id', 'order_no', 'customer_name', 'customer_phone',
        'governorate', 'address', 'notes', 'payment_method', 'receipt_path',
        'status', 'subtotal', 'shipping', 'total', 'handled_by', 'confirmed_at',
    ];

    protected function casts(): array
    {
        return ['confirmed_at' => 'datetime'];
    }

    public const STATUSES = [
        'pending' => 'قيد المراجعة',
        'confirmed' => 'مؤكد',
        'processing' => 'قيد التجهيز',
        'shipped' => 'تم الشحن',
        'delivered' => 'تم التسليم',
        'cancelled' => 'ملغي',
    ];

    public const PAYMENT_METHODS = [
        'cod' => 'الدفع عند الاستلام',
        'whatsapp' => 'واتساب',
        'transfer' => 'تحويل (فودافون/إنستاباي)',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->order_no ??= static::generateOrderNo();
        });
    }

    public static function generateOrderNo(): string
    {
        $year = now()->year;
        $seq = static::withoutGlobalScopes()->whereYear('created_at', $year)->count() + 1;

        return sprintf('ALM-%d-%05d', $year, $seq);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(OrderNote::class)->latest();
    }

    /** @var list<string> */
    public const STATUS_FLOW = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];

    /** Temporary note attached to the next status history entry. */
    public ?string $statusChangeNote = null;

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
