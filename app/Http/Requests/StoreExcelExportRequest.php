<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExcelExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'export_type' => ['required', 'in:brands,categories,parts,vehicles,customers,suppliers,warehouses,inventory_report,purchase_report,sales_report'],
            'type' => ['nullable', 'in:all,part,vehicle'],
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'item_type' => ['nullable', 'in:all,part,vehicle'],
            'is_active' => ['nullable', 'in:all,1,0'],
            'keyword' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
