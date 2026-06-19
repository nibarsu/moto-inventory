<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceivableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ar_no' => ['required', 'string', 'max:30', 'unique:accounts_receivable,ar_no'],
            'customer_id' => ['required', 'exists:customers,id'],
            'source_type' => ['nullable', 'string', 'max:30'],
            'source_id' => ['nullable', 'integer', 'min:1'],
            'ar_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:ar_date'],
            'total_amount' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'received_amount' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'remark' => ['nullable', 'string'],
        ];
    }
}
