<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePayableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ap_no' => ['required', 'string', 'max:30', 'unique:accounts_payable,ap_no'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'source_type' => ['nullable', 'string', 'max:30'],
            'source_id' => ['nullable', 'integer', 'min:1'],
            'ap_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:ap_date'],
            'total_amount' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'paid_amount' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'remark' => ['nullable', 'string'],
        ];
    }
}
