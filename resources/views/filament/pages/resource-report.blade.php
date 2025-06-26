<x-filament::page>
    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">تقرير الموارد المستخدمة في المهام</h2>
        <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
            {{-- حقل البحث عن اسم المورد --}}
            <label for="searchItem" class="sr-only">البحث باسم المورد</label>
            <x-filament::input.wrapper class="w-full">
                <x-filament::input
                    type="text"
                    id="searchItem"
                    placeholder="ابحث باسم المورد..."
                    wire:model.live.debounce.500ms="searchItem"
                    class="rtl:text-right"
                />
            </x-filament::input.wrapper>

            {{-- حقل اختيار الشهر --}}
            <label for="selectedMonth" class="sr-only">اختيار الشهر</label>
            <x-filament::input.wrapper class="w-full">
                <x-filament::input
                    type="month"
                    id="selectedMonth"
                    wire:model.live="selectedMonth"
                    class="rtl:text-right"
                />
            </x-filament::input.wrapper>

            <button type="button" onclick="printReport()" class="filament-button filament-button-size-md inline-flex items-center justify-center bg-primary-600 text-white hover:bg-primary-500 focus:outline-none focus:ring-4 focus:ring-primary-500 focus:ring-opacity-50 px-4 py-2 rounded-lg text-sm font-medium w-full md:w-auto">
                <x-heroicon-o-printer class="w-5 h-5 mr-2 -ml-1 rtl:mr-0 rtl:ml-2 rtl:-mr-1"/>
                طباعة التقرير
            </button>
        </div>
    </div>

    {{-- قم بتغليف المحتوى المراد طباعته بـ div له id --}}
    <div id="report-content">
        <h3 class="text-md font-semibold mb-4 text-gray-800 dark:text-gray-200 print:hidden">
            البيانات بتاريخ: {{ now()->translatedFormat('d F Y') }}
            @if ($this->formattedSelectedMonth)
                <span class="text-primary-600 dark:text-primary-400"> (شهر {{ $this->formattedSelectedMonth }})</span>
            @endif
            @if ($this->searchItem)
                <span class="text-primary-600 dark:text-primary-400"> (المورد: {{ $this->searchItem }})</span>
            @endif
        </h3>

        @if (!empty($this->searchItem))
            <div class="mb-4 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg shadow print:bg-white print:shadow-none">
                <p class="text-lg font-bold text-primary-700 dark:text-primary-200 print:text-primary-800">
                    إجمالي كمية "<span class="text-primary-800 dark:text-primary-50 print:text-primary-900">{{ $this->searchItem }}</span>" المصروفة:
                    <span class="text-2xl ml-2">{{ $this->totalQuantityForSearchItem }}</span>
                    {{-- تم التعديل هنا: استخدام !empty($this->resources) للتحقق --}}
                    @if (!empty($this->resources))
                        <span class="text-base text-primary-600 dark:text-primary-300 print:text-primary-700">
                            {{ $this->resources[0]['resource_unit'] ?? '' }}
                        </span>
                    @endif
                </p>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse text-right text-sm">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 print:bg-gray-200">
                        <th class="border p-2 text-gray-700 dark:text-gray-200 print:text-gray-800 whitespace-nowrap">التاريخ</th>
                        <th class="border p-2 text-gray-700 dark:text-gray-200 print:text-gray-800 whitespace-nowrap">الوحدة</th>
                        <th class="border p-2 text-gray-700 dark:text-gray-200 print:text-gray-800 whitespace-nowrap">نوع المهمة</th>
                        <th class="border p-2 text-gray-700 dark:text-gray-200 print:text-gray-800 whitespace-nowrap">المورد</th>
                        <th class="border p-2 text-gray-700 dark:text-gray-200 print:text-gray-800 whitespace-nowrap">الكمية</th>
                        <th class="border p-2 text-gray-700 dark:text-gray-200 print:text-gray-800 whitespace-nowrap">وحدة المورد</th>
                        <th class="border p-2 text-gray-700 dark:text-gray-200 print:text-gray-800 whitespace-nowrap">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->resources as $index => $res)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white dark:bg-gray-800 print:bg-white' : 'bg-gray-50 dark:bg-gray-900 print:bg-gray-50' }}">
                            <td class="border p-2 text-gray-800 dark:text-white print:text-gray-800 whitespace-nowrap">{{ $res['date'] }}</td>
                            <td class="border p-2 text-gray-800 dark:text-white print:text-gray-800 whitespace-nowrap">{{ $res['unit'] }}</td>
                            <td class="border p-2 text-gray-800 dark:text-white print:text-gray-800 whitespace-nowrap">{{ $res['task_type'] }}</td>
                            <td class="border p-2 text-gray-800 dark:text-white print:text-gray-800 whitespace-nowrap">{{ $res['item'] }}</td>
                            <td class="border p-2 text-gray-800 dark:text-white print:text-gray-800 whitespace-nowrap">{{ $res['quantity'] }}</td>
                            <td class="border p-2 text-gray-800 dark:text-white print:text-gray-800 whitespace-nowrap">{{ $res['resource_unit'] }}</td>
                            <td class="border p-2 text-gray-800 dark:text-white print:text-gray-800 whitespace-nowrap">{{ $res['notes'] ?? '' }}</td>
                        </tr>
                    @endforeach
                    @if (empty($this->resources))
                        <tr>
                            <td colspan="7" class="border p-4 text-center text-gray-500 dark:text-gray-400 print:text-gray-600">
                                لا توجد موارد مستخدمة لعرضها.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Script JavaScript للطباعة --}}
    <script>
        function printReport() {
            const printContent = document.getElementById('report-content').innerHTML;
            const originalBody = document.body.innerHTML;

            document.body.innerHTML = printContent;
            document.body.style.direction = 'rtl';
            document.body.style.fontFamily = '"Amiri", "Noto Kufi Arabic", serif';

            window.print();

            document.body.innerHTML = originalBody;
            document.body.style.direction = '';
            document.body.style.fontFamily = '';
            location.reload();
        }
    </script>
</x-filament::page>