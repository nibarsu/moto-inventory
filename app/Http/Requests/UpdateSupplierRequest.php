<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
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
                Rule::unique('suppliers', 'code')->ignore($this->supplier),
            ],
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'tax_id' => [
                'nullable',
                'string',
                'max:20',
            ],
            'contact_person' => [
                'nullable',
                'string',
                'max:50',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],
            'mobile' => [
                'nullable',
                'string',
                'max:30',
            ],
            'email' => [
                'nullable',
                'email',
                'max:100',
            ],
            'address' => [
                'nullable',
                'string',
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
            'code.required' => '請輸入供應商代碼。',
            'code.unique' => '供應商代碼已存在。',
            'name.required' => '請輸入供應商名稱。',
            'email.email' => '請輸入有效的 Email。',
        ];
    }
}
