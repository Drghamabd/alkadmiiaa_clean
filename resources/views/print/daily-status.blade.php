<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الموقف اليومي - {{ \Carbon\Carbon::parse($record->date)->format('Y-m-d') }}</title>
    <style>
        @page { size: A4; margin: 10mm; }
        body { font-family: 'Arial', sans-serif; line-height: 1.4; color: #000; margin: 0; padding: 0; font-size: 13px; } /* تقليل ارتفاع السطر وحجم الخط الأساسي */
        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 5mm;
            border: 1px solid #ccc; /* <--- إضافة إطار كامل للصفحة هنا */
            box-sizing: border-box; /* لضمان عدم زيادة الحجم الكلي مع الحدود */
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .header .logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-left: 10px; /* مسافة بين الشعار والنص */
        }
        .header .text-content {
            flex-grow: 1;
            text-align: center;
        }
        .title { font-size: 18px; font-weight: bold; margin: 0; }
        .subtitle { font-size: 14px; margin: 2px 0; color: #555; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #e6e6e6;
            font-weight: bold;
        }
        .table-title {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
            margin-top: 12px;
            margin-bottom: 5px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
            color: #333;
        }
        .two-column-tables {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap; /* يسمح بالالتفاف إذا لم يتسع العرض */
            margin-bottom: 10px;
        }
        .two-column-tables > div {
            width: 49%; /* تقريبا نصف العرض لكل جدول */
            box-sizing: border-box; /* لضمان عدم تجاوز العرض المحدد */
        }
        .two-column-tables table {
            margin: 0;
        }

        /* ** تنسيقات التوقيعات الجديدة (مسؤول يمين، منظم يسار) ** */
        .signatures-container {
            margin-top: 25px;
            overflow: hidden; /* Clearfix for floats */
            width: 100%; /* تأكد أنها تأخذ العرض الكامل */
            display: flex; /* استخدام Flexbox لتوزيع العناصر */
            justify-content: space-between; /* توزيع العناصر على الأطراف */
            align-items: flex-end; /* محاذاة العناصر إلى الأسفل */
        }
        .signature-block {
            width: 48%; /* ضبط العرض لكل كتلة توقيع */
            margin-top: 10px;
            font-size: 12px;
            padding: 5px; /* إضافة حشوة حول الكتلة */
            box-sizing: border-box;
            /* Flexbox items don't need float, justify-content handles positioning */
        }
        .responsible-signature {
            text-align: right; /* مسؤول شعبة الخدمية في اليمين */
        }
        .organizer-signature {
            text-align: left; /* منظم الموقف في اليسار */
        }
        .signature-line {
            margin-top: 10px; /* مسافة بين النص وسطر التوقيع */
        }

        .department { text-align: center; margin-top: 10px; font-weight: bold; }

        /* طباعة CSS */
        @media print {
            .no-print { display: none; }
            body { font-size: 12px; } /* حجم خط أصغر للطباعة */
            table { font-size: 11px; }
            th, td { padding: 3px; }
            .header { margin-bottom: 10px; }
            .title { font-size: 16px; }
            .subtitle { font-size: 12px; }
            .table-title { font-size: 13px; margin-top: 8px; }
            .two-column-tables { flex-wrap: nowrap; } /* تمنع الالتفاف في الطباعة لتبقى جنبًا إلى جنب */
            .signature-block { width: 48%; } /* الحفاظ على العرض للطباعة */
        }
    </style>
