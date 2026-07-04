<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrackFacebookPixelEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_name' => ['required', 'string', Rule::in([
                'AddToCart', 'InitiateCheckout', 'Lead',
            ])],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'custom_data' => ['nullable', 'array'],
            'user_data' => ['nullable', 'array'],
        ];
    }
}
