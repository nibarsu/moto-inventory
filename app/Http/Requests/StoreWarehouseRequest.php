<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
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
                'unique:warehouses,code',
            ],
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'address' => [
                'nullable',
                'string',
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
            'code.required' => '請輸入倉庫代碼。',
            'code.unique' => '倉庫代碼已存在。',
            'name.required' => '請輸入倉庫名稱。',
        ];
    }
}