</head>
<body>
<div class="container" lang="ar" dir="rtl">

    <div class="header">
        <img src="{{ asset('images/logo.png') }}"
             alt="شعار المدينة"
             class="logo"
             onerror="this.onerror=null; this.src='https://placehold.co/60x60/FF0000/FFFFFF?text=خطأ+شعار';"
             title="إذا لم يظهر الشعار، تأكد من مسار الصورة في مجلد public/images">
        <div class="text-content">
            <div class="title">الموقف اليومي للموظفين</div>
            <div class="subtitle">قسم مدينة الإمام الحسين (ع) للزائرين</div>
            <div class="subtitle">الموقف الخاص بالشعبة الخدمية</div>
        </div>
    </div>

    <table>
        <tr>
            <td colspan="2">اليوم: {{ $record->day_name }}</td>
            <td colspan="2">التاريخ: {{ $record->hijri_date }} ({{ \Carbon\Carbon::parse($record->date)->format('d/m/Y') }})</td>
        </tr>
    </table>

    {{-- حاوية للجداول ذات العمودين: الإجازات الدورية والسنوية --}}
    <div class="two-column-tables">
        @if (!empty($record->periodic_leaves))
        <div>
            <div class="table-title">الإجازات الدورية</div>
            <table>
                <thead>
                    <tr>
                        <th>م</th>
                        <th>الاسم</th>
                        <th>الرقم الوظيفي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($record->periodic_leaves as $index => $leave)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $leave['employee_name'] ?? '' }}</td>
                        <td>{{ $leave['employee_number'] ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if (!empty($record->annual_leaves))
        <div>
            <div class="table-title">الإجازات السنوية</div>
            <table>
                <thead>
                    <tr>
                        <th>م</th>
                        <th>الاسم</th>
                        <th>الرقم الوظيفي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($record->annual_leaves as $index => $leave)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $leave['employee_name'] ?? '' }}</td>
                        <td>{{ $leave['employee_number'] ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <div class="two-column-tables"> {{-- حاوية جديدة لإجازات الأعياد واستراحة الخفر --}}
        @if (!empty($record->eid_leaves))
        <div>
            <div class="table-title">إجازات الأعياد</div>
            <table>
                <thead>
                    <tr>
                        <th>م</th>
                        <th>نوع العيد</th>
                        <th>الاسم</th>
                        <th>الرقم الوظيفي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($record->eid_leaves as $index => $leave)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @php
                                $eidType = $leave['eid_type'] ?? '';
                                echo match ($eidType) {
                                    'eid_alfitr' => 'عيد الفطر',
                                    'eid_aladha' => 'عيد الأضحى',
                                    'eid_algahdir' => 'عيد الغدير',
                                    default => $eidType
                                };
                            @endphp
                        </td>
                        <td>{{ $leave['employee_name'] ?? '' }}</td>
                        <td>{{ $leave['employee_number'] ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- استراحة خفر (الآن بدون حقل الاستخدام المدمج) --}}
        @if (!empty($record->guard_rest))
        <div>
            <div class="table-title">استراحة خفر</div>
            <table>
                <thead>
                    <tr>
                        <th>م</th>
                        <th>الاسم</th>
                        <th>الرقم الوظيفي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($record->guard_rest as $index => $rest)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $rest['employee_name'] ?? '' }}</td>
                        <td>{{ $rest['employee_number'] ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    {{-- نهاية حاوية الجداول ذات العمودين الجديدة --}}

    {{-- جدول جديد للاستخدامات المخصصة --}}
    @if (!empty($record->custom_usages))
    <div class="table-title">الاستخدام </div>
    <table>
        <thead>
            <tr>
                <th>م</th>
                <th>الاسم</th>
                <th>الرقم الوظيفي</th>
                <th>تفاصيل الاستخدام</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->custom_usages as $index => $usage)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $usage['employee_name'] ?? '' }}</td>
                <td>{{ $usage['employee_number'] ?? '' }}</td>
                <td>{{ $usage['usage_details'] ?? '&mdash;' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- الجداول الأخرى (تظهر بشكل عمودي) --}}

    @if (!empty($record->temporary_leaves))
    <div class="table-title">الإجازات الزمنية</div>
    <table>
        <thead>
            <tr>
                <th>م</th>
                <th>الاسم</th>
                <th>الرقم الوظيفي</th>
                <th>الوقت</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->temporary_leaves as $index => $leave)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $leave['employee_name'] ?? '' }}</td>
                <td>{{ $leave['employee_number'] ?? '' }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($leave['from_time'])->format('H:i') ?? '' }} -
                    {{ \Carbon\Carbon::parse($leave['to_time'])->format('H:i') ?? '' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if (!empty($record->bereavement_leaves))
    <div class="table-title">إجازة الوفاة</div>
    <table>
        <thead>
            <tr>
                <th>م</th>
                <th>الاسم</th>
                <th>الرقم الوظيفي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->bereavement_leaves as $index => $leave)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $leave['employee_name'] ?? '' }}</td>
                <td>{{ $leave['employee_number'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if (!empty($record->unpaid_leaves))
    <div class="table-title">إجازة بدون راتب</div>
    <table>
        <thead>
            <tr>
                <th>م</th>
                <th>الاسم</th>
                <th>الرقم الوظيفي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->unpaid_leaves as $index => $leave)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $leave['employee_name'] ?? '' }}</td>
                <td>{{ $leave['employee_number'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if (!empty($record->absences))
    <div class="table-title">الغياب</div>
    <table>
        <thead>
            <tr>
                <th>م</th>
                <th>الاسم</th>
                <th>الرقم الوظيفي</th>
                <th>من تاريخ</th>
                <th>إلى تاريخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->absences as $index => $absence)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $absence['employee_name'] ?? '' }}</td>
                <td>{{ $absence['employee_number'] ?? '' }}</td>
                <td>{{ \Carbon\Carbon::parse($absence['from_date'])->format('Y-m-d') ?? '' }}</td>
                <td>{{ \Carbon\Carbon::parse($absence['to_date'])->format('Y-m-d') ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if (!empty($record->long_leaves))
    <div class="table-title">الإجازات الطويلة</div>
    <table>
        <thead>
            <tr>
                <th>م</th>
                <th>الاسم</th>
                <th>الرقم الوظيفي</th>
                <th>من تاريخ</th>
                <th>إلى تاريخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->long_leaves as $index => $leave)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $leave['employee_name'] ?? '' }}</td>
                <td>{{ $leave['employee_number'] ?? '' }}</td>
                <td>{{ \Carbon\Carbon::parse($leave['from_date'])->format('Y-m-d') ?? '' }}</td>
                <td>{{ \Carbon\Carbon::parse($leave['to_date'])->format('Y-m-d') ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if (!empty($record->sick_leaves))
    <div class="table-title">الإجازات المرضية</div>
    <table>
        <thead>
            <tr>
                <th>م</th>
                <th>الاسم</th>
                <th>الرقم الوظيفي</th>
                <th>من تاريخ</th>
                <th>إلى تاريخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->sick_leaves as $index => $leave)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $leave['employee_name'] ?? '' }}</td>
                <td>{{ $leave['employee_number'] ?? '' }}</td>
                <td>{{ \Carbon\Carbon::parse($leave['from_date'])->format('Y-m-d') ?? '' }}</td>
                <td>{{ \Carbon\Carbon::parse($leave['to_date'])->format('Y-m-d') ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <table>
        @php
            // استرجاع total_required من السجل إذا كان موجودًا، وإلا فاستخدم القيمة الافتراضية 86
            $totalRequired = $record->total_required ?? 86;
            $totalEmployees = \App\Models\Employee::where('is_active', 1)->count();
            $shortage = $totalRequired - $totalEmployees;

            $paidLeavesCount = count($record->annual_leaves ?? [])
                               + count($record->periodic_leaves ?? [])
                               + count($record->sick_leaves ?? [])
                               + count($record->bereavement_leaves ?? [])
                               + count($record->eid_leaves ?? []);

            $unpaidLeavesCount = count($record->unpaid_leaves ?? []);
            $absencesCount = count($record->absences ?? []);
            $temporaryLeavesCount = count($record->temporary_leaves ?? []);
            $guardRestCount = count($record->guard_rest ?? []);
            $customUsagesCount = count($record->custom_usages ?? []); // إضافة عدد الاستخدامات المخصصة

            // تم تعديل حساب الحضور الفعلي ليشمل guard_rest و custom_usages
            $actualAttendance = $totalEmployees - ($paidLeavesCount + $unpaidLeavesCount + $absencesCount + $temporaryLeavesCount + $guardRestCount + $customUsagesCount);

        @endphp
        <tr>
            <th>الملاك</th>
            <th>الموجود الحالي</th>
            <th>النقص</th>
            <th>الحضور الفعلي</th>
            <th>إجازات براتب</th>
            <th>إجازات بدون راتب</th>
            <th>الغياب</th>
            <th>استراحة خفر</th>
            <th>إجازات زمنية</th>
            <th>تعيين</th>
            <th>نقل</th>
            <th>فصل</th>
        </tr>
        <tr>
            <td>{{ $totalRequired }}</td>
            <td>{{ $totalEmployees }}</td>
            <td>{{ $shortage }}</td>
            <td>{{ $actualAttendance }}</td>
            <td>{{ $paidLeavesCount }}</td>
            <td>{{ $unpaidLeavesCount }}</td>
            <td>{{ $absencesCount }}</td>
            <td>{{ $guardRestCount }}</td>
            <td>{{ $temporaryLeavesCount }}</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
        </tr>
    </table>

    <div class="signatures-container">
        {{-- مسؤول شعبة الخدمية في اليمين --}}
        <div class="signature-block responsible-signature">
            <div>مسؤول شعبة الخدمية</div>
            <div class="signature-line">
                <div>التوقيع: ........................</div>
                <div>التاريخ: {{ \Carbon\Carbon::parse($record->date)->format('d/m/Y') }}</div>
            </div>
        </div>

        {{-- منظم الموقف في اليسار --}}
        @if (!empty($record->organizer_employee_name))
        <div class="signature-block organizer-signature">
            <div>منظم الموقف: {{ $record->organizer_employee_name }}</div>
            <div class="signature-line">
                <div>التوقيع: ........................</div>
            </div>
        </div>
        @endif
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
            طباعة التقرير
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">
            إغلاق النافذة
        </button>
    </div>

    <script>
        // طباعة الصفحة تلقائيًا بعد تحميلها
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500); // تأخير بسيط لضمان تحميل كل العناصر
        };
    </script>
</div>
</body>
</html>
