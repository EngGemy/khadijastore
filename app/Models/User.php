<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable, FilamentUser
{
    use AuditableTrait, HasRoles, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'brand_id', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // لا نوثّق تغيّر كلمة المرور في الـ audit
    protected $auditExclude = ['password', 'remember_token'];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'platform' => $this->isSuperAdmin(),
            'merchant' => $this->hasAnyRole(['brand_admin', 'brand_staff']),
            default => false,
        };
    }

    public function panelLoginUrl(): string
    {
        return $this->isSuperAdmin()
            ? url('/platform/login')
            : url('/merchant/login');
    }

    public function panelOrderUrl(int $orderId): string
    {
        $prefix = $this->isSuperAdmin() ? '/platform' : '/merchant';

        return url($prefix.'/orders/'.$orderId);
    }
}
