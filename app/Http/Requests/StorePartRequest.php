<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'part_no' => [
                'required',
                'string',
                'max:50',
                'unique:parts,part_no',
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:50',
            ],
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'brand_id' => [
                'nullable',
                'exists:brands,id',
            ],
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where('type', 'part'),
            ],
            'unit' => [
                'required',
                'string',
                'max:20',
            ],
            'last_cost_price' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],
            'sale_price' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],
            'safety_stock' => [
                'required',
                'integer',
                'min:0',
            ],
            'remark' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'part_no.required' => '請輸入料號。',
            'part_no.unique' => '料號已存在。',
            'name.required' => '請輸入商品名稱。',
            'brand_id.exists' => '選擇的品牌不存在。',
            'category_id.exists' => '選擇的分類不存在。',
        ];
    }
}
