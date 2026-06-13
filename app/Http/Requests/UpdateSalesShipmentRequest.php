<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSalesShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipment_no' => ['required', 'string', 'max:30', Rule::unique('sales_shipments', 'shipment_no')->ignore($this->sales_shipment)],
            'shipment_date' => ['required', 'date'],
            'remark' => ['nullable', 'string'],
        ];
    }
}
