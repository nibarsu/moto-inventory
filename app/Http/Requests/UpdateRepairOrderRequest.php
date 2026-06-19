<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRepairOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'wo_no' => ['required', 'string', 'max:30', Rule::unique('repair_orders', 'wo_no')->ignore($this->repair_order)],
            'order_date' => ['required', 'date'],
            'customer_id' => ['required', 'exists:customers,id'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'plate_no' => ['nullable', 'string', 'max:20'],
            'mileage' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:open,in_progress,completed,cancelled'],
            'complaint' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'remark' => ['nullable', 'string'],
        ];
    }
}
