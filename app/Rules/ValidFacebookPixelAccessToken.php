<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\FacebookPixelService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates that a Meta access token can access the given Pixel ID via Graph API.
 */
class ValidFacebookPixelAccessToken implements ValidationRule
{
    public function __construct(
        private readonly ?string $pixelId,
        private readonly bool $skipIfUnchanged = false,
        private readonly ?string $existingToken = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->skipIfUnchanged && $this->existingToken && $value === '********') {
            return;
        }

        if (! is_string($value) || $value === '') {
            $fail('رمز الوصول مطلوب.');

            return;
        }

        if (! $this->pixelId) {
            $fail('معرّف البكسل مطلوب للتحقق من رمز الوصول.');

            return;
        }

        $service = app(FacebookPixelService::class);

        if (! $service->validateAccessToken($this->pixelId, $value)) {
            $fail('رمز الوصول غير صالح أو لا يملك صلاحية الوصول إلى هذا البكسل.');
        }
    }
}
