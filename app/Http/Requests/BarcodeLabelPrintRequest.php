<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarcodeLabelPrintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.type' => ['required', 'in:part,vehicle'],
            'items.*.id' => ['required', 'integer', 'min:1'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => '請至少選擇一筆商品進行列印。',
            'items.min' => '請至少選擇一筆商品進行列印。',
            'items.*.quantity.min' => '列印張數至少要 1 張。',
            'items.*.quantity.max' => '單一商品一次最多列印 50 張。',
        ];
    }
}
