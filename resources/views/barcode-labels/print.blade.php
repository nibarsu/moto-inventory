<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                條碼列印預覽
            </h2>
            <div class="print-hidden flex items-center gap-3">
                <span class="text-sm text-gray-500">產生時間：{{ $printedAt->format('Y-m-d H:i:s') }}</span>
                <button type="button" onclick="window.print()" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm hover:bg-indigo-500">
                    列印
                </button>
            </div>
        </div>
    </x-slot>

    <style>
        .barcode-sheet {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .barcode-label {
            width: 70mm;
            min-height: 36mm;
            box-sizing: border-box;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #ffffff;
            padding: 10px;
            page-break-inside: avoid;
        }

        .barcode-symbol {
            height: 56px;
            width: 100%;
        }

        .barcode-symbol svg {
            display: block;
            height: 100%;
            width: 100%;
        }

        @media print {
            .print-hidden,
            nav,
            header {
                display: none !important;
            }

            main,
            .barcode-sheet {
                margin: 0;
                padding: 0;
            }

            .barcode-label {
                border: 1px solid #111827;
                border-radius: 0;
            }
        }
    </style>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="print-hidden mb-6 text-sm text-gray-600">
                        共 {{ $labels->count() }} 張標籤。若印表機尺寸不同，可先用瀏覽器列印預覽調整縮放比例。
                    </div>

                    <div class="barcode-sheet">
                        @foreach ($labels as $label)
                            <div class="barcode-label">
                                <div class="mb-1 flex items-center justify-between gap-2">
                                    <div class="text-xs font-semibold tracking-wide text-gray-700">{{ $label->type_label }}</div>
                                    <div class="text-xs text-gray-500">{{ $label->code }}</div>
                                </div>
                                <div class="mb-2 text-sm font-semibold text-gray-900">{{ $label->name }}</div>
                                <div class="barcode-symbol">{!! $label->svg !!}</div>
                                <div class="mt-2 text-center text-xs tracking-[0.2em] text-gray-700">{{ $label->barcode }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
