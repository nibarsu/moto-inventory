<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
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
            'model_code' => [
                'required',
                'string',
                'max:50',
                'unique:vehicles,model_code',
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
                Rule::exists('categories', 'id')->where('type', 'vehicle'),
            ],
            'year' => [
                'nullable',
                'integer',
                'min:1900',
                'max:2100',
            ],
            'color' => [
                'nullable',
                'string',
                'max:50',
            ],
            'engine_displacement' => [
                'nullable',
                'string',
                'max:50',
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
            'model_code.required' => '請輸入車型代碼。',
            'model_code.unique' => '車型代碼已存在。',
            'name.required' => '請輸入車名。',
            'brand_id.exists' => '選擇的品牌不存在。',
            'category_id.exists' => '選擇的分類不存在。',
        ];
    }
}
