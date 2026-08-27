<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuickSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_date' => ['required', 'date'],
            'customer_name' => ['required', 'string', 'max:100'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'remark' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['nullable', 'string', 'max:150'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'items.*.remark' => ['nullable', 'string'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $rows = collect($this->input('items', []))
                    ->map(fn (array $item) => [
                        'name' => trim((string) ($item['name'] ?? '')),
                        'quantity' => $item['quantity'] ?? null,
                        'unit_price' => $item['unit_price'] ?? null,
                    ]);

                $filledRows = $rows->filter(fn (array $item) => $item['name'] !== '' || $item['quantity'] !== null || $item['unit_price'] !== null);

                if ($filledRows->isEmpty()) {
                    $validator->errors()->add('items', '請至少輸入一筆商品明細。');

                    return;
                }

                foreach ($filledRows as $index => $item) {
                    if ($item['name'] === '') {
                        $validator->errors()->add("items.$index.name", '商品名稱不可空白。');
                    }

                    if ($item['quantity'] === null || $item['quantity'] === '') {
                        $validator->errors()->add("items.$index.quantity", '請輸入數量。');
                    }

                    if ($item['unit_price'] === null || $item['unit_price'] === '') {
                        $validator->errors()->add("items.$index.unit_price", '請輸入單價。');
                    }
                }
            },
        ];
    }
}
