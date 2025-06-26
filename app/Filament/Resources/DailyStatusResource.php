<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyStatusResource\Pages;
use App\Models\DailyStatus;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Filament\Forms\Get;
use Filament\Forms\Set;


class DailyStatusResource extends Resource
{
    protected static ?string $model = DailyStatus::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $modelLabel = 'الموقف اليومي';
    protected static ?string $navigationLabel = 'الموقف اليومي';
    protected static ?string $pluralModelLabel = 'المواقف اليومية';
    protected static ?string $navigationGroup = 'إدارة الموظفين';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'date';
    protected static ?string $slug = 'daily-status';
    protected static ?string $navigationUrl = 'daily-status';
    protected static ?string $breadcrumb = 'الموقف اليومي';
    protected static ?string $breadcrumbPlural = 'المواقف اليومية';
    protected static ?string $breadcrumbPluralGroup = 'إدارة الموظفين';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('معلومات الموقف')
                ->schema([
                    Forms\Components\DatePicker::make('date')
                        ->label('التاريخ')
                        ->required()
                        ->default(now())
                        ->live(),

                    Forms\Components\Placeholder::make('hijri_date')
                        ->label('التاريخ الهجري')
                        ->content(fn (Get $get) => $get('date') ? self::convertToHijri($get('date')) : null),

                    Forms\Components\Placeholder::make('day_name')
                        ->label('اليوم')
                        ->content(fn (Get $get) => $get('date') ? self::getDayName($get('date')) : null),
                ])->columns(3),

            Forms\Components\Section::make('الإجازات')
                ->schema([
                    self::makeLeaveRepeater('periodic_leaves', 'الإجازات الدورية'),
                    self::makeLeaveRepeater('annual_leaves', 'الإجازات السنوية'),
                    self::makeTemporaryLeaveRepeater('temporary_leaves', 'الإجازات الزمنية'),

                    Forms\Components\Repeater::make('eid_leaves')
                        ->label('إجازات الأعياد')
                        ->schema([
                            Forms\Components\Select::make('eid_type')
                                ->label('نوع العيد')
                                ->options([
                                    'eid_alfitr' => 'عيد الفطر',
                                    'eid_aladha' => 'عيد الأضحى',
                                    'eid_algahdir' => 'عيد الغدير',
                                ])
                                ->required()
                                ->native(false),
                            ...self::getBaseLeaveRepeaterSchema(),
                        ])
                        ->columns(3)
                        ->itemLabel(fn (array $state): ?string => ($state['eid_type'] ? match ($state['eid_type']) {
                            'eid_alfitr' => 'عيد الفطر',
                            'eid_aladha' => 'عيد الأضحى',
                            'eid_algahdir' => 'عيد الغدير',
                            default => ''
                        } . ' - ' : '') . ($state['employee_name'] ?? null)),

                    // إعادة حقل استراحة خفر إلى طبيعته الأصلية
                    self::makeLeaveRepeater('guard_rest', 'استراحة خفر'),
                ]),

