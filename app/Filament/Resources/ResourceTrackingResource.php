<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResourceTrackingResource\Pages;
use App\Models\ResourceTracking;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use App\Models\GeneralCleaningTask;
use App\Models\SanitationFacilityTask;
use Carbon\Carbon;

class ResourceTrackingResource extends Resource
{
    protected static ?string $model = ResourceTracking::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?string $navigationLabel = 'تتبع الموارد';
    protected static ?string $modelLabel = 'تتبع الموارد';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('date')
                ->required()
                ->default(now())
                ->label('التاريخ'),

            Forms\Components\Select::make('unit_id')
                ->relationship('unit', 'name')
                ->required()
                ->label('الوحدة')
                ->native(false),

            Forms\Components\TextInput::make('working_hours')
                ->numeric()
                ->required()
                ->minValue(0)
                ->maxValue(24)
                ->label('ساعات العمل الإجمالية'),

            Forms\Components\TextInput::make('cleaning_materials')
                ->numeric()
                ->required()
                ->minValue(0)
                ->suffix('لتر')
                ->label('مواد التنظيف المستهلكة'),

            Forms\Components\TextInput::make('water_consumption')
                ->numeric()
                ->required()
                ->minValue(0)
                ->suffix('لتر')
                ->label('استهلاك المياه'),

            Forms\Components\TextInput::make('equipment_usage')
                ->numeric()
                ->required()
                ->minValue(0)
                ->label('عدد المعدات المستخدمة'),

            Forms\Components\Textarea::make('notes')
                ->label('ملاحظات')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->label('التاريخ'),

                Tables\Columns\TextColumn::make('unit.name')
                    ->sortable()
                    ->label('الوحدة'),

                Tables\Columns\TextColumn::make('working_hours')
                    ->sortable()
                    ->label('ساعات العمل'),

                Tables\Columns\TextColumn::make('cleaning_materials')
                    ->sortable()
                    ->suffix(' لتر')
                    ->label('مواد التنظيف'),

                Tables\Columns\TextColumn::make('efficiency')
                    ->label('الكفاءة (مهمة/ساعة)')
                    ->state(function (ResourceTracking $record) {
                        $completedGeneral = GeneralCleaningTask::where('unit_id', $record->unit_id)
                            ->whereDate('date', $record->date)
                            ->where('status', 'مكتمل')
                            ->count();
                        $completedSanitation = SanitationFacilityTask::where('unit_id', $record->unit_id)
                            ->whereDate('date', $record->date)
                            ->where('status', 'مكتمل')
                            ->count();
                        $totalCompletedTasks = $completedGeneral + $completedSanitation;

                        $hours = $record->working_hours ?? 1;
                        return round($totalCompletedTasks / max($hours, 1), 2);
                    }),

                Tables\Columns\IconColumn::make('is_efficient')
                    ->label('كفء؟')
                    ->boolean()
                    ->state(fn(ResourceTracking $record) =>
                        $record->working_hours > 0 && ($record->cleaning_materials > 0 || $record->water_consumption > 0 || $record->equipment_usage > 0)
                    ),

