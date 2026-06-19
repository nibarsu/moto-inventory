<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesReportIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
            'item_type' => ['nullable', 'in:all,part,vehicle'],
            'keyword' => ['nullable', 'string', 'max:100'],
        ];
    }
}
