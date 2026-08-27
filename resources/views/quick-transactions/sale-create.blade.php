@include('quick-transactions._form', [
    'action' => route('quick-sales.store'),
    'title' => '快速出貨單',
    'partyLabel' => '客戶名稱',
    'partyName' => 'customer_name',
    'partyLookupUrl' => route('lookups.customers'),
    'productLookupUrl' => route('lookups.products'),
    'warehouses' => $warehouses,
    'submitLabel' => '建立出貨單',
    'defaultPriceField' => 'sale_price',
])
