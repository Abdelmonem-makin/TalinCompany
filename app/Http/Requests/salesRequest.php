<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class salesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            [
            // 'customer_id'=>'required',
            'Bank' => 'nullable|numeric',
            'products' => 'required|array|min:1',
            'products.*.quantity' => 'required|integer|min:1',
        ],[
                'products.required' => 'لا توجد منتجات في الطلب',
                'products.*.quantity.required' => 'الكمية مطلوبة لكل منتج',
            ]
        ];
    }
}
