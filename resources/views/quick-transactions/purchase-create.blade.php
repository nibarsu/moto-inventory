@include('quick-transactions._form', [
    'action' => route('quick-purchases.store'),
    'title' => '快速進貨單',
    'partyLabel' => '供應商名稱',
    'partyName' => 'supplier_name',
    'partyLookupUrl' => route('lookups.suppliers'),
    'productLookupUrl' => route('lookups.products'),
    'warehouses' => $warehouses,
    'submitLabel' => '建立進貨單',
    'defaultPriceField' => 'last_cost_price',
    'documentType' => 'PURCHASE RECEIPT',
])
