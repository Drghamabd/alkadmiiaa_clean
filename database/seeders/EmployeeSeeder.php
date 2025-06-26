<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
          \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('employees')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $employees = [
    // النظافة العامة
    ['name' => 'ضرغام عبد الله هادي عبد', 'employee_number' => 1758, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'ثائر عبد الأمير زيدان', 'employee_number' => 1761, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'وهب سرحان جهيد جواد', 'employee_number' => 2066, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'مصطفى حاتم كريم جبار', 'employee_number' => 15649, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'احمد ناصر حسون جابر', 'employee_number' => 2002, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'حيدر إبراهيم عمران  كا طع', 'employee_number' => 1920, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'سلام حسين ملا توان راهي', 'employee_number' => 1845, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'احمد عبد الحسين كاظم علاوي', 'employee_number' => 1922, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'ظافر عبد المحسن ماضي صفوان', 'employee_number' => 1919, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'إسماعيل محمد علي عباس', 'employee_number' => 12666, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'منتظر محمد فليح حسن قنديل', 'employee_number' => 18448, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'علاء رسن ديوان فياض', 'employee_number' => 19652, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'فاضل جواد عبد الامير علوان', 'employee_number' => 2023, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'يوسف عبد الأمير حسين', 'employee_number' => 2025, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'امين عبد الله حاكم عبد', 'employee_number' => 15650, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'احمد هاشم علوان جثير', 'employee_number' => 2081, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'علاء عبد الأمير عبد الكاظم', 'employee_number' => 1821, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'جاسم عزيز مهدي محمد', 'employee_number' => 2079, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'سعد مهجر داغر مطلك', 'employee_number' => 2011, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'مرتضى عباس حسن علي', 'employee_number' => 10380, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'وسام ناظم عبد الرضا حمزه', 'employee_number' => 11675, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'مرتضى شهيد رياح ناصر', 'employee_number' => 21512, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'صلاح علاوي علوان بدوي', 'employee_number' => 1875, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'مسلم جبار محمد علي', 'employee_number' => 2000, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'مختار محمد كاظم مشكور', 'employee_number' => 21515, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'ياسر رشيد حميد حسن', 'employee_number' => 16386, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'محمد رضا حسين علوان', 'employee_number' => 15924, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'عبد الله سعيد حسين محسن', 'employee_number' => 25294, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'منتظر لفتة ياسر مارد', 'employee_number' => 15421, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'عباس محمد لفتة جاسم', 'employee_number' => 18015, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'عباس حميد خشان شنبار', 'employee_number' => 1990, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'ليث سعيد حسين محسن', 'employee_number' => 19315, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'حمزة احميد عباس حلوب', 'employee_number' => 20279, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'حسن محسن حسين عمران', 'employee_number' => 23125, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'تحسين علي حسين داود', 'employee_number' => 1904, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'احمد محمد نعمه جبر', 'employee_number' => 26340, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'محمد كاظم سعيد كاظم', 'employee_number' => 27828, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'احمد هاشم عبود حنون', 'employee_number' => 15175, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'سيف مهند رزاق علي', 'employee_number' => 29056, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'محمد علوان عبد الحسين هادي', 'employee_number' => 29051, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'علي فاضل مظهور كاظم', 'employee_number' => 29053, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'كاظم عباس شنان شعلان', 'employee_number' => 29055, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
        ['name' => 'علي معن هادي حسين', 'employee_number' => 29054, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => '   كرار حيدر غالب كاظم', 'employee_number' => 19313, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
    ['name' => 'يسر ثابت جعاز جواد', 'employee_number' => 28595, 'job_title' => 'النظافة العامة', 'unit_id' => 1, 'role' => 'موظف'],
          
// المنشآت الصحية
['name' => 'علي شمخي جابر طياح', 'employee_number' => 1956, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'احمد حسن عبيد بداي', 'employee_number' => 1872, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'مهلهل صبيح بر يسم عسكر', 'employee_number' => 10332, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'أمجد قاسم خدران مهول', 'employee_number' => 10729, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'أيسر محمود عبد الله محمد', 'employee_number' => 1938, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'عباس مهدي كاظم جاسم', 'employee_number' => 1887, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'حمودي مكي علي كاظم', 'employee_number' => 9193, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'علي باكيت عواد شبعان', 'employee_number' => 1871, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'علي ميثم علوان عزيز', 'employee_number' => 2012, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'كاظم محسن جبار فرج', 'employee_number' => 1828, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'كرار علي حسين حسن', 'employee_number' => 1775, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'كرار منعم عبد الكريم دريس', 'employee_number' => 1848, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'سيف سعد محمد صالح', 'employee_number' => 10419, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'حاتم عبد الكريم هادي سلمان', 'employee_number' => 9189, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'محمد محسن حسين علي', 'employee_number' => 1858, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'محمد عماد كاظم شدهان', 'employee_number' => 13798, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'مشير مظهر مال الله سلمان', 'employee_number' => 1946, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'ميثاق هادي جابر عبد', 'employee_number' => 1952, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'وهاب فرحان جابر جريو', 'employee_number' => 1953, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'مرتضى يحيى نجــم عبد حسون', 'employee_number' => 15721, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'مالك عبد الحسين جواد كاظم', 'employee_number' => 26485, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
['name' => 'علي محي بليل عباس', 'employee_number' => 1940, 'job_title' => 'المنشآت الصحية', 'unit_id' => 2, 'role' => 'موظف'],
        ];

       foreach ($employees as $index => $employee) {
            Employee::create([
                'name'            => $employee['name'],
                'email'           => 'emp' . ($index + 1) . '@example.com',
                'password'        => Hash::make('password'),
                'job_title'       => $employee['job_title'],
                'employee_number' => $employee['employee_number'],
                'unit_id'         => $employee['unit_id'],
                'role'            => $employee['role'],
                'is_active'       => true,
            ]);
        }
    }
}