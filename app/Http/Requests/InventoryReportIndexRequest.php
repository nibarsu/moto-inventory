<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryReportIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['nullable', 'in:all,part,vehicle'],
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
            'is_active' => ['nullable', 'in:all,1,0'],
            'keyword' => ['nullable', 'string', 'max:100'],
        ];
    }
}
