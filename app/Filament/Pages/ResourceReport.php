<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\SanitationFacilityTask;
use App\Models\GeneralCleaningTask;
use Carbon\Carbon;

class ResourceReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'تقرير الموارد';
    protected static string $view = 'filament.pages.resource-report';
    protected static ?string $slug = 'resource-report';
    protected static ?string $navigationGroup = 'التقارير والإحصائيات';
    protected static ?int $navigationSort = 4;
    protected static ?string $recordTitleAttribute = 'date';

    // احتفظ بها كـ array لأنه Livewire يتوقع مصفوفات للخصائص العامة
    // ومع ذلك، داخل loadResources، يمكننا العمل مع Collection
    public array $resources = [];
    public ?string $searchItem = '';
    public ?string $selectedMonth = '';
    public float $totalQuantityForSearchItem = 0;
    public ?string $formattedSelectedMonth = '';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && !auth()->user()->hasRole('filament_user');
    }

    public static function canAccess(): bool
    {
        return auth()->check() && !auth()->user()->hasRole('filament_user');
    }

    public function mount(): void
    {
        $this->selectedMonth = Carbon::now()->format('Y-m');
        $this->loadResources();
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['searchItem', 'selectedMonth'])) {
            $this->loadResources();
        }
    }

    protected function loadResources(): void
    {
        // ابدأ كـ Collection
        $resourcesCollection = collect();
        $totalForSearch = 0;

        // تنسيق الشهر بمجرد تحديده
        if (!empty($this->selectedMonth)) {
            try {
                $this->formattedSelectedMonth = Carbon::createFromFormat('Y-m', $this->selectedMonth)->translatedFormat('F Y');
            } catch (\Exception $e) {
                $this->formattedSelectedMonth = '';
                \Log::error('Invalid month format for resource report: ' . $this->selectedMonth);
            }
        } else {
            $this->formattedSelectedMonth = '';
        }

        // بناء استعلامات المهام
        $sanitationQuery = SanitationFacilityTask::with('unit');
        $generalCleaningQuery = GeneralCleaningTask::with('unit');

        // تطبيق فلتر الشهر
        if (!empty($this->selectedMonth)) {
            try {
                $startDate = Carbon::createFromFormat('Y-m', $this->selectedMonth)->startOfMonth();
                $endDate = Carbon::createFromFormat('Y-m', $this->selectedMonth)->endOfMonth();

                $sanitationQuery->whereBetween('date', [$startDate, $endDate]);
                $generalCleaningQuery->whereBetween('date', [$startDate, $endDate]);
            } catch (\Exception $e) {
                // التعامل مع الخطأ إذا كان تنسيق الشهر غير صحيح
            }
        }

        // جلب البيانات بعد تطبيق فلتر الشهر
        $sanitationTasks = $sanitationQuery->get();
        $generalCleaningTasks = $generalCleaningQuery->get();

        // معالجة مهام مرافق الصرف الصحي
        $sanitationTasks->each(function ($task) use (&$resourcesCollection, &$totalForSearch) {
            foreach ($task->resources_used ?? [] as $res) {
                $itemName = $res['name'] ?? '-';
                $quantity = (float)($res['quantity'] ?? 0);

                if (empty($this->searchItem) || stripos($itemName, $this->searchItem) !== false) {
                    $resourcesCollection->push([
                        'date' => $task->date,
                        'unit' => $task->unit->name ?? '---',
                        'task_type' => $task->task_type,
                        'item' => $itemName,
                        'quantity' => $quantity,
                        'resource_unit' => $res['unit'] ?? '-',
                        'notes' => $res['notes'] ?? '',
                    ]);

                    if (!empty($this->searchItem) && stripos($itemName, $this->searchItem) !== false) {
                        $totalForSearch += $quantity;
                    }
                }
            }
        });

        // معالجة مهام التنظيف العام
        $generalCleaningTasks->each(function ($task) use (&$resourcesCollection, &$totalForSearch) {
            foreach ($task->resources_used ?? [] as $res) {
                $itemName = $res['name'] ?? '-';
                $quantity = (float)($res['quantity'] ?? 0);

                if (empty($this->searchItem) || stripos($itemName, $this->searchItem) !== false) {
                    $resourcesCollection->push([
                        'date' => $task->date,
                        'unit' => $task->unit->name ?? '---',
                        'task_type' => $task->task_type,
                        'item' => $itemName,
                        'quantity' => $quantity,
                        'resource_unit' => $res['unit'] ?? '-',
                        'notes' => $res['notes'] ?? '',
                    ]);

                    if (!empty($this->searchItem) && stripos($itemName, $this->searchItem) !== false) {
                        $totalForSearch += $quantity;
                    }
                }
            }
        });

        // الآن قم بتحويلها إلى مصفوفة لتناسب خاصية Livewire العامة
        $this->resources = $resourcesCollection->sortByDesc('date')->values()->toArray();
        $this->totalQuantityForSearchItem = $totalForSearch;
    }
}