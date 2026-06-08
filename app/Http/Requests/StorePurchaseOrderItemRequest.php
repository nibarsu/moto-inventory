<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_type' => ['required', 'in:part,vehicle'],
            'item_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'remark' => ['nullable', 'string'],
        ];
    }
}
