<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            條碼掃描
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">相機掃描</h3>
                                <p class="mt-1 text-sm text-gray-500">建議使用 HTTPS 或本機開發網址，並允許瀏覽器存取相機。</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="button" id="start-scanner" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm hover:bg-indigo-500">
                                    開始掃描
                                </button>
                                <button type="button" id="stop-scanner" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                                    停止
                                </button>
                            </div>
                        </div>

                        <div class="overflow-hidden rounded-lg border border-dashed border-gray-300 bg-gray-50">
                            <video id="barcode-video" class="aspect-[4/3] w-full bg-gray-900 object-cover" autoplay muted playsinline></video>
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs font-semibold uppercase tracking-widest text-gray-500">掃描狀態</div>
                                <div id="scanner-status" class="mt-2 text-sm text-gray-700">尚未啟動掃描器。</div>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs font-semibold uppercase tracking-widest text-gray-500">最近掃描結果</div>
                                <div id="last-scan-result" class="mt-2 break-all text-sm font-medium text-gray-900">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-900">手動查詢</h3>
                        <p class="mt-1 text-sm text-gray-500">如果掃描器或瀏覽器不支援相機辨識，可以直接輸入條碼內容查詢。</p>

                        <form method="GET" action="{{ route('barcode-scans.index') }}" id="barcode-scan-form" class="mt-6 space-y-4">
                            <div>
                                <x-input-label for="type" value="商品類型" />
                                <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="all" @selected($type === 'all')>全部</option>
                                    <option value="part" @selected($type === 'part')>零件</option>
                                    <option value="vehicle" @selected($type === 'vehicle')>整車</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="barcode" value="條碼內容" />
                                <x-text-input id="barcode" name="barcode" type="text" class="mt-1 block w-full" :value="$barcode" placeholder="請掃描或輸入條碼 / 料號 / 車型代碼" autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('barcode')" />
                            </div>

                            <div class="flex gap-3">
                                <x-primary-button>查詢</x-primary-button>
                                <a href="{{ route('barcode-scans.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                                    清除
                                </a>
                            </div>
                        </form>

                        <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                            本頁優先使用瀏覽器原生條碼辨識。若裝置不支援，仍可保留手動輸入流程，不會影響日常作業。
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">查詢結果</h3>
                            @if ($barcode !== '')
                                <p class="mt-1 text-sm text-gray-500">查詢內容：<span class="font-medium text-gray-900">{{ $barcode }}</span></p>
                            @endif
                        </div>
                        @if ($barcode !== '')
                            <div class="text-sm text-gray-500">共 {{ $results->count() }} 筆</div>
                        @endif
                    </div>

                    @if ($barcode === '')
                        <div class="rounded-lg border border-dashed border-gray-300 px-6 py-10 text-center text-sm text-gray-500">
                            啟動掃描器或手動輸入條碼後，系統會在這裡顯示對應的商品資料。
                        </div>
                    @elseif ($results->isEmpty())
                        <div class="rounded-lg border border-dashed border-gray-300 px-6 py-10 text-center text-sm text-gray-500">
                            找不到符合的商品資料，請確認條碼、料號或車型代碼是否正確。
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">類型</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">代碼</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">商品名稱</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">品牌</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">分類</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">比對方式</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">狀態</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">操作</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($results as $item)
                                        <tr>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->type_label }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $item->code }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                <div>{{ $item->name }}</div>
                                                <div class="mt-1 text-xs text-gray-500">條碼：{{ $item->barcode }}</div>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->brand }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->category }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                {{ $item->matched_by === 'barcode' ? '條碼' : '商品代碼' }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-center text-sm">
                                                @if ($item->is_active)
                                                    <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">啟用</span>
                                                @else
                                                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">停用</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                                <a href="{{ $item->show_url }}" class="font-medium text-indigo-600 hover:text-indigo-900">查看</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const video = document.getElementById('barcode-video');
            const startButton = document.getElementById('start-scanner');
            const stopButton = document.getElementById('stop-scanner');
            const statusElement = document.getElementById('scanner-status');
            const resultElement = document.getElementById('last-scan-result');
            const barcodeInput = document.getElementById('barcode');
            const form = document.getElementById('barcode-scan-form');

            let stream = null;
            let detector = null;
            let timerId = null;
            let isSubmitting = false;

            const setStatus = (message) => {
                statusElement.textContent = message;
            };

            const stopScanner = () => {
                if (timerId) {
                    window.clearInterval(timerId);
                    timerId = null;
                }

                if (stream) {
                    stream.getTracks().forEach((track) => track.stop());
                    stream = null;
                }

                video.srcObject = null;
                setStatus('掃描器已停止。');
            };

            const submitDetectedCode = (code) => {
                if (isSubmitting) {
                    return;
                }

                isSubmitting = true;
                resultElement.textContent = code;
                barcodeInput.value = code;
                setStatus('已辨識條碼，正在查詢商品資料...');
                stopScanner();
                form.submit();
            };

            const scanFrame = async () => {
                if (!detector || !video.videoWidth || !video.videoHeight || isSubmitting) {
                    return;
                }

                try {
                    const barcodes = await detector.detect(video);

                    if (!barcodes.length) {
                        return;
                    }

                    const detectedCode = (barcodes[0].rawValue || '').trim();

                    if (!detectedCode) {
                        return;
                    }

                    submitDetectedCode(detectedCode);
                } catch (error) {
                    setStatus(`掃描失敗：${error.message}`);
                }
            };

            const startScanner = async () => {
                if (!('mediaDevices' in navigator) || !navigator.mediaDevices.getUserMedia) {
                    setStatus('目前瀏覽器不支援相機存取，請改用手動輸入查詢。');
                    return;
                }

                if (!('BarcodeDetector' in window)) {
                    setStatus('目前瀏覽器不支援原生條碼辨識，請改用手動輸入查詢。');
                    return;
                }

                try {
                    detector = new window.BarcodeDetector({
                        formats: ['code_39', 'code_128', 'ean_13', 'ean_8', 'upc_a', 'upc_e'],
                    });

                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: { ideal: 'environment' },
                        },
                        audio: false,
                    });

                    video.srcObject = stream;
                    await video.play();

                    setStatus('掃描器啟動成功，請將條碼置於畫面中央。');
                    timerId = window.setInterval(scanFrame, 500);
                } catch (error) {
                    setStatus(`無法啟動掃描器：${error.message}`);
                }
            };

            startButton?.addEventListener('click', startScanner);
            stopButton?.addEventListener('click', stopScanner);
            window.addEventListener('beforeunload', stopScanner);
        });
    </script>
</x-app-layout>
