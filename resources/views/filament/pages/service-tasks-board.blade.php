<x-filament-panels::page>
    {{-- يمكنك إضافة أي محتوى إضافي هنا للصفحة، مثل عنوان أو وصف --}}
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">إدارة مهام الشعبة الخدمية</h2>
    <p class="text-gray-600 dark:text-gray-400 mb-8">نظرة عامة مرنة على البيانات والمهام والرسوم البيانية.</p>

    {{-- حاوية الكانبان الرئيسية التي ستطبق عليها الـ CSS المرن --}}
    <div class="fi-kanban-grid"> {{-- هذه الفئة تم تخصيصها في theme.css --}}

        {{-- مثال لعمود واحد --}}
        <div class="kanban-column"> {{-- هذه الفئة تم تخصيصها في theme.css --}}
            <div class="kanban-column-header"> {{-- هذه الفئة تم تخصيصها في theme.css --}}
                <h3>معلقة</h3>
                <span class="fi-badge" data-color="warning">1</span> {{-- شارة العدد --}}
                <button type="button" class="fi-btn">
                    <x-heroicon-o-plus class="w-5 h-5" /> {{-- أيقونة الإضافة --}}
                </button>
            </div>

            <div class="kanban-column-cards"> {{-- حاوية البطاقات --}}
                {{-- مثال لبطاقة واحدة (مهمة) --}}
                <div class="kanban-card" data-status="pending"> {{-- هذه الفئة تم تخصيصها في theme.css --}}
                    <h4 class="kanban-card-title">قاعة 7</h4> {{-- عنوان المهمة --}}
                    <p class="kanban-card-description">تنظيف ومسح شامل</p> {{-- وصف المهمة --}}

                    <div class="kanban-card-meta"> {{-- معلومات فرعية --}}
                        <div>
                            <x-heroicon-o-calendar class="w-4 h-4" />
                            <span>{{ now()->format('Y-m-d') }}</span>
                        </div>
                        <div>
                            <x-heroicon-o-user class="w-4 h-4" />
                            <span>هادي عبد</span>
                        </div>
                        <div>
                            <span class="fi-badge" data-color="warning">متوسطة</span> {{-- شارة الأولوية --}}
                        </div>
                    </div>
                </div>

                {{-- يمكنك تكرار هذا الجزء لكل مهمة في هذا العمود --}}
                {{-- @if ($pendingTasks->isEmpty())
                    <div class="fi-kanban-column-empty-state">
                        <x-heroicon-o-archive box class="fi-icon" />
                        <p>لا توجد مهام في هذا العمود</p>
                    </div>
                @endif --}}
            </div>
        </div>

        {{-- عمود آخر (مثال: قيد التنفيذ) --}}
        <div class="kanban-column">
            <div class="kanban-column-header">
                <h3>قيد التنفيذ</h3>
                <span class="fi-badge" data-color="info">0</span>
                <button type="button" class="fi-btn">
                    <x-heroicon-o-plus class="w-5 h-5" />
                </button>
            </div>
            <div class="kanban-column-cards">
                <div class="fi-kanban-column-empty-state">
                    <x-heroicon-o-archive box class="fi-icon" />
                    <p>لا توجد مهام في هذا العمود</p>
                </div>
            </div>
        </div>

        {{-- عمود ثالث (مثال: مكتملة) --}}
        <div class="kanban-column">
            <div class="kanban-column-header">
                <h3>مكتملة</h3>
                <span class="fi-badge" data-color="success">0</span>
                <button type="button" class="fi-btn">
                    <x-heroicon-o-plus class="w-5 h-5" />
                </button>
            </div>
            <div class="kanban-column-cards">
                <div class="fi-kanban-column-empty-state">
                    <x-heroicon-o-archive box class="fi-icon" />
                    <p>لا توجد مهام في هذا العمود</p>
                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>