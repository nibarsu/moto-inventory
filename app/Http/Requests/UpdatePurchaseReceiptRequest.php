<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePurchaseReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receipt_no' => ['required', 'string', 'max:30', Rule::unique('purchase_receipts', 'receipt_no')->ignore($this->purchase_receipt)],
            'receipt_date' => ['required', 'date'],
            'remark' => ['nullable', 'string'],
        ];
    }
}