            Forms\Components\Section::make('الغياب والإجازات الأخرى')
                ->schema([
                    self::makeLeaveRepeater('unpaid_leaves', 'إجازة بدون راتب'),
                    self::makeLeaveRepeaterWithDates('absences', 'الغياب'),
                    self::makeLeaveRepeaterWithDates('long_leaves', 'الإجازات الطويلة'),
                    self::makeLeaveRepeaterWithDates('sick_leaves', 'الإجازات المرضية'),
                    self::makeLeaveRepeater('bereavement_leaves', 'إجازة وفاة'),
                    
                    // إضافة حقل استخدام مخصص جديد هنا
                    Forms\Components\Repeater::make('custom_usages')
                        ->label('الاستخدام ')
                        ->schema([
                            // حقول الموظف الأساسية
                            Forms\Components\Select::make('employee_id')
                                ->label('اسم الموظف')
                                ->options(fn () => Employee::pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $employee = Employee::find($state);
                                    if ($employee) {
                                        $set('employee_number', $employee->employee_number);
                                        $set('employee_name', $employee->name);
                                    } else {
                                        $set('employee_number', null);
                                        $set('employee_name', null);
                                    }
                                }),
                            Forms\Components\TextInput::make('employee_number')
                                ->label('الرقم الوظيفي')
                                ->numeric()
                                ->required()
                                ->readOnly(),
                            Forms\Components\Hidden::make('employee_name'),
                            
                            // حقل تفاصيل الاستخدام
                            Forms\Components\TextInput::make('usage_details')
                                ->label('تفاصيل الاستخدام')
                                ->placeholder('مثال: اجتماع، مهمة خارجية، دورة تدريبية...')
                                ->required()
                                ->columnSpanFull(), // ليأخذ عرض العمود بالكامل
                        ])
                        ->columns(3) // 3 أعمدة (اسم، رقم، تفاصيل استخدام)
                        ->itemLabel(fn (array $state): ?string => 'استخدام: ' . ($state['employee_name'] ?? null)),
                ]),

            Forms\Components\Section::make('الإحصائيات')
                ->schema([
                    // تم تغيير هذا من Placeholder إلى TextInput ليصبح قابلاً للتعديل
                    Forms\Components\TextInput::make('total_required')
                        ->label('الملاك')
                        ->numeric()
                        ->default(86) // قيمة افتراضية يمكن تغييرها
                        ->required()
                        ->live(), // لتحديث النقص بناءً على هذه القيمة

                    Forms\Components\Placeholder::make('total_employees')
                        ->label('الموجود الحالي')
                        ->content(function () {
                            return \App\Models\Employee::where('is_active', 1)->count();
                        }),

                    Forms\Components\Placeholder::make('shortage')
                        ->label('النقص')
                        ->content(function (Get $get) { // استخدام Get للحصول على قيمة total_required
                            $required = (int) ($get('total_required') ?? 86); // الحصول على القيمة المدخلة أو 86 كافتراضي
                            $current = \App\Models\Employee::where('is_active', 1)->count();
                            return $required - $current;
                        }),

                    Forms\Components\Placeholder::make('actual_attendance')
                        ->label('الحضور الفعلي')
                        ->content(function (Get $get) {
                            $current = \App\Models\Employee::where('is_active', 1)->count();

                            $paidLeaves = count($get('annual_leaves') ?? [])
                                         + count($get('periodic_leaves') ?? [])
                                         + count($get('sick_leaves') ?? [])
                                         + count($get('bereavement_leaves') ?? []);

                            $eidLeavesCount = 0;
                            foreach ($get('eid_leaves') ?? [] as $eidLeave) {
                                if (isset($eidLeave['employee_id'])) {
                                    $eidLeavesCount++;
                                }
                            }
                            $paidLeaves += $eidLeavesCount;

                            $unpaidLeaves = count($get('unpaid_leaves') ?? []);
                            $absences = count($get('absences') ?? []);
                            $temporaryLeaves = count($get('temporary_leaves') ?? []);
                            $guardRest = count($get('guard_rest') ?? []);
                            $customUsages = count($get('custom_usages') ?? []); // إضافة الاستخدامات المخصصة

                            // الحضور الفعلي = الموجود الحالي - (جميع أنواع الإجازات والغياب واستراحة الخفر والاستخدامات المخصصة)
                            return $current - ($paidLeaves + $unpaidLeaves + $absences + $temporaryLeaves + $guardRest + $customUsages);
                        }),

                    Forms\Components\Placeholder::make('paid_leaves_count')
                        ->label('إجازات براتب')
                        ->content(function (Get $get) {
                            $count = count($get('annual_leaves') ?? [])
                                       + count($get('periodic_leaves') ?? [])
                                       + count($get('sick_leaves') ?? [])
                                       + count($get('bereavement_leaves') ?? []);

                            foreach ($get('eid_leaves') ?? [] as $eidLeave) {
                                if (isset($eidLeave['employee_id'])) {
                                    $count++;
                                }
                            }
                            return $count;
                        }),

                    Forms\Components\Placeholder::make('unpaid_leaves_count')
                        ->label('إجازات بدون راتب')
                        ->content(fn (Get $get) =>
                            count($get('unpaid_leaves') ?? [])
                        ),

                    Forms\Components\Placeholder::make('absences_count')
                        ->label('الغياب')
                        ->content(fn (Get $get) =>
                            count($get('absences') ?? [])
                        ),
                    Forms\Components\Placeholder::make('guard_rest_count')
                        ->label('استراحة خفر')
                        ->content(fn (Get $get) =>
                            count($get('guard_rest') ?? [])
                        ),
                    Forms\Components\Placeholder::make('temporary_leaves_count')
                        ->label('إجازات زمنية')
                        ->content(fn (Get $get) =>
                            count($get('temporary_leaves') ?? [])
                        ),
                    // إضافة عدد الاستخدامات المخصصة
                    Forms\Components\Placeholder::make('custom_usages_count')
                        ->label('الاستخدام ')
                        ->content(fn (Get $get) =>
                            count($get('custom_usages') ?? [])
                        ),
                ])
                ->columns(2),

            // ** إضافة قسم منظم الموقف **
            Forms\Components\Section::make('منظم الموقف')
                ->schema([
                    Forms\Components\Select::make('organizer_employee_id')
                        ->label('اسم الموظف المنظم')
                        ->options(fn () => Employee::pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            $employee = Employee::find($state);
                            if ($employee) {
                                $set('organizer_employee_name', $employee->name);
                            } else {
                                $set('organizer_employee_name', null);
                            }
                        }),
                    Forms\Components\Hidden::make('organizer_employee_name'),
                ])->columns(1), // عمود واحد لهذا القسم

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')->label('التاريخ')->date(),
                Tables\Columns\TextColumn::make('hijri_date')->label('التاريخ الهجري'),
                Tables\Columns\TextColumn::make('day_name')->label('اليوم'),
                Tables\Columns\TextColumn::make('total_employees')->label('العدد الكلي'),
                Tables\Columns\TextColumn::make('actual_attendance')->label('الحضور الفعلي'),
                Tables\Columns\TextColumn::make('organizer_employee_name')->label('منظم الموقف'), // إضافة عمود لمنظم الموقف
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('طباعة')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('print.daily-status', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export')->label('تصدير البيانات'),
                ]),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')->label('تصدير البيانات'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailyStatuses::route('/'),
            'create' => Pages\CreateDailyStatus::route('/create'),
            'edit' => Pages\EditDailyStatus::route('/{record}/edit'),
            'print' => Pages\PrintDailyStatus::route('/{record}/print'),
        ];
    }

    protected static function getBaseLeaveRepeaterSchema(): array
    {
        return [
            Forms\Components\Select::make('employee_id')
                ->label('اسم الموظف')
                ->options(fn () => Employee::pluck('name', 'id'))
                ->searchable()
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, Set $set) {
                    $employee = Employee::find($state);
                    if ($employee) {
                        $set('employee_number', $employee->employee_number);
                        $set('employee_name', $employee->name);
                    } else {
                        $set('employee_number', null);
                        $set('employee_name', null);
                    }
                }),
            // استخدام readOnly() لضمان إرسال القيمة عند الحفظ
            Forms\Components\TextInput::make('employee_number')->label('الرقم الوظيفي')->numeric()->required()->readOnly(),
            Forms\Components\Hidden::make('employee_name'),
        ];
    }

    protected static function getEmployeeItemLabelCallable(): callable
    {
        return fn (array $state): ?string => $state['employee_name'] ?? null;
    }

    // هذا التابع أصبح يُستخدم للأنواع الأخرى غير استراحة الخفر والاستخدامات المخصصة الآن
    protected static function makeLeaveRepeater(string $name, string $label, array $extraSchema = []): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make($name)
            ->label($label)
            ->schema(array_merge(self::getBaseLeaveRepeaterSchema(), $extraSchema)) // دمج الـ schema الأساسي مع الإضافي
            ->columns(2)
            ->itemLabel(self::getEmployeeItemLabelCallable());
    }

    protected static function makeLeaveRepeaterWithDates(string $name, string $label): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make($name)
            ->label($label)
            ->schema([
                ...self::getBaseLeaveRepeaterSchema(),
                Forms\Components\DatePicker::make('from_date')->label('من')->required(),
                Forms\Components\DatePicker::make('to_date')->label('إلى')->required(),
            ])
            ->columns(4)
            ->itemLabel(self::getEmployeeItemLabelCallable());
    }

    protected static function makeTemporaryLeaveRepeater(string $name, string $label): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make($name)
            ->label($label)
            ->schema([
                ...self::getBaseLeaveRepeaterSchema(),
                Forms\Components\TimePicker::make('from_time')->label('من الساعة')->required(),
                Forms\Components\TimePicker::make('to_time')->label('إلى الساعة')->required(),
            ])
            ->columns(4)
            ->itemLabel(self::getEmployeeItemLabelCallable());
    }

    protected static function convertToHijri(string $gregorianDate): string
    {
        return \Alkoumi\LaravelHijriDate\Hijri::Date('j F Y', $gregorianDate);
    }

    protected static function getDayName(string $date): string
    {
        $days = [
            'Sunday' => 'الأحد',
            'Monday' => 'الإثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
            'Saturday' => 'السبت',
        ];

        $day = Carbon::parse($date)->format('l');
        return $days[$day] ?? $day;
    }
}
