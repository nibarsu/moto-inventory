<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarcodeLabelIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['nullable', 'in:all,part,vehicle'],
            'keyword' => ['nullable', 'string', 'max:100'],
        ];
    }
}
