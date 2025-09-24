@extends('master')
@section('title')
    ادارة المواعيد
@stop
@section('css')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <style>
        /* Calendar Styles */
        #calendar {
            max-width: 100%;
            margin: 0 auto;
        }

        .fc-event {
            cursor: pointer;
            font-size: 0.85em;
            padding: 2px 4px;
        }

        /* إضافة CSS للتجاوب مع أحجام الشاشات المختلفة */
        @media (max-width: 575.98px) {
            .min-mobile {
                display: table-cell;
            }


            .fixed-status-menu {
                position: fixed;
                left: 20px;
                top: 50%;
                transform: translateY(-50%);
                z-index: 1000;
                background: white;
                border-radius: 8px;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
                padding: 10px 0;
                width: 180px;
            }

            .status-menu-item {
                padding: 8px 15px;
                display: flex;
                align-items: center;
                cursor: pointer;
                transition: all 0.3s;
            }

            .status-menu-item:hover {
                background-color: #f8f9fa;
            }

            .status-menu-item i {
                margin-left: 8px;
                font-size: 14px;
            }

            .status-menu-item .text-danger {
                color: #dc3545;
            }

            .status-menu-item .text-success {
                color: #28a745;
            }

            .status-menu-item .text-warning {
                color: #ffc107;
            }

            .status-menu-item .text-info {
                color: #17a2b8;
            }

            .status-menu-item .text-primary {
                color: #007bff;
            }

            .min-tablet {
                display: none;
            }

            .min-desktop {
                display: none;
            }
        }

        @media (min-width: 576px) and (max-width: 991.98px) {
            .min-mobile {
                display: table-cell;
            }

            .min-tablet {
                display: table-cell;
            }

            .min-desktop {
                display: none;
            }
        }

        @media (min-width: 992px) {
            .min-mobile {
                display: table-cell;
            }

            .min-tablet {
                display: table-cell;
            }

            .min-desktop {
                display: table-cell;
            }
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            left: auto;
        }

        /* عشان نخلي القائمة ثابتة على الشاشة وقت ما تظهر */
        .fixed-dropdown-menu {
            position: fixed !important;
            top: 100px;
            /* عدّل حسب المكان المناسب */
            right: 120px;
            /* تزحزح نحو اليسار */
            z-index: 1050;
            /* عشان تبقى فوق كل العناصر */
        }
    </style>

