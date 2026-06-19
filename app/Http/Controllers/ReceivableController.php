<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReceivableRequest;
use App\Http\Requests\UpdateReceivableRequest;
use App\Models\Customer;
use App\Models\Receivable;

class ReceivableController extends Controller
{
    public function index()
    {
        $receivables = Receivable::with('customer')
            ->orderByDesc('ar_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('accounts-receivable.index', compact('receivables'));
    }

    public function create()
    {
        return view('accounts-receivable.create', [
            'customers' => $this->customers(),
            'defaultArNo' => $this->generateArNo(),
        ]);
    }

    public function store(StoreReceivableRequest $request)
    {
        Receivable::create($this->payload($request->validated()) + [
            'created_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('accounts-receivable.index')
            ->with('success', '應收帳款已建立。');
    }

    public function show(Receivable $accountsReceivable)
    {
        $accountsReceivable->load(['customer', 'creator']);

        return view('accounts-receivable.show', [
            'receivable' => $accountsReceivable,
            'statusLabels' => $this->statusLabels(),
        ]);
    }

    public function edit(Receivable $accountsReceivable)
    {
        return view('accounts-receivable.edit', [
            'receivable' => $accountsReceivable,
            'customers' => $this->customers(),
        ]);
    }

    public function update(UpdateReceivableRequest $request, Receivable $accountsReceivable)
    {
        $accountsReceivable->update($this->payload($request->validated()));

        return redirect()
            ->route('accounts-receivable.index')
            ->with('success', '應收帳款已更新。');
    }

    public function destroy(Receivable $accountsReceivable)
    {
        $accountsReceivable->delete();

        return redirect()
            ->route('accounts-receivable.index')
            ->with('success', '應收帳款已刪除。');
    }

    private function payload(array $validated): array
    {
        $totalAmount = round((float) $validated['total_amount'], 2);
        $receivedAmount = round((float) $validated['received_amount'], 2);
        $balanceAmount = round(max(0, $totalAmount - $receivedAmount), 2);

        return $validated + [
            'total_amount' => $totalAmount,
            'received_amount' => $receivedAmount,
            'balance_amount' => $balanceAmount,
            'status' => $this->resolveStatus($totalAmount, $receivedAmount),
        ];
    }

    private function resolveStatus(float $totalAmount, float $receivedAmount): string
    {
        if ($totalAmount <= 0) {
            return 'void';
        }

        if ($receivedAmount <= 0) {
            return 'open';
        }

        if ($receivedAmount >= $totalAmount) {
            return 'paid';
        }

        return 'partial';
    }

    private function customers()
    {
        return Customer::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function generateArNo(): string
    {
        $prefix = 'AR-'.now()->format('Ymd');
        $count = Receivable::where('ar_no', 'like', $prefix.'-%')->count() + 1;

        return sprintf('%s-%03d', $prefix, $count);
    }

    private function statusLabels(): array
    {
        return [
            'open' => '未收款',
            'partial' => '部分收款',
            'paid' => '已收款',
            'void' => '作廢',
        ];
    }
}
