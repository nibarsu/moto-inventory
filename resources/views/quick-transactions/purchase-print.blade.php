<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <title>快速進貨單 {{ $purchaseReceipt->receipt_no }}</title>
    <style>
        @page { size: A4 portrait; margin: 12mm; }
        :root {
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #9ca3af;
            --soft: #e5e7eb;
            --panel: #f9fafb;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: var(--ink);
            font-family: Arial, "Microsoft JhengHei", sans-serif;
            background: #f3f4f6;
        }
        .print-actions {
            max-width: 210mm;
            margin: 16px auto 0;
            text-align: right;
        }
        .print-actions button {
            border: 1px solid #111827;
            background: #111827;
            color: #fff;
            border-radius: 6px;
            padding: 8px 14px;
            font-size: 13px;
            cursor: pointer;
        }
        .sheet {
            width: 186mm;
            min-height: 262mm;
            margin: 8px auto 16px;
            padding: 8mm 9mm 10mm;
            background: #fff;
            border: 1px solid var(--soft);
        }
        .company-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--ink);
        }
        .company-name {
            font-size: 30px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .doc-name {
            font-size: 18px;
            font-weight: 700;
        }
        .subhead {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }
        .meta-table,
        .items-table,
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td,
        .items-table th,
        .items-table td,
        .summary-table td {
            border: 1px solid var(--line);
            padding: 6px 8px;
            font-size: 13px;
        }
        .meta-table .label,
        .summary-table .label {
            width: 64px;
            color: var(--muted);
            background: var(--panel);
            white-space: nowrap;
        }
        .items-wrap {
            margin-top: 10px;
        }
        .items-table th {
            background: var(--panel);
            font-weight: 700;
            text-align: center;
        }
        .items-table td {
            height: 32px;
            vertical-align: middle;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .notes-row {
            display: grid;
            grid-template-columns: 1fr 210px;
            gap: 10px;
            margin-top: 10px;
            align-items: start;
        }
        .notes-box {
            min-height: 88px;
            border: 1px solid var(--line);
            padding: 8px 10px;
            font-size: 13px;
            line-height: 1.7;
        }
        .summary-table td {
            height: 34px;
        }
        .summary-table .amount {
            width: 110px;
            text-align: right;
            font-weight: 700;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 18px;
            margin-top: 26px;
            font-size: 13px;
        }
        .signatures .line {
            padding-top: 20px;
            border-top: 1px solid var(--ink);
            text-align: center;
        }
        .memo {
            margin-top: 16px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.6;
        }
        @media print {
            body { background: #fff; }
            .print-actions { display: none; }
            .sheet {
                width: auto;
                min-height: auto;
                margin: 0;
                border: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    @php
        $rows = $purchaseReceipt->items->values();
        $minRows = max(8, $rows->count());
    @endphp

    <div class="print-actions">
        <button type="button" onclick="window.print()">列印</button>
    </div>

    <div class="sheet">
        <div class="company-row">
            <div>
                <div class="company-name">{{ config('app.name', 'Moto Inventory') }}</div>
                <div style="margin-top: 2px; color: var(--muted); font-size: 12px;">快速單據列印版</div>
            </div>
            <div class="doc-name">進貨單</div>
        </div>

        <div class="subhead">
            <table class="meta-table">
                <tr>
                    <td class="label">供應商</td>
                    <td>{{ $purchaseReceipt->supplier?->name ?: '-' }}</td>
                    <td class="label">倉庫</td>
                    <td>{{ $purchaseReceipt->warehouse?->name ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="label">地址</td>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td class="label">備註</td>
                    <td colspan="3">{{ $purchaseReceipt->remark ?: ' ' }}</td>
                </tr>
            </table>

            <table class="meta-table">
                <tr>
                    <td class="label">單號</td>
                    <td>{{ $purchaseReceipt->receipt_no }}</td>
                </tr>
                <tr>
                    <td class="label">日期</td>
                    <td>{{ optional($purchaseReceipt->receipt_date)->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <td class="label">建立者</td>
                    <td>{{ $purchaseReceipt->creator?->name ?: '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="items-wrap">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 48px;">項次</th>
                        <th style="width: 110px;">料號</th>
                        <th>商品名稱</th>
                        <th style="width: 70px;">數量</th>
                        <th style="width: 86px;">單價</th>
                        <th style="width: 96px;">金額</th>
                        <th style="width: 100px;">備註</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < $minRows; $i++)
                        @php $item = $rows->get($i); @endphp
                        <tr>
                            <td class="center">{{ $item ? $i + 1 : '' }}</td>
                            <td>{{ $item?->item_code ?: '' }}</td>
                            <td>{{ $item?->item_name ?: '' }}</td>
                            <td class="right">{{ $item ? number_format($item->quantity) : '' }}</td>
                            <td class="right">{{ $item ? number_format((float) $item->unit_cost, 2) : '' }}</td>
                            <td class="right">{{ $item ? number_format((float) $item->line_total, 2) : '' }}</td>
                            <td>{{ $item?->remark ?: '' }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <div class="notes-row">
            <div class="notes-box">
                <div>附記：</div>
                <div>1. 本單據作為進貨收貨與對帳依據。</div>
                <div>2. 如品項、數量或金額有疑義，請於收單後儘速確認。</div>
            </div>

            <table class="summary-table">
                <tr>
                    <td class="label">小計</td>
                    <td class="amount">{{ number_format((float) $purchaseReceipt->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">折讓</td>
                    <td class="amount">0.00</td>
                </tr>
                <tr>
                    <td class="label">總計</td>
                    <td class="amount">{{ number_format((float) $purchaseReceipt->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="signatures">
            <div class="line">供應商簽章</div>
            <div class="line">收貨人簽章</div>
            <div class="line">經手人簽章</div>
        </div>

        <div class="memo">
            本列印版為 A4 傳統單據格式，可直接使用瀏覽器列印。
        </div>
    </div>
</body>
</html>
