@extends('master')

@section('title')
    تقرير أقساط العملاء
@stop

@section('css')
    <!-- Google Fonts - Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">

    <style>
        /* تحسينات Select2 لإزالة العناصر غير المرغوبة */
        .select2-container {
            display: block !important;
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: auto !important;
            min-height: 48px !important;
            border: 2px solid #e9ecef !important;
            border-radius: 12px !important;
            padding: 0.5rem 1rem !important;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
        }

        .select2-dropdown {
            border: 2px solid #e9ecef !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            max-height: 300px !important;
            overflow-y: auto !important;
        }

        .select2-results {
            max-height: 300px !important;
            overflow-y: auto !important;
        }

        .select2-results__options {
            max-height: 300px !important;
            overflow-y: auto !important;
        }

        .select2-results__option {
            padding: 0.5rem 1rem !important;
        }

        .select2-results__option--highlighted {
            background-color: #0d6efd !important;
        }

        /* إخفاء أي عناصر إضافية قد تظهر */
        .select2-container + .select2-container,
        .select2-container ~ .select2-dropdown {
            display: none !important;
        }

        /* تحسين المظهر العام */
        .select2-selection__rendered {
            color: #495057 !important;
            line-height: 1.5 !important;
        }

        .select2-selection__placeholder {
            color: #6c757d !important;
        }

        .select2-selection__arrow {
             height: 46px !important;
             right: 10px !important;
         }

         /* CSS مخصص للـ dropdown */
         .select2-dropdown-custom {
             max-height: 300px !important;
             overflow-y: auto !important;
         }

         .select2-dropdown-custom .select2-results {
             max-height: 300px !important;
             overflow-y: auto !important;
         }

         .select2-dropdown-custom .select2-results__options {
             max-height: 300px !important;
             overflow-y: auto !important;
         }

         /* تحسين شريط التمرير */
         .select2-results::-webkit-scrollbar {
             width: 8px;
         }

         .select2-results::-webkit-scrollbar-track {
             background: #f1f1f1;
             border-radius: 4px;
         }

         .select2-results::-webkit-scrollbar-thumb {
             background: #c1c1c1;
             border-radius: 4px;
         }

         .select2-results::-webkit-scrollbar-thumb:hover {
             background: #a8a8a8;
         }

         /* إخفاء رسائل التحديد غير المرغوبة */
         .select2-selection__limit,
         .select2-selection__choice__remove {
             display: none !important;
         }

         .select2-container--bootstrap-5 .select2-selection__choice {
             display: none !important;
         }

         /* إعدادات خاصة للجوال */
         @media (max-width: 768px) {
             .select2-search--dropdown {
                 display: block !important;
             }

             .select2-search__field {
                 display: block !important;
                 width: 100% !important;
                 padding: 8px 12px !important;
                 border: 1px solid #ddd !important;
                 border-radius: 4px !important;
                 font-size: 16px !important; /* منع التكبير في iOS */
                 -webkit-appearance: none !important;
                 appearance: none !important;
             }

             .select2-dropdown {
                 position: fixed !important;
                 top: 50% !important;
                 left: 50% !important;
                 transform: translate(-50%, -50%) !important;
                 width: 90% !important;
                 max-width: 400px !important;
                 z-index: 99999 !important;
             }

             .select2-container--open .select2-dropdown {
                 border: 2px solid #007bff !important;
                 box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
             }
         }

         /* إصلاح عرض التفاصيل القابلة للطي */
         .installment-main-row.has-details {
             transition: all 0.3s ease;
             border-left: 4px solid transparent;
             position: relative;
         }

         .installment-main-row.has-details:hover {
             background-color: #f8f9fa;
             border-left-color: #0d6efd;
             transform: translateX(-2px);
         }

         .installment-main-row.has-details::after {
             content: "";
             position: absolute;
             left: 0;
             top: 0;
             bottom: 0;
             width: 3px;
             background: linear-gradient(45deg, #007bff, #0056b3);
             opacity: 0;
             transition: opacity 0.3s ease;
         }

         .installment-main-row.has-details:hover::after {
             opacity: 1;
         }

         /* تحسين أيقونة السهم */
         .collapse-icon {
             transition: transform 0.3s ease;
             font-size: 0.875rem;
             width: 16px;
             height: 16px;
             display: inline-flex;
             align-items: center;
             justify-content: center;
             cursor: pointer;
             color: #007bff;
         }

         .collapse-icon:hover {
             color: #0056b3;
             transform: scale(1.1);
         }

         .collapse-icon.rotated {
             transform: rotate(180deg);
         }

         .collapse-icon.rotated:hover {
             transform: rotate(180deg) scale(1.1);
         }

         /* تحسين صفوف التفاصيل */
         .collapse {
             transition: all 0.3s ease;
         }

         .collapse.show {
             animation: slideDown 0.4s ease-out;
         }

         @keyframes slideDown {
             from {
                 opacity: 0;
                 transform: translateY(-10px);
             }
             to {
                 opacity: 1;
                 transform: translateY(0);
             }
         }

         /* تحسين كارت التفاصيل */
         .installment-details-card {
             background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
             border-radius: 0.75rem;
             margin: 0.5rem;
             box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
             border: 2px solid #dee2e6;
             overflow: hidden;
         }

         /* تحسين أقسام التفاصيل */
         .detail-section {
             background: white;
             border-radius: 0.75rem;
             padding: 1.25rem;
             box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
             border: 2px solid #e9ecef;
             height: 100%;
             transition: all 0.3s ease;
         }

         .detail-section:hover {
             box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
             transform: translateY(-2px);
         }

         .detail-title {
             color: #495057;
             font-size: 1rem;
             font-weight: 700;
             margin-bottom: 1rem;
             padding-bottom: 0.75rem;
             border-bottom: 3px solid #e9ecef;
             display: flex;
             align-items: center;
         }

         .detail-item {
             margin-bottom: 0.75rem;
             font-size: 0.9rem;
             line-height: 1.5;
             display: flex;
             justify-content: space-between;
             align-items: center;
         }

         .detail-item strong {
             color: #6c757d;
             font-weight: 700;
             margin-left: 0.5rem;
         }

         /* تحسين قسم الأقساط الفرعية */
         .sub-installments-section {
             margin-top: 1rem;
             padding: 1.5rem;
             background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
             border-radius: 0.75rem;
             border: 2px solid #dee2e6;
             box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
         }

         .sub-installments-title {
             color: #495057;
             font-size: 1rem;
             font-weight: 700;
             margin-bottom: 1rem;
             padding-bottom: 0.75rem;
             border-bottom: 3px solid #dee2e6;
             display: flex;
             align-items: center;
             justify-content: space-between;
         }

         /* تحسين جدول التفاصيل */
         .sub-installments-table {
             background: white;
             border-radius: 0.75rem;
             overflow: hidden;
             box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
             border: 2px solid #e9ecef;
             table-layout: fixed;
             width: 100%;
         }

         .sub-installments-table thead th {
             background: linear-gradient(135deg, #495057 0%, #343a40 100%);
             color: white;
             font-weight: 700;
             font-size: 0.8rem;
             border: none;
             padding: 1rem 0.75rem;
             text-align: center;
             position: sticky;
             top: 0;
             z-index: 10;
             text-transform: uppercase;
             letter-spacing: 0.5px;
         }

         .sub-installments-table tbody td {
             font-size: 0.85rem;
             padding: 0.875rem 0.75rem;
             border-bottom: 1px solid #e9ecef;
             vertical-align: middle;
             transition: background-color 0.2s ease;
             word-wrap: break-word;
             overflow-wrap: break-word;
         }

         .sub-installments-table tbody .detail-row:hover {
             background-color: #f8f9fa;
             transform: scale(1.01);
             box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
         }

         .sub-installments-table tbody tr:last-child td {
             border-bottom: none;
         }

         /* تنسيق صف الإجمالي في جدول التفاصيل */
         .sub-installments-table .table-info {
             background: linear-gradient(135deg, #cff4fc 0%, #b6effb 100%) !important;
             border-top: 2px solid #0dcaf0;
         }

         .sub-installments-table .table-info td {
             font-weight: 700;
             border-bottom: none;
             padding: 1rem 0.75rem;
         }

         /* تحسين حالة القسط */
         .status-badge {
             padding: 0.5rem 0.875rem;
             border-radius: 20px;
             font-weight: 600;
             font-size: 0.8rem;
             text-transform: uppercase;
             letter-spacing: 0.5px;
             box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
             border: 2px solid transparent;
             display: inline-block;
             min-width: 80px;
             text-align: center;
         }

         .status-paid {
             background: linear-gradient(135deg, #d1e7dd 0%, #a3cfbb 100%);
             color: #0f5132;
             border-color: #badbcc;
         }

         .status-pending {
             background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
             color: #664d03;
             border-color: #ffdf7e;
         }

         .status-overdue {
             background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
             color: #721c24;
             border-color: #f5c2c7;
         }

         .status-partial {
             background: linear-gradient(135deg, #d3edfa 0%, #9eeaf9 100%);
             color: #055160;
             border-color: #b8daff;
         }

         /* تحسين المبالغ */
         .amount-positive {
             color: #198754;
             font-weight: 700;
             font-family: 'Courier New', monospace;
         }

         .amount-negative {
             color: #dc3545;
             font-weight: 700;
             font-family: 'Courier New', monospace;
         }

         .amount-neutral {
             color: #6c757d;
             font-weight: 600;
             font-family: 'Courier New', monospace;
         }

         /* تحسين الأفاتار */
         .avatar-sm {
             width: 40px;
             height: 40px;
             font-size: 0.875rem;
             box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
         }

         /* تحسين الحالة الفارغة */
         .empty-state {
             padding: 3rem 2rem;
         }

         .empty-state i {
             opacity: 0.5;
         }

         /* تحسين الكود */
         code.small {
             background-color: #f8f9fa;
             border: 1px solid #dee2e6;
             border-radius: 4px;
             padding: 0.25rem 0.5rem;
             font-size: 0.75rem;
             color: #6f42c1;
         }

         /* تحسين شريط التمرير للجدول */
         .table-responsive {
             max-height: 500px;
             overflow-y: auto;
             border-radius: 0.75rem;
         }

         .table-responsive::-webkit-scrollbar {
             width: 8px;
             height: 8px;
         }

         .table-responsive::-webkit-scrollbar-track {
             background: #f1f1f1;
             border-radius: 4px;
         }

         .table-responsive::-webkit-scrollbar-thumb {
             background: #c1c1c1;
             border-radius: 4px;
         }

         .table-responsive::-webkit-scrollbar-thumb:hover {
             background: #a8a8a8;
         }

         /* تحسين الرسوم المتحركة */
         @keyframes fadeIn {
             from {
                 opacity: 0;
                 transform: translateY(10px);
             }
             to {
                 opacity: 1;
                 transform: translateY(0);
             }
         }

         .detail-row {
             animation: fadeIn 0.3s ease-out;
         }

         /* تحسين صف الإجمالي العام */
         .table-grand-total {
             background: linear-gradient(135deg, #e7f3ff 0%, #cce7ff 100%) !important;
             border-top: 3px solid #0d6efd;
             font-size: 1rem;
         }

         .table-grand-total td {
             padding: 1.25rem 0.75rem;
             font-weight: 700;
             color: #0d6efd;
             border-bottom: none;
         }

         /* تحسين أزرار التحكم السريع */
         #quickControls {
             background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
             border-bottom: 2px solid #dee2e6;
         }

         #quickControls .btn {
             font-size: 0.875rem;
             padding: 0.5rem 1rem;
             border-radius: 8px;
             transition: all 0.3s ease;
         }

         #quickControls .btn:hover {
             transform: translateY(-2px);
             box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
         }

         /* تحسين responsive */
         @media (max-width: 768px) {
             .sub-installments-section {
                 padding: 1rem;
                 margin-top: 0.5rem;
             }

             .detail-section {
                 padding: 1rem;
                 margin-bottom: 1rem;
             }

             .sub-installments-table thead th {
                 padding: 0.75rem 0.5rem;
                 font-size: 0.7rem;
             }

             .sub-installments-table tbody td {
                 padding: 0.75rem 0.5rem;
                 font-size: 0.8rem;
             }

             .installment-details-card {
                 margin: 0.25rem;
             }

             .collapse-icon {
                 font-size: 0.75rem;
             }

             .status-badge {
                 font-size: 0.7rem;
                 padding: 0.375rem 0.5rem;
                 min-width: 70px;
             }

             .detail-item {
                 flex-direction: column;
                 align-items: flex-start;
                 margin-bottom: 0.5rem;
             }

             .detail-item strong {
                 margin-left: 0;
                 margin-bottom: 0.25rem;
             }
         }

         /* تحسين الطباعة */
         @media print {
             .sub-installments-table {
                 font-size: 0.7rem;
                 break-inside: avoid;
             }

             .status-badge {
                 border: 1px solid #333 !important;
                 background: white !important;
                 color: #333 !important;
             }

             .collapse-icon,
             #quickControls {
                 display: none !important;
             }

             .installment-details-card {
                 break-inside: avoid;
                 page-break-inside: avoid;
             }

             .collapse {
                 display: block !important;
                 height: auto !important;
             }
         }

         /* تحسين التركيز للوصولية */
         .installment-main-row.has-details:focus,
         .installment-main-row.has-details:focus-within {
             outline: 2px solid #0d6efd;
             outline-offset: 2px;
             background-color: #f8f9fa;
         }

         /* تحسين المظهر العام للجدول */
         .table-modern {
             margin-bottom: 0;
             border-collapse: separate;
             border-spacing: 0;
         }

         .table-modern thead th {
             background: linear-gradient(135deg, #495057 0%, #343a40 100%);
             color: white;
             font-weight: 700;
             font-size: 0.875rem;
             border: none;
             padding: 1rem 0.75rem;
             text-align: center;
             position: sticky;
             top: 0;
             z-index: 10;
             vertical-align: middle;
         }

         .table-modern tbody td {
             padding: 0.875rem 0.75rem;
             border-bottom: 1px solid #e9ecef;
             vertical-align: middle;
             text-align: center;
         }

         .table-modern tbody tr {
             transition: all 0.2s ease;
         }

         .table-modern tbody tr:hover {
             background-color: rgba(13, 110, 253, 0.05);
         }
     </style>
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="slide-in">
                        <i class="fas fa-calendar-alt me-3"></i>
                        تقرير أقساط العملاء
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">تقرير أقساط العملاء</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Filters Section -->
        <div class="card-modern fade-in">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>
                    فلاتر التقرير
                </h5>
            </div>
            <div class="card-body-modern">
                <form id="reportForm">
                    <div class="row g-4">
                        <!-- First Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-user me-2"></i>العميل</label>
                            <select name="client" id="client" class="form-control select2">
                                <option value="">جميع العملاء</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->trade_name ?? $client->first_name . ' ' . $client->last_name }}-{{ $client->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-user-tie me-2"></i>الموظف</label>
                            <select name="employee" id="employee" class="form-control select2">
                                <option value="">جميع الموظفين</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-building me-2"></i>الفرع</label>
                            <select name="branch" id="branch" class="form-control select2">
                                <option value="">جميع الفروع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-tags me-2"></i>تصنيف العميل</label>
                            <select name="client_category" id="client_category" class="form-control select2">
                                <option value="">جميع التصنيفات</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-check-circle me-2"></i>حالة القسط</label>
                            <select name="installment_status" id="installment_status" class="form-control select2">
                                <option value="">جميع الحالات</option>
                                <option value="paid">مدفوع</option>
                                <option value="pending">قيد الانتظار</option>
                                <option value="overdue">متأخر</option>
                                <option value="partial">مدفوع جزئياً</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-calendar me-2"></i>نوع التاريخ</label>
                            <select name="date_type" id="date_type" class="form-control select2">
                                <option value="custom">مخصص</option>
                                <option value="today">اليوم</option>
                                <option value="yesterday">أمس</option>
                                <option value="this_week">هذا الأسبوع</option>
                                <option value="last_week">الأسبوع الماضي</option>
                                <option value="this_month">هذا الشهر</option>
                                <option value="last_month">الشهر الماضي</option>
                                <option value="this_year">هذا العام</option>
                                <option value="last_year">العام الماضي</option>
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="col-lg-3 col-md-6" id="custom_dates">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>من تاريخ</label>
                            <input type="date" name="date_from" id="date_from" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6" id="custom_to_date">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>إلى تاريخ</label>
                            <input type="date" name="date_to" id="date_to" class="form-control">
                        </div>

                        <!-- Options Row -->
                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="show-overdue-only" name="show_overdue_only" class="form-check-input">
                                <label for="show-overdue-only" class="form-check-label">
                                    <i class="fas fa-exclamation-triangle me-2"></i>الأقساط المتأخرة فقط
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="group-by-client" name="group_by_client" class="form-check-input">
                                <label for="group-by-client" class="form-check-label">
                                    <i class="fas fa-users me-2"></i>تجميع حسب العميل
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-lg-6 col-md-12 align-self-end">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn-modern btn-primary-modern" id="filterBtn">
                                    <i class="fas fa-search"></i>
                                    تصفية التقرير
                                </button>
                                <button type="button" class="btn-modern btn-outline-modern" id="resetBtn">
                                    <i class="fas fa-refresh"></i>
                                    إعادة تعيين
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card-modern no-print fade-in">
            <div class="card-body-modern">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn-modern btn-success-modern" id="exportExcel">
                            <i class="fas fa-file-excel"></i>
                            تصدير إكسل
                        </button>
                        <button class="btn-modern btn-warning-modern" id="printBtn">
                            <i class="fas fa-print"></i>
                            طباعة
                        </button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn-modern btn-primary-modern active" id="summaryViewBtn">
                            <i class="fas fa-chart-pie"></i>
                            ملخص
                        </button>
                        <button type="button" class="btn-modern btn-outline-modern" id="detailViewBtn">
                            <i class="fas fa-list"></i>
                            تفاصيل
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4 fade-in" id="totalsSection">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card primary">
                    <div class="stats-icon primary">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stats-value" id="totalInstallments">0</div>
                    <div class="stats-label">إجمالي الأقساط</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stats-value" id="totalAmount">0</div>
                    <div class="stats-label">إجمالي المبلغ (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-value" id="pendingAmount">0</div>
                    <div class="stats-label">المبلغ المتبقي (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card danger">
                    <div class="stats-icon danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stats-value" id="overdueAmount">0</div>
                    <div class="stats-label">المبلغ المتأخر (ريال)</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني لأقساط العملاء
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="installmentChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <div class="card-modern fade-in" id="reportContainer" style="position: relative;">
            <!-- Loading Overlay -->
            <div class="loading-overlay">
                <div class="spinner"></div>
            </div>

            <div class="card-header-modern">
                <h5 class="mb-0" id="reportTitle">
                    <i class="fas fa-table me-2"></i>
                    تقرير أقساط العملاء
                </h5>
            </div>

            <!-- Quick Controls for Expand/Collapse All -->
            <div id="quickControls" class="p-3 border-bottom">
                <div class="d-flex gap-2 mb-3">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="expandAllDetails">
                        <i class="fas fa-expand-alt me-1"></i>توسيع الكل
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="collapseAllDetails">
                        <i class="fas fa-compress-alt me-1"></i>طي الكل
                    </button>
                </div>
            </div>

            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>#</th>
                                <th><i class="fas fa-file-invoice me-2"></i>رقم الفاتورة</th>
                                <th><i class="fas fa-user me-2"></i>اسم العميل</th>
                                <th><i class="fas fa-building me-2"></i>الفرع</th>
                                <th><i class="fas fa-user-tie me-2"></i>الموظف</th>
                                <th><i class="fas fa-hashtag me-2"></i>رقم القسط</th>
                                <th><i class="fas fa-money-bill-wave me-2"></i>مبلغ القسط</th>
                                <th><i class="fas fa-calendar-alt me-2"></i>تاريخ الاستحقاق</th>
                                <th><i class="fas fa-check-circle me-2"></i>حالة القسط</th>
                                <th><i class="fas fa-money-check me-2"></i>المبلغ المدفوع</th>
                                <th><i class="fas fa-clock me-2"></i>المبلغ المتبقي</th>
                                <th><i class="fas fa-calendar-times me-2"></i>أيام التأخير</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <!-- سيتم تحديث البيانات هنا عبر AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <script>
        let installmentChart;

        $(document).ready(function() {
            // تهيئة Select2 مع إعدادات محسنة للجوال
            $('.select2').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
                language: {
                    noResults: function() {
                        return "لا توجد نتائج";
                    },
                    searching: function() {
                        return "جاري البحث...";
                    },
                    loadingMore: function() {
                        return "جاري تحميل المزيد...";
                    }
                },
                allowClear: true,
                width: '100%',
                dropdownParent: $('body'),
                minimumResultsForSearch: 0,
                placeholder: function() {
                    return $(this).data('placeholder') || 'اختر...';
                },
                dropdownCssClass: 'select2-dropdown-custom',
                adaptContainerCssClass: function(clazz) {
                    return clazz;
                },
                adaptDropdownCssClass: function(clazz) {
                    return clazz;
                },
                minimumInputLength: 0,
                closeOnSelect: true,
                selectOnClose: false
            });

            // تخصيص تصميم Select2 وإزالة العناصر غير المرغوبة
            $('.select2-container--bootstrap-5 .select2-selection--single').css({
                'border': '2px solid var(--gray-200)',
                'border-radius': '12px',
                'height': 'auto',
                'padding': '0.5rem 1rem',
                'min-height': '48px'
            });

            // إزالة أي عناصر إضافية قد تظهر تحت Select2
            $('.select2-container').each(function() {
                $(this).next('.select2-dropdown').remove();
                $(this).siblings('.select2-dropdown').remove();
            });

            // منع ظهور dropdown في مكان خاطئ
            $('.select2').on('select2:open', function() {
                $('.select2-dropdown').css({
                    'position': 'absolute',
                    'z-index': '9999'
                });
            });

            // تحميل البيانات الأولية
            loadReportData();

            // Add animation classes on load
            setTimeout(() => {
                $('.fade-in').addClass('animate__animated animate__fadeInUp');
            }, 100);

            // ==== إصلاح مشكلة عرض تفاصيل الأقساط ====

            // 1. التأكد من أن Bootstrap Collapse يعمل بشكل صحيح
            function initializeCollapseHandlers() {
                // إزالة أي معالجات أحداث قديمة لتجنب التداخل
                $(document).off('click', '.installment-main-row.has-details');
                $(document).off('click', '.collapse-icon');

                // إضافة معالج جديد للنقر على الصفوف
                $(document).on('click', '.installment-main-row.has-details', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const targetId = $(this).attr('data-bs-target');
                    console.log('النقر على الصف، الهدف:', targetId);
                    
                    if (!targetId) {
                        console.log('لا يوجد هدف محدد');
                        return;
                    }

                    const $target = $(targetId);
                    const $icon = $(this).find('.collapse-icon');

                    console.log('العنصر المستهدف موجود:', $target.length > 0);
                    console.log('الأيقونة موجودة:', $icon.length > 0);

                    if ($target.length === 0) {
                        console.log('العنصر المستهدف غير موجود:', targetId);
                        return;
                    }

                    // تبديل حالة العرض مع تأثيرات بصرية محسنة
                    if ($target.hasClass('show')) {
                        $target.removeClass('show').fadeOut(200);
                        $icon.removeClass('rotated');
                        console.log('إخفاء التفاصيل');
                    } else {
                        $target.addClass('show').fadeIn(200);
                        $icon.addClass('rotated');
                        console.log('إظهار التفاصيل');
                    }
                });
                
                // إضافة معالج منفصل للنقر على الأيقونة
                $(document).on('click', '.collapse-icon', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).closest('.installment-main-row.has-details').trigger('click');
                });
            }

            // 2. التأكد من أن البيانات تحتوي على التفاصيل
            function validateInstallmentData(installments) {
                console.log('فحص بيانات الأقساط:', installments);

                installments.forEach((installment, index) => {
                    console.log(`القسط ${index + 1}:`, {
                        id: installment.id,
                        hasDetails: installment.details && installment.details.length > 0,
                        detailsCount: installment.details ? installment.details.length : 0,
                        details: installment.details
                    });
                });
            }

            // 3. تحسين دالة تحديث محتوى الجدول مع إصلاحات
            function updateTableBodyFixed(installments, totals) {
                let tableHtml = '';

                if (installments && installments.length > 0) {
                    // التحقق من البيانات
                    validateInstallmentData(installments);

                    let grandTotals = {
                        total_amount: 0,
                        paid_amount: 0,
                        remaining_amount: 0
                    };

                    installments.forEach((installment, index) => {
                        grandTotals.total_amount += parseFloat(installment.amount || 0);
                        grandTotals.paid_amount += parseFloat(installment.paid_amount || 0);
                        grandTotals.remaining_amount += parseFloat(installment.remaining_amount || 0);

                        const statusClass = getStatusClass(installment.status);
                        const statusText = getStatusText(installment.status);
                        const daysOverdue = installment.days_overdue || 0;
                        const overdueClass = daysOverdue > 0 ? 'text-danger fw-bold' : 'text-muted';

                        // تحسين فحص وجود التفاصيل
                        const hasDetails = installment.details && Array.isArray(installment.details) && installment.details.length > 0;
                        const uniqueCollapseId = `details-${installment.id}-${index}`; // معرف فريد

                        console.log(`القسط ${installment.id} له تفاصيل:`, hasDetails, installment.details);

                        // الصف الرئيسي للقسط مع تحسينات
                        tableHtml += `<tr class="installment-main-row ${hasDetails ? 'has-details' : ''}"
                            ${hasDetails ? `data-bs-target="#${uniqueCollapseId}"` : ''}
                            style="${hasDetails ? 'cursor: pointer; background-color: #f8f9fa;' : ''}">`;

                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                ${hasDetails ? `<i class="fas fa-chevron-down me-2 text-primary collapse-icon" id="icon-${uniqueCollapseId}" style="transition: transform 0.3s ease;"></i>` : '<span class="me-4"></span>'}
                                ${index + 1}
                            </div>
                        </td>`;

                        // باقي أعمدة الجدول
                        tableHtml += `<td><span class="badge bg-primary">${installment.invoice_number || 'غير محدد'}</span></td>`;
                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">${installment.client_name || 'غير محدد'}</div>
                                    <small class="text-muted">${installment.client_code || ''}</small>
                                </div>
                            </div>
                        </td>`;
                        tableHtml += `<td>${installment.branch ? `<span class="badge bg-success">${installment.branch}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td>${installment.employee ? `<span class="badge bg-warning">${installment.employee}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info">${installment.installment_number || 1}</span>
                                ${hasDetails ? `<span class="badge bg-secondary ms-2">${installment.details.length} تفصيل</span>` : ''}
                            </div>
                        </td>`;
                        tableHtml += `<td><span class="amount-positive">${formatNumber(installment.amount)}</span></td>`;
                        tableHtml += `<td><span class="fw-bold">${formatDate(installment.due_date)}</span></td>`;
                        tableHtml += `<td><span class="status-badge ${statusClass}">${statusText}</span></td>`;
                        tableHtml += `<td><span class="amount-positive">${formatNumber(installment.paid_amount)}</span></td>`;
                        tableHtml += `<td><span class="amount-negative">${formatNumber(installment.remaining_amount)}</span></td>`;
                        tableHtml += `<td><span class="${overdueClass}">${daysOverdue > 0 ? daysOverdue + ' يوم' : 'في الموعد'}</span></td>`;
                        tableHtml += `</tr>`;

                        // صف التفاصيل القابل للطي - تحسينات مهمة
                        if (hasDetails) {
                            tableHtml += `<tr class="collapse" id="${uniqueCollapseId}">`;
                            tableHtml += `<td colspan="12" class="p-0">`;
                            tableHtml += `<div class="installment-details-card">`;

                            // معلومات القسط الأساسية
                            tableHtml += `<div class="row g-3 p-3 mb-3">`;

                            // تفاصيل الفاتورة
                            tableHtml += `<div class="col-md-4">`;
                            tableHtml += `<div class="detail-section">`;
                            tableHtml += `<h6 class="detail-title"><i class="fas fa-file-invoice me-2"></i>تفاصيل الفاتورة</h6>`;
                            tableHtml += `<div class="detail-item"><strong>رقم الفاتورة:</strong> ${installment.invoice_number || 'غير محدد'}</div>`;
                            tableHtml += `<div class="detail-item"><strong>معرف الفاتورة:</strong> #${installment.invoice_id || 'غير محدد'}</div>`;
                            tableHtml += `<div class="detail-item"><strong>تاريخ الإنشاء:</strong> ${formatDate(installment.due_date)}</div>`;
                            tableHtml += `</div>`;
                            tableHtml += `</div>`;

                            // تفاصيل العميل
                            tableHtml += `<div class="col-md-4">`;
                            tableHtml += `<div class="detail-section">`;
                            tableHtml += `<h6 class="detail-title"><i class="fas fa-user me-2"></i>تفاصيل العميل</h6>`;
                            tableHtml += `<div class="detail-item"><strong>اسم العميل:</strong> ${installment.client_name || 'غير محدد'}</div>`;
                            tableHtml += `<div class="detail-item"><strong>كود العميل:</strong> ${installment.client_code || 'غير محدد'}</div>`;
                            tableHtml += `<div class="detail-item"><strong>الفرع:</strong> ${installment.branch || 'غير محدد'}</div>`;
                            tableHtml += `<div class="detail-item"><strong>الموظف المسؤول:</strong> ${installment.employee || 'غير محدد'}</div>`;
                            tableHtml += `</div>`;
                            tableHtml += `</div>`;

                            // تفاصيل القسط
                            tableHtml += `<div class="col-md-4">`;
                            tableHtml += `<div class="detail-section">`;
                            tableHtml += `<h6 class="detail-title"><i class="fas fa-money-bill-wave me-2"></i>تفاصيل القسط</h6>`;
                            tableHtml += `<div class="detail-item"><strong>رقم القسط:</strong> ${installment.installment_number || 1}</div>`;
                            tableHtml += `<div class="detail-item"><strong>المبلغ الأصلي:</strong> ${formatNumber(installment.amount)} ريال</div>`;
                            tableHtml += `<div class="detail-item"><strong>المبلغ المدفوع:</strong> ${formatNumber(installment.paid_amount)} ريال</div>`;
                            tableHtml += `<div class="detail-item"><strong>المبلغ المتبقي:</strong> ${formatNumber(installment.remaining_amount)} ريال</div>`;
                            tableHtml += `<div class="detail-item"><strong>تاريخ الاستحقاق:</strong> ${formatDate(installment.due_date)}</div>`;
                            tableHtml += `<div class="detail-item"><strong>الحالة:</strong> <span class="status-badge ${statusClass}">${statusText}</span></div>`;
                            if (daysOverdue > 0) {
                                tableHtml += `<div class="detail-item text-danger"><strong>أيام التأخير:</strong> ${daysOverdue} يوم</div>`;
                            }
                            tableHtml += `</div>`;
                            tableHtml += `</div>`;
                            tableHtml += `</div>`; // end main info row

                            // جدول الأقساط الفرعية
                            tableHtml += generateSubInstallmentsTable(installment.details);

                            tableHtml += `</div>`; // end installment-details-card
                            tableHtml += `</td>`;
                            tableHtml += `</tr>`;
                        }
                    });

                    // إضافة صف الإجمالي العام
                    tableHtml += `
                        <tr class="table-grand-total">
                            <td colspan="6">
                                <i class="fas fa-chart-bar me-2"></i>
                                <strong>المجموع الكلي</strong>
                            </td>
                            <td class="fw-bold">${formatNumber(grandTotals.total_amount)}</td>
                            <td colspan="2"></td>
                            <td class="fw-bold">${formatNumber(grandTotals.paid_amount)}</td>
                            <td class="fw-bold">${formatNumber(grandTotals.remaining_amount)}</td>
                            <td></td>
                        </tr>
                    `;
                } else {
                    tableHtml = generateEmptyState();
                }

                // تحديث الجدول
                $('#reportTableBody').html(tableHtml);

                // إعادة تهيئة معالجات الأحداث
                initializeCollapseHandlers();

                // إضافة tooltip للعناصر
                $('[title]').tooltip();

                console.log('تم تحديث الجدول بنجاح');
            }

            // 4. دالة توليد جدول الأقساط الفرعية
            function generateSubInstallmentsTable(details) {
                if (!details || !Array.isArray(details) || details.length === 0) {
                    return `<div class="alert alert-info">لا توجد تفاصيل فرعية لهذا القسط</div>`;
                }

                const totalDetailsAmount = details.reduce((sum, detail) => sum + parseFloat(detail.amount || 0), 0);
                const totalDetailsPaid = details.reduce((sum, detail) => sum + parseFloat(detail.paid_amount || 0), 0);
                const totalDetailsRemaining = details.reduce((sum, detail) => sum + parseFloat(detail.remaining_amount || 0), 0);

                let tableHtml = `<div class="sub-installments-section">`;
                tableHtml += `<div class="d-flex justify-content-between align-items-center mb-3">`;
                tableHtml += `<h6 class="sub-installments-title mb-0">
                    <i class="fas fa-list me-2"></i>
                    تفاصيل الأقساط الفرعية (${details.length})
                </h6>`;
                tableHtml += `<div class="d-flex gap-2">`;
                tableHtml += `<span class="badge bg-primary">إجمالي: ${formatNumber(totalDetailsAmount)} ريال</span>`;
                tableHtml += `<span class="badge bg-success">مدفوع: ${formatNumber(totalDetailsPaid)} ريال</span>`;
                tableHtml += `<span class="badge bg-warning">متبقي: ${formatNumber(totalDetailsRemaining)} ريال</span>`;
                tableHtml += `</div>`;
                tableHtml += `</div>`;

                tableHtml += `<div class="table-responsive">`;
                tableHtml += `<table class="table table-sm sub-installments-table">`;
                tableHtml += `<thead>`;
                tableHtml += `<tr>`;
                tableHtml += `<th style="width: 60px;">#</th>`;
                tableHtml += `<th style="width: 150px;">الوصف</th>`;
                tableHtml += `<th style="width: 100px;">المبلغ</th>`;
                tableHtml += `<th style="width: 110px;">تاريخ الاستحقاق</th>`;
                tableHtml += `<th style="width: 80px;">الحالة</th>`;
                tableHtml += `<th style="width: 100px;">المدفوع</th>`;
                tableHtml += `<th style="width: 100px;">المتبقي</th>`;
                tableHtml += `<th style="width: 100px;">طريقة الدفع</th>`;
                tableHtml += `<th style="width: 100px;">رقم المرجع</th>`;
                tableHtml += `<th style="width: 80px;">أيام التأخير</th>`;
                tableHtml += `<th style="width: 150px;">الملاحظات</th>`;
                tableHtml += `<th style="width: 100px;">تاريخ الإنشاء</th>`;
                tableHtml += `</tr>`;
                tableHtml += `</thead>`;
                tableHtml += `<tbody>`;

                details.forEach((detail, detailIndex) => {
                    const detailStatusClass = getStatusClass(detail.status);
                    const detailStatusText = getStatusText(detail.status);
                    const detailOverdueClass = detail.days_overdue > 0 ? 'text-danger fw-bold' : 'text-muted';

                    tableHtml += `<tr class="detail-row">`;
                    tableHtml += `<td><span class="badge bg-secondary">${detailIndex + 1}</span></td>`;
                    tableHtml += `<td>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-alt me-2 text-info"></i>
                            <span class="fw-bold">${detail.description || 'قسط فرعي'}</span>
                        </div>
                    </td>`;
                    tableHtml += `<td><span class="amount-positive fw-bold">${formatNumber(detail.amount)} ريال</span></td>`;
                    tableHtml += `<td><span class="fw-bold">${formatDate(detail.due_date)}</span></td>`;
                    tableHtml += `<td><span class="status-badge ${detailStatusClass}">${detailStatusText}</span></td>`;
                    tableHtml += `<td><span class="amount-positive">${formatNumber(detail.paid_amount)} ريال</span></td>`;
                    tableHtml += `<td><span class="amount-negative">${formatNumber(detail.remaining_amount)} ريال</span></td>`;
                    tableHtml += `<td><span class="badge bg-info">${detail.payment_method || 'غير محدد'}</span></td>`;
                    tableHtml += `<td><code class="small">${detail.reference_number || '-'}</code></td>`;
                    tableHtml += `<td><span class="${detailOverdueClass}">
                        ${detail.days_overdue > 0 ? detail.days_overdue + ' يوم' : 'في الموعد'}
                    </span></td>`;
                    tableHtml += `<td><small class="text-muted" title="${detail.notes || '-'}">
                        ${detail.notes ? (detail.notes.length > 30 ? detail.notes.substring(0, 30) + '...' : detail.notes) : '-'}
                    </small></td>`;
                    tableHtml += `<td><small class="text-muted">${formatDate(detail.created_at)}</small></td>`;
                    tableHtml += `</tr>`;
                });

                // إضافة صف الإجمالي للتفاصيل
                tableHtml += `<tr class="table-info fw-bold">`;
                tableHtml += `<td colspan="2" class="text-end"><strong>إجمالي التفاصيل:</strong></td>`;
                tableHtml += `<td><span class="amount-positive fw-bold">${formatNumber(totalDetailsAmount)} ريال</span></td>`;
                tableHtml += `<td colspan="2"></td>`;
                tableHtml += `<td><span class="amount-positive fw-bold">${formatNumber(totalDetailsPaid)} ريال</span></td>`;
                tableHtml += `<td><span class="amount-negative fw-bold">${formatNumber(totalDetailsRemaining)} ريال</span></td>`;
                tableHtml += `<td colspan="5"></td>`;
                tableHtml += `</tr>`;

                tableHtml += `</tbody>`;
                tableHtml += `</table>`;
                tableHtml += `</div>`;
                tableHtml += `</div>`; // end sub-installments-section

                return tableHtml;
            }

            // 5. دالة توليد الحالة الفارغة
            function generateEmptyState() {
                return `
                    <tr>
                        <td colspan="12" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد أقساط مطابقة للفلاتر المحددة</h5>
                                <p class="text-muted mb-3">جرب تغيير الفلاتر أو إعادة تعيينها للحصول على نتائج</p>
                                <button class="btn btn-outline-primary" onclick="$('#resetBtn').click();">
                                    <i class="fas fa-refresh me-2"></i>إعادة تعيين الفلاتر
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }

            // 6. تحسين معالج أحداث Collapse
            $(document).on('show.bs.collapse', '[id^="details-"]', function() {
                const collapseId = $(this).attr('id');
                const iconId = `#icon-${collapseId}`;
                $(iconId).addClass('rotated');
                console.log('فتح التفاصيل:', collapseId);
            });

            $(document).on('hide.bs.collapse', '[id^="details-"]', function() {
                const collapseId = $(this).attr('id');
                const iconId = `#icon-${collapseId}`;
                $(iconId).removeClass('rotated');
                console.log('إغلاق التفاصيل:', collapseId);
            });

            // معالجات أزرار التحكم السريع
            $(document).on('click', '#expandAllDetails', function() {
                console.log('توسيع الكل');
                $('.collapse').addClass('show').fadeIn(200);
                $('.collapse-icon').addClass('rotated');
            });

            $(document).on('click', '#collapseAllDetails', function() {
                console.log('طي الكل');
                $('.collapse').removeClass('show').fadeOut(200);
                $('.collapse-icon').removeClass('rotated');
            });

            // 8. تحديث الدالة الأصلية
            window.updateTableBody = updateTableBodyFixed;

            // تهيئة معالجات الأحداث
            setTimeout(function() {
                initializeCollapseHandlers();
                console.log('تم تهيئة معالجات الأحداث');
            }, 100);

            // التعامل مع تغيير نوع التاريخ
            $('#date_type').change(function() {
                loadReportData();
            });

            // التعامل مع تصفية التقرير
            $('#filterBtn').click(function() {
                $(this).addClass('loading');
                loadReportData();
            });

            // إعادة تعيين الفلاتر
            $('#resetBtn').click(function() {
                $('#reportForm')[0].reset();
                $('.select2').val(null).trigger('change');
                $('#date_type').val('custom').trigger('change');
                loadReportData();
            });

            // التعامل مع تصدير إكسل
            $('#exportExcel').click(function() {
                exportToExcel();
            });

            // التعامل مع الطباعة
            $('#printBtn').click(function() {
                window.print();
            });

            // تحديث البيانات عند تغيير أي فلتر
            $('.select2').on('change', function() {
                if ($('#date_type').val() === 'custom' || $(this).attr('id') !== 'date_type') {
                    loadReportData();
                }
            });

            // دالة تحميل بيانات التقرير
            function loadReportData() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#reportForm').serialize();

                $.ajax({
                    url: '{{ route('ClientReport.customerInstallmentsAjax') }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        updateReportDisplay(response);
                        $('.loading-overlay').fadeOut();
                        $('#filterBtn').prop('disabled', false);
                        $('#filterBtn').removeClass('loading');

                        // Add success animation
                        $('#reportContainer').addClass('fade-in');
                    },
                    error: function(xhr, status, error) {
                        console.error('خطأ في تحميل البيانات:', error);
                        $('.loading-overlay').fadeOut();
                        $('#filterBtn').prop('disabled', false);
                        $('#filterBtn').removeClass('loading');

                        // Show error message
                        showAlert('حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.', 'danger');
                    }
                });
            }

            // دالة تحديث عرض التقرير
            function updateReportDisplay(data) {
                // تحديث الإجماليات مع تأثير العد التصاعدي
                animateValue('#totalInstallments', 0, data.totals.total_installments, 1000);
                animateValue('#totalAmount', 0, data.totals.total_amount, 1000);
                animateValue('#pendingAmount', 0, data.totals.pending_amount, 1000);
                animateValue('#overdueAmount', 0, data.totals.overdue_amount, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    تقرير أقساط العملاء
                `);

                // تحديث الرسم البياني
                updateChart(data.chart_data);

                // تحديث جدول البيانات
                updateTableBodyFixed(data.installments, data.totals);
            }

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                const ctx = document.getElementById('installmentChart').getContext('2d');

                if (installmentChart) {
                    installmentChart.destroy();
                }

                installmentChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['مدفوع', 'قيد الانتظار', 'متأخر', 'مدفوع جزئياً'],
                        datasets: [{
                            data: [
                                chartData.paid || 0,
                                chartData.pending || 0,
                                chartData.overdue || 0,
                                chartData.partial || 0
                            ],
                            backgroundColor: [
                                '#28a745',
                                '#ffc107',
                                '#dc3545',
                                '#17a2b8'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        family: 'Cairo',
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(255, 255, 255, 0.2)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': ' + formatNumber(context.parsed) + ' ريال';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // دالة الحصول على كلاس حالة القسط
            function getStatusClass(status) {
                switch (status) {
                    case 'paid':
                        return 'status-paid';
                    case 'pending':
                        return 'status-pending';
                    case 'overdue':
                        return 'status-overdue';
                    case 'partial':
                        return 'status-partial';
                    default:
                        return 'status-pending';
                }
            }

            // دالة الحصول على نص حالة القسط
            function getStatusText(status) {
                switch (status) {
                    case 'paid':
                        return 'مدفوع';
                    case 'pending':
                        return 'قيد الانتظار';
                    case 'overdue':
                        return 'متأخر';
                    case 'partial':
                        return 'مدفوع جزئياً';
                    default:
                        return 'قيد الانتظار';
                }
            }

            // دالة تنسيق التاريخ للعرض
            function formatDate(dateString) {
                if (!dateString) return 'غير محدد';
                const date = new Date(dateString);
                return date.toLocaleDateString('ar-SA', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                });
            }

            // دالة تنسيق الأرقام
            function formatNumber(number) {
                return parseFloat(number || 0).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // دالة تنسيق العملة
            function formatCurrency(amount) {
                return new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(amount);
            }

            // دالة الرسوم المتحركة للأرقام
            function animateValue(element, start, end, duration) {
                const obj = $(element);
                const range = end - start;
                const increment = end > start ? 1 : -1;
                const stepTime = Math.abs(Math.floor(duration / range));
                const timer = setInterval(function() {
                    start += increment;
                    obj.text(formatNumber(start));
                    if (start == end) {
                        clearInterval(timer);
                    }
                }, stepTime);
            }

            // دالة تصدير إكسل
            function exportToExcel() {
                showAlert('جاري تصدير الملف...', 'info');

                const table = document.querySelector('#reportContainer table');
                const wb = XLSX.utils.table_to_book(table, {
                    raw: false,
                    cellDates: true
                });

                const today = new Date();
                const fileName = `تقرير_أقساط_العملاء_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

                XLSX.writeFile(wb, fileName);
                showAlert('تم تصدير الملف بنجاح!', 'success');
            }

            // دالة عرض التنبيهات
            function showAlert(message, type) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show position-fixed"
                         style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;

                $('body').append(alertHtml);

                // إزالة التنبيه تلقائياً بعد 3 ثوان
                setTimeout(() => {
                    $('.alert').alert('close');
                }, 3000);
            }

            // التعامل مع أزرار العرض
            $('#summaryViewBtn, #detailViewBtn').click(function() {
                $('.btn-group .btn-modern').removeClass('btn-primary-modern active').addClass(
                    'btn-outline-modern');
                $(this).removeClass('btn-outline-modern').addClass('btn-primary-modern active');

                if ($(this).attr('id') === 'summaryViewBtn') {
                    $('#chartSection').show();
                    $('#totalsSection').show();
                    $('#reportContainer').hide();
                    showAlert('تم التبديل إلى عرض الملخص', 'info');
                } else {
                    $('#chartSection').hide();
                    $('#totalsSection').hide();
                    $('#reportContainer').show();
                    showAlert('تم التبديل إلى عرض التفاصيل', 'info');
                }
            });

            // تأثيرات hover للكروت
            $('.stats-card').hover(
                function() {
                    $(this).css('transform', 'translateY(-8px) scale(1.02)');
                },
                function() {
                    $(this).css('transform', 'translateY(0) scale(1)');
                }
            );

            // تأثيرات hover للأزرار
            $('.btn-modern').hover(
                function() {
                    if (!$(this).hasClass('active')) {
                        $(this).css('transform', 'translateY(-2px)');
                    }
                },
                function() {
                    if (!$(this).hasClass('active')) {
                        $(this).css('transform', 'translateY(0)');
                    }
                }
            );

            // تحسين UX - تلميحات الأدوات
            $('[data-bs-toggle="tooltip"]').tooltip();

            // إضافة معالج للتاريخ
            $('#date_from, #date_to').on('change', function() {
                if ($('#date_type').val() === 'custom') {
                    loadReportData();
                }
            });

            // إضافة معالج للصفحة المتحركة
            $(window).scroll(function() {
                const scrollTop = $(this).scrollTop();
                if (scrollTop > 100) {
                    $('.page-header').addClass('scrolled');
                } else {
                    $('.page-header').removeClass('scrolled');
                }
            });

            // إعداد تحديث البيانات التلقائي كل 5 دقائق
            setInterval(function() {
                if (!$('.loading-overlay').is(':visible')) {
                    loadReportData();
                }
            }, 300000); // 5 دقائق

            // إضافة keyboard shortcuts
            $(document).keydown(function(e) {
                // Ctrl+P للطباعة
                if (e.ctrlKey && e.keyCode === 80) {
                    e.preventDefault();
                    $('#printBtn').click();
                }
                // Ctrl+E لتصدير Excel
                if (e.ctrlKey && e.keyCode === 69) {
                    e.preventDefault();
                    $('#exportExcel').click();
                }
                // Ctrl+R لإعادة تحميل البيانات
                if (e.ctrlKey && e.keyCode === 82) {
                    e.preventDefault();
                    $('#filterBtn').click();
                }
                // ESC لإعادة تعيين الفلاتر
                if (e.keyCode === 27) {
                    $('#resetBtn').click();
                }
            });

            // إضافة تحسينات للـ responsive
            function adjustForMobile() {
                if ($(window).width() < 768) {
                    $('.sub-installments-table').wrap('<div class="table-responsive-sm"></div>');
                    $('.installment-details-card').removeClass('p-3').addClass('p-2');
                } else {
                    $('.table-responsive-sm').children().unwrap();
                    $('.installment-details-card').removeClass('p-2').addClass('p-3');
                }
            }

            // تشغيل عند تحميل الصفحة وتغيير حجم النافذة
            adjustForMobile();
            $(window).resize(adjustForMobile);

            // إضافة تحسينات الأداء
            let resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (installmentChart) {
                        installmentChart.resize();
                    }
                }, 250);
            });

            // تحسين الطباعة
            window.addEventListener('beforeprint', function() {
                // إظهار جميع التفاصيل المطوية قبل الطباعة
                $('.collapse').addClass('show');
                $('.collapse-icon').addClass('rotated');
            });

            window.addEventListener('afterprint', function() {
                // إعادة إخفاء التفاصيل بعد الطباعة
                $('.collapse').removeClass('show');
                $('.collapse-icon').removeClass('rotated');
            });

            console.log('تم تحميل إصلاحات عرض تفاصيل الأقساط بنجاح');
        });
    </script>
@endsection