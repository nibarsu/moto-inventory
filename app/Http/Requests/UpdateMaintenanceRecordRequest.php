<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMaintenanceRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'record_no' => ['required', 'string', 'max:30', Rule::unique('maintenance_records', 'record_no')->ignore($this->maintenance_record)],
            'service_date' => ['required', 'date'],
            'customer_id' => ['required', 'exists:customers,id'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'repair_order_id' => ['nullable', 'exists:repair_orders,id'],
            'plate_no' => ['nullable', 'string', 'max:20'],
            'mileage' => ['nullable', 'integer', 'min:0'],
            'service_type' => ['required', 'string', 'max:50'],
            'next_service_date' => ['nullable', 'date', 'after_or_equal:service_date'],
            'next_service_mileage' => ['nullable', 'integer', 'min:0'],
            'service_content' => ['nullable', 'string'],
            'remark' => ['nullable', 'string'],
        ];
    }
}
