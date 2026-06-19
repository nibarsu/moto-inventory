<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayableRequest;
use App\Http\Requests\UpdatePayableRequest;
use App\Models\Payable;
use App\Models\Supplier;

class PayableController extends Controller
{
    public function index()
    {
        $payables = Payable::with('supplier')
            ->orderByDesc('ap_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('accounts-payable.index', compact('payables'));
    }

    public function create()
    {
        return view('accounts-payable.create', [
            'suppliers' => $this->suppliers(),
            'defaultApNo' => $this->generateApNo(),
        ]);
    }

    public function store(StorePayableRequest $request)
    {
        Payable::create($this->payload($request->validated()) + [
            'created_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('accounts-payable.index')
            ->with('success', '應付帳款已建立。');
    }

    public function show(Payable $accountsPayable)
    {
        $accountsPayable->load(['supplier', 'creator']);

        return view('accounts-payable.show', [
            'payable' => $accountsPayable,
            'statusLabels' => $this->statusLabels(),
        ]);
    }

    public function edit(Payable $accountsPayable)
    {
        return view('accounts-payable.edit', [
            'payable' => $accountsPayable,
            'suppliers' => $this->suppliers(),
        ]);
    }

    public function update(UpdatePayableRequest $request, Payable $accountsPayable)
    {
        $accountsPayable->update($this->payload($request->validated()));

        return redirect()
            ->route('accounts-payable.index')
            ->with('success', '應付帳款已更新。');
    }

    public function destroy(Payable $accountsPayable)
    {
        $accountsPayable->delete();

        return redirect()
            ->route('accounts-payable.index')
            ->with('success', '應付帳款已刪除。');
    }

    private function payload(array $validated): array
    {
        $totalAmount = round((float) $validated['total_amount'], 2);
        $paidAmount = round((float) $validated['paid_amount'], 2);
        $balanceAmount = round(max(0, $totalAmount - $paidAmount), 2);

        return $validated + [
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'balance_amount' => $balanceAmount,
            'status' => $this->resolveStatus($totalAmount, $paidAmount),
        ];
    }

    private function resolveStatus(float $totalAmount, float $paidAmount): string
    {
        if ($totalAmount <= 0) {
            return 'void';
        }

        if ($paidAmount <= 0) {
            return 'open';
        }

        if ($paidAmount >= $totalAmount) {
            return 'paid';
        }

        return 'partial';
    }

    private function suppliers()
    {
        return Supplier::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function generateApNo(): string
    {
        $prefix = 'AP-'.now()->format('Ymd');
        $count = Payable::where('ap_no', 'like', $prefix.'-%')->count() + 1;

        return sprintf('%s-%03d', $prefix, $count);
    }

    private function statusLabels(): array
    {
        return [
            'open' => '未付款',
            'partial' => '部分付款',
            'paid' => '已付款',
            'void' => '作廢',
        ];
    }
}
