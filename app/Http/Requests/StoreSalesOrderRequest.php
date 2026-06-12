<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'so_no' => ['required', 'string', 'max:30', 'unique:sales_orders,so_no'],
            'order_date' => ['required', 'date'],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'customer_id' => ['required', 'exists:customers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'status' => ['required', 'in:draft,confirmed,completed,cancelled'],
            'total_amount' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'remark' => ['nullable', 'string'],
        ];
    }
}
