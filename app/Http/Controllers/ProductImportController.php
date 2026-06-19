<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductImportRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Part;
use App\Models\ProductImportLog;
use App\Models\Vehicle;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ProductImportController extends Controller
{
    public function index()
    {
        return view('product-imports.index', [
            'logs' => ProductImportLog::query()
                ->with('creator')
                ->latest()
                ->paginate(10),
            'partColumns' => $this->partColumns(),
            'vehicleColumns' => $this->vehicleColumns(),
        ]);
    }

    public function store(StoreProductImportRequest $request)
    {
        $itemType = $request->validated('item_type');
        $file = $request->file('import_file');

        try {
            $result = $this->processImport($itemType, $file->getRealPath());

            ProductImportLog::create([
                'item_type' => $itemType,
                'original_filename' => $file->getClientOriginalName(),
                'total_rows' => $result['total_rows'],
                'created_count' => $result['created_count'],
                'updated_count' => $result['updated_count'],
                'skipped_count' => $result['skipped_count'],
                'status' => 'completed',
                'summary' => [
                    'headers' => $result['headers'],
                    'errors' => $result['errors'],
                ],
                'created_by' => Auth::id(),
            ]);
        } catch (Throwable $exception) {
            ProductImportLog::create([
                'item_type' => $itemType,
                'original_filename' => $file->getClientOriginalName(),
                'status' => 'failed',
                'summary' => [
                    'headers' => [],
                    'errors' => [$exception->getMessage()],
                ],
                'created_by' => Auth::id(),
            ]);

            return redirect()
                ->route('product-imports.index')
                ->with('error', '商品匯入失敗，請確認 CSV 格式後重新上傳。');
        }

        return redirect()
            ->route('product-imports.index')
            ->with('success', '商品匯入完成。');
    }

    public function show(ProductImportLog $productImport)
    {
        $productImport->load('creator');

        return view('product-imports.show', [
            'log' => $productImport,
        ]);
    }

    public function template(string $itemType): StreamedResponse
    {
        abort_unless(in_array($itemType, ['part', 'vehicle'], true), 404);

        $headers = $itemType === 'part' ? $this->partColumns() : $this->vehicleColumns();
        $sample = $itemType === 'part'
            ? ['P-001', 'P-001', '機油 10W40', 'YAMAHA', 'OIL', '罐', '180', '280', '10', '常用保養品', '1']
            : ['V-125-01', 'V-125-01', '勁豪 125', 'YAMAHA', 'SCOOTER125', '2026', '白色', '125cc', '52000', '61000', '展示車', '1'];

        $callback = static function () use ($headers, $sample): void {
            $stream = fopen('php://output', 'wb');
            fputcsv($stream, $headers);
            fputcsv($stream, $sample);
            fclose($stream);
        };

        return response()->streamDownload(
            $callback,
            $itemType.'-import-template.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    private function processImport(string $itemType, string $path): array
    {
        $rows = $this->readCsv($path);

        if (count($rows) < 2) {
            throw new RuntimeException('CSV 檔案內容不足，至少需要標題列與一筆資料。');
        }

        $headers = array_map([$this, 'normalizeHeader'], array_shift($rows));
        $expected = $itemType === 'part' ? $this->partColumns() : $this->vehicleColumns();
        $required = $itemType === 'part' ? ['part_no', 'name'] : ['model_code', 'name'];

        foreach ($required as $column) {
            if (! in_array($column, $headers, true)) {
                throw new RuntimeException('CSV 缺少必要欄位：'.$column);
            }
        }

        $rows = array_values(array_filter($rows, static fn (array $row) => count(array_filter($row, static fn ($value) => trim((string) $value) !== '')) > 0));

        $brandMap = Brand::query()->pluck('id', 'code')->mapWithKeys(fn ($id, $code) => [Str::upper(trim((string) $code)) => $id]);
        $categoryMap = Category::query()
            ->where('type', $itemType)
            ->pluck('id', 'code')
            ->mapWithKeys(fn ($id, $code) => [Str::upper(trim((string) $code)) => $id]);

        $summary = [
            'headers' => array_values(array_intersect($headers, $expected)),
            'total_rows' => count($rows),
            'created_count' => 0,
            'updated_count' => 0,
            'skipped_count' => 0,
            'errors' => [],
        ];

        DB::transaction(function () use ($rows, $headers, $itemType, $brandMap, $categoryMap, &$summary): void {
            foreach ($rows as $index => $row) {
                $line = $index + 2;
                $data = $this->rowToData($headers, $row);

                try {
                    if ($itemType === 'part') {
                        $action = $this->importPartRow($data, $brandMap->all(), $categoryMap->all());
                    } else {
                        $action = $this->importVehicleRow($data, $brandMap->all(), $categoryMap->all());
                    }

                    $summary[$action.'_count']++;
                } catch (RuntimeException $exception) {
                    $summary['skipped_count']++;
                    $summary['errors'][] = '第 '.$line.' 行：'.$exception->getMessage();
                }
            }
        });

        return $summary;
    }

    private function importPartRow(array $data, array $brandMap, array $categoryMap): string
    {
        $partNo = trim((string) ($data['part_no'] ?? ''));
        $name = trim((string) ($data['name'] ?? ''));

        if ($partNo === '' || $name === '') {
            throw new RuntimeException('part_no 與 name 為必填欄位。');
        }

        $attributes = [
            'barcode' => $this->nullableString($data['barcode'] ?? null, $partNo),
            'name' => $name,
            'brand_id' => $this->mapOptionalCode($data['brand_code'] ?? null, $brandMap, '品牌代碼'),
            'category_id' => $this->mapOptionalCode($data['category_code'] ?? null, $categoryMap, '分類代碼'),
            'unit' => $this->nullableString($data['unit'] ?? null, '個'),
            'last_cost_price' => $this->decimalValue($data['last_cost_price'] ?? null),
            'sale_price' => $this->decimalValue($data['sale_price'] ?? null),
            'safety_stock' => $this->integerValue($data['safety_stock'] ?? null),
            'remark' => $this->nullableString($data['remark'] ?? null),
            'is_active' => $this->booleanValue($data['is_active'] ?? null),
        ];

        $part = Part::query()->where('part_no', $partNo)->first();

        if ($part) {
            $part->update($attributes);

            return 'updated';
        }

        Part::create(['part_no' => $partNo] + $attributes + ['average_cost_price' => 0]);

        return 'created';
    }

    private function importVehicleRow(array $data, array $brandMap, array $categoryMap): string
    {
        $modelCode = trim((string) ($data['model_code'] ?? ''));
        $name = trim((string) ($data['name'] ?? ''));

        if ($modelCode === '' || $name === '') {
            throw new RuntimeException('model_code 與 name 為必填欄位。');
        }

        $attributes = [
            'barcode' => $this->nullableString($data['barcode'] ?? null, $modelCode),
            'name' => $name,
            'brand_id' => $this->mapOptionalCode($data['brand_code'] ?? null, $brandMap, '品牌代碼'),
            'category_id' => $this->mapOptionalCode($data['category_code'] ?? null, $categoryMap, '分類代碼'),
            'year' => $this->nullableYear($data['year'] ?? null),
            'color' => $this->nullableString($data['color'] ?? null),
            'engine_displacement' => $this->nullableString($data['engine_displacement'] ?? null),
            'last_cost_price' => $this->decimalValue($data['last_cost_price'] ?? null),
            'sale_price' => $this->decimalValue($data['sale_price'] ?? null),
            'remark' => $this->nullableString($data['remark'] ?? null),
            'is_active' => $this->booleanValue($data['is_active'] ?? null),
        ];

        $vehicle = Vehicle::query()->where('model_code', $modelCode)->first();

        if ($vehicle) {
            $vehicle->update($attributes);

            return 'updated';
        }

        Vehicle::create(['model_code' => $modelCode] + $attributes + ['average_cost_price' => 0]);

        return 'created';
    }

    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException('無法讀取匯入檔案。');
        }

        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = array_map(function ($value) {
                return is_string($value) ? preg_replace('/^\xEF\xBB\xBF/', '', trim($value)) : $value;
            }, $row);
        }

        fclose($handle);

        return $rows;
    }

    private function rowToData(array $headers, array $row): array
    {
        $data = [];

        foreach ($headers as $index => $header) {
            $data[$header] = Arr::get($row, $index);
        }

        return $data;
    }

    private function normalizeHeader(string $header): string
    {
        return Str::lower(trim(preg_replace('/^\xEF\xBB\xBF/', '', $header)));
    }

    private function mapOptionalCode(mixed $value, array $map, string $label): ?int
    {
        $code = Str::upper(trim((string) $value));

        if ($code === '') {
            return null;
        }

        if (! array_key_exists($code, $map)) {
            throw new RuntimeException($label.'不存在：'.$code);
        }

        return $map[$code];
    }

    private function nullableString(mixed $value, ?string $default = null): ?string
    {
        $normalized = trim((string) $value);

        if ($normalized === '') {
            return $default;
        }

        return $normalized;
    }

    private function decimalValue(mixed $value): float
    {
        $normalized = trim((string) $value);

        if ($normalized === '') {
            return 0;
        }

        if (! is_numeric($normalized)) {
            throw new RuntimeException('金額欄位必須為數字。');
        }

        $decimal = round((float) $normalized, 2);

        if ($decimal < 0) {
            throw new RuntimeException('金額欄位不可為負數。');
        }

        return $decimal;
    }

    private function integerValue(mixed $value): int
    {
        $normalized = trim((string) $value);

        if ($normalized === '') {
            return 0;
        }

        if (! preg_match('/^-?\d+$/', $normalized)) {
            throw new RuntimeException('整數欄位格式不正確。');
        }

        $integer = (int) $normalized;

        if ($integer < 0) {
            throw new RuntimeException('整數欄位不可為負數。');
        }

        return $integer;
    }

    private function nullableInteger(mixed $value): ?int
    {
        $normalized = trim((string) $value);

        if ($normalized === '') {
            return null;
        }

        if (! preg_match('/^-?\d+$/', $normalized)) {
            throw new RuntimeException('整數欄位格式不正確。');
        }

        return (int) $normalized;
    }

    private function nullableYear(mixed $value): ?int
    {
        $year = $this->nullableInteger($value);

        if ($year === null) {
            return null;
        }

        if ($year < 1900 || $year > 2100) {
            throw new RuntimeException('年份欄位必須介於 1900 到 2100。');
        }

        return $year;
    }

    private function booleanValue(mixed $value): bool
    {
        return in_array(Str::lower(trim((string) $value)), ['1', 'true', 'yes', 'y', '啟用'], true);
    }

    private function partColumns(): array
    {
        return ['part_no', 'barcode', 'name', 'brand_code', 'category_code', 'unit', 'last_cost_price', 'sale_price', 'safety_stock', 'remark', 'is_active'];
    }

    private function vehicleColumns(): array
    {
        return ['model_code', 'barcode', 'name', 'brand_code', 'category_code', 'year', 'color', 'engine_displacement', 'last_cost_price', 'sale_price', 'remark', 'is_active'];
    }
}
