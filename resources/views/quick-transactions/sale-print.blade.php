<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <title>快速出貨單 {{ $salesShipment->shipment_no }}</title>
    <style>
        body { font-family: Arial, "Microsoft JhengHei", sans-serif; margin: 24px; color: #111827; }
        .sheet { max-width: 794px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
        .title { font-size: 28px; font-weight: 700; }
        .meta { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .meta td { padding: 6px 8px; border: 1px solid #d1d5db; font-size: 14px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #d1d5db; padding: 8px; font-size: 14px; }
        .table th { background: #f3f4f6; text-align: left; }
        .text-right { text-align: right; }
        .total-row td { font-weight: 700; }
        .footer { margin-top: 48px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        .sign { border-top: 1px solid #111827; padding-top: 8px; font-size: 14px; }
        @media print {
            body { margin: 0; }
            .print-actions { display: none; }
            .sheet { padding: 16mm; max-width: none; }
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="print-actions" style="margin-bottom: 16px;">
            <button onclick="window.print()">列印</button>
        </div>

        <div class="header">
            <div>
                <div class="title">出貨單</div>
                <div>單號：{{ $salesShipment->shipment_no }}</div>
            </div>
            <div>日期：{{ optional($salesShipment->shipment_date)->format('Y-m-d') }}</div>
        </div>

        <table class="meta">
            <tr>
                <td width="15%">客戶</td>
                <td width="35%">{{ $salesShipment->customer?->name }}</td>
                <td width="15%">倉庫</td>
                <td width="35%">{{ $salesShipment->warehouse?->name }}</td>
            </tr>
            <tr>
                <td>備註</td>
                <td colspan="3">{{ $salesShipment->remark ?: '-' }}</td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>商品名稱</th>
                    <th>料號</th>
                    <th class="text-right">數量</th>
                    <th class="text-right">單價</th>
                    <th class="text-right">金額</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($salesShipment->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->item_code ?: '-' }}</td>
                        <td class="text-right">{{ number_format($item->quantity) }}</td>
                        <td class="text-right">{{ number_format((float) $item->unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format((float) $item->line_total, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" class="text-right">合計</td>
                    <td class="text-right">{{ number_format((float) $salesShipment->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <div class="sign">客戶簽收</div>
            <div class="sign">經手人簽章</div>
        </div>
    </div>
</body>
</html>
