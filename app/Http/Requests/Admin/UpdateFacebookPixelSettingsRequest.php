<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\FacebookPixelSetting;
use App\Rules\ValidFacebookPixelAccessToken;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFacebookPixelSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && ($user->isSuperAdmin() || $user->hasRole('brand_admin'));
    }

    public function rules(): array
    {
        $brandId = $this->resolveBrandId();
        $existing = FacebookPixelSetting::query()->where('brand_id', $brandId)->first();
        $maskToken = $existing?->access_token ? '********' : null;

        return [
            'brand_id' => [
                Rule::requiredIf(fn () => $this->user()?->isSuperAdmin()),
                'nullable',
                'integer',
                'exists:brands,id',
            ],
            'pixel_id' => ['required', 'string', 'regex:/^\d{10,20}$/'],
            'access_token' => [
                'required',
                'string',
                'min:20',
                new ValidFacebookPixelAccessToken(
                    pixelId: $this->input('pixel_id'),
                    skipIfUnchanged: (bool) $existing,
                    existingToken: $maskToken,
                ),
            ],
            'test_event_code' => ['nullable', 'string', 'max:64'],
            'is_enabled' => ['sometimes', 'boolean'],
            'capi_enabled' => ['sometimes', 'boolean'],
            'track_pageview' => ['sometimes', 'boolean'],
            'track_viewcontent' => ['sometimes', 'boolean'],
            'track_addtocart' => ['sometimes', 'boolean'],
            'track_initiatecheckout' => ['sometimes', 'boolean'],
            'track_purchase' => ['sometimes', 'boolean'],
            'track_lead' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'pixel_id.regex' => 'معرّف البكسل يجب أن يكون رقمًا من 10–20 خانة.',
            'access_token.required' => 'رمز الوصول (Access Token) مطلوب.',
        ];
    }

    public function resolveBrandId(): int
    {
        if ($this->user()?->isSuperAdmin() && $this->filled('brand_id')) {
            return (int) $this->input('brand_id');
        }

        return (int) $this->user()->brand_id;
    }

    /**
     * @return array<string, mixed>
     */
    public function settingsPayload(): array
    {
        return $this->only([
            'pixel_id',
            'access_token',
            'test_event_code',
            'is_enabled',
            'capi_enabled',
            'track_pageview',
            'track_viewcontent',
            'track_addtocart',
            'track_initiatecheckout',
            'track_purchase',
            'track_lead',
        ]);
    }
}
