<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
            'customer_name' => ['required', 'string', 'max:120'],
            // رقم موبايل مصري
            'customer_phone' => ['required', 'regex:/^01[0-9]{9}$/'],
            'governorate' => ['required', 'string', 'exists:governorates,name'],
            'address' => ['required', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:cod,whatsapp,transfer'],
            // إيصال التحويل مطلوب فقط عند اختيار "transfer"
            'receipt' => ['nullable', 'required_if:payment_method,transfer', 'image', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_phone.regex' => 'رقم الموبايل غير صحيح (يجب أن يبدأ بـ 01 و11 رقمًا).',
            'receipt.required_if' => 'صورة الإيصال مطلوبة عند الدفع بالتحويل.',
            'governorate.exists' => 'المحافظة غير صحيحة.',
        ];
    }
}