                Tables\Columns\TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn(ResourceTracking $record) => $record->notes),
            ])
            ->headerActions([
                Tables\Actions\Action::make('auto_generate_resource_data')
                    ->label('توليد بيانات الموارد تلقائياً')
                    ->icon('heroicon-o-sparkles')
                    ->color('info')
                    ->action(function () {
                        self::generateDailyResourceData();
                        Notification::make()
                            ->title('تم توليد / تحديث بيانات الموارد بنجاح')
                            ->success()
                            ->send();
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unit')
                    ->relationship('unit', 'name')
                    ->label('الوحدة'),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('to')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('date', '>=', $data['from']))
                            ->when($data['to'], fn($q) => $q->whereDate('date', '<=', $data['to']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResourceTrackings::route('/'),
            'create' => Pages\CreateResourceTracking::route('/create'),
            'edit' => Pages\EditResourceTracking::route('/{record}/edit'),
        ];
    }

    public static function generateDailyResourceData()
    {
        $units = Unit::all();
        $today = now()->format('Y-m-d');

        foreach ($units as $unit) {
            $existingRecord = ResourceTracking::where('unit_id', $unit->id)
                ->where('date', $today)
                ->first();

            $totalCleaningMaterials = 0;
            $totalWaterConsumption = 0;
            $totalEquipmentUsage = 0;

            $generalCleaningTasks = GeneralCleaningTask::where('unit_id', $unit->id)
                ->whereDate('date', $today)
                ->where('status', 'مكتمل')
                ->get();

            foreach ($generalCleaningTasks as $task) {
                foreach ($task->resources_used ?? [] as $resource) {
                    $itemName = $resource['name'] ?? '';
                    $quantity = (float)($resource['quantity'] ?? 0);
                    $resourceUnit = $resource['unit'] ?? '';

                    // ✅ تم تحديث الشرط ليشمل "كفوف"، "كمامات"، "كاسات"، و "نفايات" / "أكياس نفايات"
                    if (stripos($itemName, 'منظف') !== false ||
                        stripos($itemName, 'صابون') !== false ||
                        stripos($itemName, 'معقم') !== false ||
                        stripos($itemName, 'كفوف') !== false ||
                        stripos($itemName, 'كمامات') !== false ||
                        stripos($itemName, 'كاسات') !== false ||
                        stripos($itemName, 'نفايات') !== false || // هذا سيشمل "أكياس نفايات" و "صناديق نفايات" وما شابه
                        stripos($itemName, 'أكياس نفايات') !== false
                    ) {
                        $totalCleaningMaterials += $quantity;
                    } elseif (stripos($itemName, 'ماء') !== false || (stripos($resourceUnit, 'لتر') !== false && stripos($itemName, 'ماء') !== false)) {
                        $totalWaterConsumption += $quantity;
                    } elseif (stripos($itemName, 'مكنسة') !== false || stripos($itemName, 'ممسحة') !== false || stripos($itemName, 'جهاز') !== false) {
                        $totalEquipmentUsage += $quantity;
                    }
                }
            }

            $sanitationTasks = SanitationFacilityTask::where('unit_id', $unit->id)
                ->whereDate('date', $today)
                ->where('status', 'مكتمل')
                ->get();

            foreach ($sanitationTasks as $task) {
                foreach ($task->resources_used ?? [] as $resource) {
                    $itemName = $resource['name'] ?? '';
                    $quantity = (float)($resource['quantity'] ?? 0);
                    $resourceUnit = $resource['unit'] ?? '';

                    // ✅ تم تحديث الشرط ليشمل "كفوف"، "كمامات"، "كاسات"، و "نفايات" / "أكياس نفايات"
                    if (stripos($itemName, 'منظف') !== false ||
                        stripos($itemName, 'مطهر') !== false ||
                        stripos($itemName, 'معطر') !== false ||
                        stripos($itemName, 'تيزاب') !== false ||
                        stripos($itemName, 'فلاش') !== false ||
                        stripos($itemName, 'زاهي') !== false ||
                        stripos($itemName, 'صابون') !== false ||
                        stripos($itemName, 'صابون سائل') !== false ||
                        stripos($itemName, 'ملمع') !== false ||
                        stripos($itemName, 'سيم') !== false ||
                        stripos($itemName, 'جلافة') !== false ||
                        stripos($itemName, 'بطش') !== false ||
                        stripos($itemName, 'سفنجة') !== false ||
                        stripos($itemName, 'كفوف') !== false ||
                         stripos($itemName, 'مكرافة') !== false ||
                        stripos($itemName, 'مقشة') !== false ||
                        stripos($itemName, 'شفرة') !== false ||
                        stripos($itemName, 'ماء') !== false ||
                        stripos($itemName, 'مياه') !== false ||
                         stripos($itemName, 'ثلح') !== false ||
                        stripos($itemName, 'ماء ارو') !== false ||
                        stripos($itemName, 'كلاص') !== false ||
                        stripos($itemName, 'كاس') !== false ||

                        stripos($itemName, 'كمامات') !== false ||
                        stripos($itemName, 'كاسات') !== false ||
                        stripos($itemName, 'نفايات') !== false ||
                        stripos($itemName, 'أكياس نفايات') !== false
                    ) {
                        $totalCleaningMaterials += $quantity;
                    } elseif (stripos($itemName, 'ماء') !== false || (stripos($resourceUnit, 'لتر') !== false && stripos($itemName, 'ماء') !== false)) {
                        $totalWaterConsumption += $quantity;
                    } elseif (stripos($itemName, 'فرشاة') !== false || stripos($itemName, 'مضخة') !== false) {
                        $totalEquipmentUsage += $quantity;
                    }
                }
            }

            $totalWorkingHours = $generalCleaningTasks->sum('working_hours') + $sanitationTasks->sum('working_hours');
            $totalWorkingHours = $totalWorkingHours > 0 ? $totalWorkingHours : 8;

            if (!$existingRecord) {
                ResourceTracking::create([
                    'date' => $today,
                    'unit_id' => $unit->id,
                    'working_hours' => $totalWorkingHours,
                    'cleaning_materials' => $totalCleaningMaterials,
                    'water_consumption' => $totalWaterConsumption,
                    'equipment_usage' => $totalEquipmentUsage,
                    'notes' => 'بيانات تم توليدها تلقائياً لـ ' . $unit->name . ' بتاريخ ' . $today . ' بناءً على المهام المكتملة.',
                ]);
            } else {
                $existingRecord->update([
                    'working_hours' => $totalWorkingHours,
                    'cleaning_materials' => $totalCleaningMaterials,
                    'water_consumption' => $totalWaterConsumption,
                    'equipment_usage' => $totalEquipmentUsage,
                    'notes' => 'بيانات موارد تم تحديثها تلقائياً لـ ' . $unit->name . ' بتاريخ ' . $today . ' بناءً على المهام المكتملة.',
                ]);
            }
        }
    }
}