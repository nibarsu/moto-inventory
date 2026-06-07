<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'code' => [
                'required',
                'string',
                'max:30',
                'unique:categories,code',
            ],
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'type' => [
                'required',
                'in:part,vehicle',
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
            'code.required' => '請輸入分類代碼。',
            'code.unique' => '分類代碼已存在。',
            'name.required' => '請輸入分類名稱。',
            'type.required' => '請選擇分類類型。',
            'type.in' => '分類類型必須是零件或整車。',
        ];
    }
}