@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">


    @include('layouts.alerts.success')
    @include('layouts.alerts.error')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة المواعيد</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-3">
                <div class="d-flex flex-wrap justify-content-end" style="gap: 10px;">

                    <!-- زر أضف العميل -->

                    <!-- زر تحميل ملف -->
                    <label class="bg-white border d-flex align-items-center justify-content-center"
                        style="width: 44px; height: 44px; cursor: pointer; border-radius: 6px;" title="تحميل ملف">
                        <i class="fas fa-cloud-upload-alt text-primary"></i>
                        <input type="file" name="file" class="d-none">
                    </label>

                    <!-- زر استيراد -->
                    <button type="submit" class="bg-white border d-flex align-items-center justify-content-center"
                        style="width: 44px; height: 44px; border-radius: 6px;" title="استيراد ك Excel">
                        <i class="fas fa-database text-primary"></i>
                    </button>

                    <!-- زر حد ائتماني -->


                    <!-- زر تصدير ك Excel (الجديد) -->
                    <button id="exportExcelBtn" class="bg-white border d-flex align-items-center justify-content-center"
                        style="width: 44px; height: 44px; border-radius: 6px;" title="تصدير ك Excel">
                        <i class="fas fa-file-excel text-primary"></i>
                    </button>

                    <a href="{{ route('appointments.create') }}"
                        class="btn btn-success d-flex align-items-center justify-content-center"
                        style="height: 44px; padding: 0 16px; font-weight: bold; border-radius: 6px;">
                        <i class="fas fa-plus ms-2"></i>
                        أضف موعد جديد
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <h4 class="card-title">بحث</h4>


                    <div class="card-body">
                        <form class="form" action="{{ route('appointments.index') }}" method="GET">
                            <div class="form-body row">
                                <div class="form-group col-md-4">
                                    <label for=""> اختر الاجراء</label>
                                    <select name="status" id="feedback2" class="form-control">
                                        <option value="">-- اختر الحالة --</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>تم
                                            جدولته</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                            تم</option>
                                        <option value="ignored" {{ request('status') == 'ignored' ? 'selected' : '' }}>صرف
                                            النظر عنه</option>
                                        <option value="rescheduled"
                                            {{ request('status') == 'rescheduled' ? 'selected' : '' }}>تم جدولته مجددا
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="sales_person_user">مسؤول المبيعات (المستخدمين)</label>
                                    <select name="sales_person_user" class="form-control" id="sales_person_user">
                                        <option value="">مسؤول المبيعات</option>
                                        @foreach ($employees as $user)
                                            <option value="{{ $user->id }}"
                                                {{ request('sales_person_user') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="time" class="form-label">اختار الحالة </label>
                                    <select class="form-control" name="status_id">
                                        <option value="">-- اختر الحالة --</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->id }}"
                                                {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="collapse" id="advancedSearchForm">
                                <div class="form-body row d-flex align-items-center g-0">
                                    <div class="form-group col-md-2">
                                        <select name="action_type" class="form-control">
                                            <option value="">نوع الإجراء</option>
                                            @foreach ($actionTypes as $type)
                                                <option value="{{ $type }}"
                                                    {{ request('action_type') == $type ? 'selected' : '' }}>
                                                    {{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <input type="date" class="form-control" placeholder="من" name="from_date"
                                            value="{{ request('from_date') }}">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <input type="date" class="form-control" placeholder="إلى" name="to_date"
                                            value="{{ request('to_date') }}">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <select name="client_id" class="form-control">
                                            <option value="">العميل</option>
                                            @if (isset($clients) && !empty($clients) && count($clients) > 0)
                                                @foreach ($clients as $client)
                                                    <option value="{{ $client->id }}"
                                                        {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                                        {{ $client->trade_name }} {{ $client->last_name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="">لا توجد عملاء حاليا</option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <select name="employee_id" class="form-control">
                                            <option value="">أضيفت بواسطة</option>
                                            @if (isset($employees) && !empty($employees) && count($employees) > 0)
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}"
                                                        {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                        {{ $employee->name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="">لا توجد موظفين حاليا</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
                                <a class="btn btn-outline-secondary ml-2 mr-2" data-toggle="collapse"
                                    data-target="#advancedSearchForm">
                                    <i class="bi bi-sliders"></i> بحث متقدم
                                </a>
                                <a href="{{ route('appointments.index') }}"
                                    class="btn btn-outline-warning waves-effect waves-light">إلغاء</a>
                            </div>
                        </form>
                    </div>






                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-row-reverse">

                    <!-- التبويبات على اليمين -->
                    <ul class="nav nav-tabs ms-auto" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="appointments-tab" data-bs-toggle="tab"
                                data-bs-target="#appointments" type="button" role="tab"
                                aria-controls="appointments" aria-selected="true">
                                الحجوزات ({{ $appointments->where('status', 1)->count() ?? 0 }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="supply-orders-tab" data-bs-toggle="tab"
                                data-bs-target="#supply-orders" type="button" role="tab"
                                aria-controls="supply-orders" aria-selected="false">
                                أوامر التوريد ({{ $appointments->where('status', 2)->count() ?? 0 }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="clients-tab" data-bs-toggle="tab" data-bs-target="#clients"
                                type="button" role="tab" aria-controls="clients" aria-selected="false">
                                العملاء ({{ $clientsCount ?? 0 }})
                            </button>
                        </li>
                    </ul>

                    <!-- أزرار عرض القائمة والشبكة والتقويم على اليسار -->
                    <div class="btn-group me-2" role="group" aria-label="View Toggle">
                        <button type="button" class="btn btn-outline-secondary active" id="gridViewBtn"
                            title="عرض الشبكة">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="listViewBtn" title="عرض القائمة">
                            <i class="fas fa-list"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="calendarViewBtn"
                            title="عرض التقويم">
                            <i class="fas fa-calendar-alt"></i>
                        </button>
                    </div>

                </div>
            </div>


            <div class="card-body">
                <div class="tab-content" id="appointmentsTabsContent">
                    <!-- Appointments Tab -->
                    <div class="tab-pane fade show active" id="appointments-content" role="tabpanel"
                        aria-labelledby="appointments-tab">
                        @include('client::appointments.partials.appointments_table', [
                            'appointments' => $appointments->where('status', 1),
                        ])
                    </div>

                    <!-- Supply Orders Tab -->
                    <div class="tab-pane fade" id="supply-orders-content" role="tabpanel"
                        aria-labelledby="supply-orders-tab">
                        {{-- @include('client.appointments.partials.supply_orders_table', [
                    'appointments' => $appointments->where('status', 2)
                ]) --}}
                    </div>

                    <!-- Clients Tab -->
                    <div class="tab-pane fade" id="clients-content" role="tabpanel" aria-labelledby="clients-tab">
                        {{-- @include('client.appointments.partials.clients_table', [
                    'clients' => $clients
                ]) --}}
                    </div>
                </div>
            </div>
        </div>
        <!-- Add this CSS -->
        <style>
            .btn-group .btn {
                padding: 0.375rem 0.75rem;
            }

            .btn-group .btn i {
                font-size: 1rem;
            }

            .btn-group .btn.active {
                background-color: #e9ecef;
                border-color: #dee2e6;
            }
        </style>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const gridViewBtn = document.getElementById('gridViewBtn');
                const listViewBtn = document.getElementById('listViewBtn');

                // Add click event listeners
                gridViewBtn.addEventListener('click', function() {
                    this.classList.add('active');
                    listViewBtn.classList.remove('active');
                    // Add your grid view logic here
                    console.log('Switched to Grid View');
                });

                listViewBtn.addEventListener('click', function() {
                    this.classList.add('active');
                    gridViewBtn.classList.remove('active');
                    // Add your list view logic here
                    console.log('Switched to List View');
                });
            });
        </script>


    </div>
    </div>
    </div>




@endsection
@section('scripts')
    <script src="{{ asset('assets/js/applmintion.js') }}"></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection
