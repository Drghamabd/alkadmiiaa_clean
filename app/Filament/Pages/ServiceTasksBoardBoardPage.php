<?php

namespace App\Filament\Pages;

use App\Models\ServiceTask;
use App\Models\Employee;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Relaticle\Flowforge\Filament\Pages\KanbanBoardPage;

class ServiceTasksBoardPage extends KanbanBoardPage
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'لوحة مهام الشُعبة الخدمية';
    protected static ?string $title = 'إدارة مهام الشُعبة الخدمية';

    public function getSubject(): Builder
    {
        return ServiceTask::query()->with('assignedTo');
    }

    public function mount(): void
    {
        $this
            ->titleField('title')
            ->columnField('status')
            ->columns([
                'pending' => 'معلقة',
                'in_progress' => 'قيد التنفيذ',
                'completed' => 'مكتملة',
                'rejected' => 'مرفوضة',
            ])
            ->descriptionField('description')
            ->orderField('order_column')
            ->columnColors([
                'pending' => 'orange',
                'in_progress' => 'blue',
                'completed' => 'green',
                'rejected' => 'red',
            ])
            ->cardLabel('مهمة')
            ->pluralCardLabel('المهام')
            ->cardAttributes([
                'unit' => 'الوحدة',
                'due_date' => 'تاريخ الاستحقاق',
                'assignee_name' => 'المسؤول',
                'priority' => 'الأولوية',
            ])
            ->cardAttributeColors([
                'unit' => 'purple',
                'due_date' => 'sky',
                'assignee_name' => 'indigo',
                'priority' => 'blue',  // تعديل هنا حسب الأولوية
            ])
            ->cardAttributeIcons([
                'unit' => 'heroicon-o-building-office',
                'due_date' => 'heroicon-o-calendar',
                'assignee_name' => 'heroicon-o-user',
                'priority' => 'heroicon-o-flag',
            ]);
    }

    public function createAction(Action $action): Action
    {
        return $action
            ->iconButton()
            ->icon('heroicon-o-plus')
            ->modalHeading('إنشاء مهمة جديدة')
            ->modalWidth('xl')
            ->form([
                Forms\Components\TextInput::make('title')
                    ->label('عنوان المهمة')
                    ->required()
                    ->placeholder('أدخل عنوان المهمة')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('وصف المهمة')
                    ->columnSpanFull(),

                Select::make('unit')
                    ->label('الوحدة')
                    ->options(ServiceTask::UNITS)
                    ->required(),

                Select::make('priority')
                    ->label('الأولوية')
                    ->options(ServiceTask::PRIORITIES)
                    ->default('medium'),

                Forms\Components\DatePicker::make('due_date')
                    ->label('تاريخ الاستحقاق'),

                Select::make('assigned_to')
                    ->label('تعيين إلى')
                    ->options(fn () => Employee::lazy()->pluck('name', 'id'))
                    ->searchable(),
            ]);
    }

    public function editAction(Action $action): Action
    {
        return $action
            ->modalHeading('تعديل المهمة')
            ->modalWidth('xl')
            ->form([
                Forms\Components\TextInput::make('title')
                    ->label('عنوان المهمة')
                    ->required()
                    ->placeholder('أدخل عنوان المهمة')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('وصف المهمة')
                    ->columnSpanFull(),

                Select::make('status')
                    ->label('الحالة')
                    ->options(ServiceTask::STATUSES)
                    ->required(),

                Select::make('unit')
                    ->label('الوحدة')
                    ->options(ServiceTask::UNITS)
                    ->required(),

                Select::make('priority')
                    ->label('الأولوية')
                    ->options(ServiceTask::PRIORITIES),

                Forms\Components\DatePicker::make('due_date')
                    ->label('تاريخ الاستحقاق'),

                Select::make('assigned_to')
                    ->label('تعيين إلى')
                    ->options(fn () => Employee::lazy()->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->modalActions([
                Action::make('delete')
                    ->label('🗑 حذف المهمة')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد الحذف')
                    ->modalDescription('هل أنت متأكد من حذف هذه المهمة؟ لا يمكن التراجع.')
                    ->action(function (Action $action) {
                        $record = $action->getRecord();

                        if ($record) {
                            $record->delete();

                            Notification::make()
                                ->title('تم الحذف')
                                ->body('تم حذف المهمة بنجاح.')
                                ->success()
                                ->send();
                        }
                    }),
            ]);
    }
}
