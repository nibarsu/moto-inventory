<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:30',
                'unique:brands,code',
            ],

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'english_name' => [
                'nullable',
                'string',
                'max:100',
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
            'code.required' => '請輸入品牌代碼',
            'code.unique' => '品牌代碼已存在',
            'name.required' => '請輸入品牌名稱',
        ];
    }
}