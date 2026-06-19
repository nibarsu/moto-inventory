<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_type' => ['required', 'in:part,vehicle'],
            'import_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_type.required' => '請選擇匯入商品類型。',
            'item_type.in' => '匯入商品類型不正確。',
            'import_file.required' => '請選擇要匯入的 CSV 檔案。',
            'import_file.mimes' => '匯入檔案必須是 CSV 或 TXT 格式。',
            'import_file.max' => '匯入檔案大小不可超過 2MB。',
        ];
    }
}
