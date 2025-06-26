<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyStatusesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_statuses', function (Blueprint $table) {
            $table->id();

            $table->date('date');                // التاريخ الميلادي
            $table->string('hijri_date')->nullable();    // التاريخ الهجري
            $table->string('day_name')->nullable();    // اسم اليوم

            // الحقول التي تحتوي على قوائم أسماء أو أرقام الموظفين بصيغة JSON
            $table->json('periodic_leaves')->nullable();    // إجازات دورية
            $table->json('annual_leaves')->nullable();    // إجازات سنوية
            $table->json('temporary_leaves')->nullable();    // إجازات زمنية
            $table->json('unpaid_leaves')->nullable();    // بدون راتب
            $table->json('absences')->nullable();        // غياب
            $table->json('long_leaves')->nullable();    // طويلة
            $table->json('sick_leaves')->nullable();    // مرضية
            $table->json('bereavement_leaves')->nullable(); // وفاة
            // إضافة حقول إجازات العيد وإجازات الخفر هنا
            $table->json('eid_leaves')->nullable();        // إجازات العيد
            $table->json('guard_rest')->nullable();        // إجازة الخفر
            $table->json('custom_usages')->nullable();    // الاستخدامات المخصصة (تمت إضافته)

            // الحقول الرقمية
            $table->integer('total_required')->default(86)->nullable(); // الملاك (تم تغييره ليكون عموداً في القاعدة)
            $table->integer('total_employees')->nullable();    // المجموع الكلي
            $table->integer('actual_attendance')->nullable(); // الحضور الفعلي
            $table->integer('paid_leaves_count')->nullable(); // إجازات براتب
            $table->integer('unpaid_leaves_count')->nullable(); // بدون راتب
            $table->integer('absences_count')->nullable();        // الغياب
            // إضافة حقل 'shortage' (النقص) هنا
            $table->integer('shortage')->nullable();        // النقص في الموظفين

            // إضافة حقول المنظم هنا
            $table->unsignedBigInteger('organizer_employee_id')->nullable(); // معرف الموظف المنظم
            $table->string('organizer_employee_name')->nullable();        // اسم الموظف المنظم

            // إضافة مفتاح أجنبي لربطها بجدول employees
            // تأكد أن جدول 'employees' موجود ومعرف الـ 'id' فيه صحيح.
            // إذا لم يكن لديك جدول employees، يمكنك إزالة هذا السطر أو التعليق عليه
            $table->foreign('organizer_employee_id')
                    ->references('id')
                    ->on('employees')
                    ->onDelete('set null'); // إذا تم حذف الموظف، يتم تعيين القيمة إلى NULL

            $table->timestamps(); // created_at و updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_statuses');
    }
}
