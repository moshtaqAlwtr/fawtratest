@extends('master')

@section('title')
    عرض القسيمة
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض القسيمة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
         @csrf
           @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-md bg-light-primary">
                        <span class="avatar-content fs-4">{{ Str::substr($salarySlip->employee->full_name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $salarySlip->employee->full_name }}</h4>
                        <small class="text-muted">نشط</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title p-2 d-flex align-items-center gap-2">
            <a href="#"
                class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;" data-toggle="modal" data-target="#modal_DELETE1">
                حذف <i class="fa fa-trash ms-1"></i>
            </a>
            <div class="vr"></div>
            <a href=""
                class="btn btn-outline-primary btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                تعديل <i class="fa fa-edit ms-1"></i>
            </a>
            
            <div class="vr"></div>

            <div class="vr"></div>
            <a href=""
                class="btn btn-outline-success btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                نسخ <i class="fa fa-copy  ms-1"></i>
            </a>

            <div class="dropdown d-inline-block">
                <button class="btn btn-outline-info btn-sm d-inline-flex align-items-center justify-content-center px-3"
                    style="min-width: 90px;" type="button" id="printDropdown" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    المطبوعات <i class="dropdown-toggle"></i>
                </button>
                
                <ul class="dropdown-menu py-1" aria-labelledby="printDropdown">
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('salarySlip.printPayslip1', $salarySlip->id) }}"><i
                                class="fa fa-file-alt me-2 text-primary"></i>payslip Layout 1</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{route('salarySlip.printPayslip2', $salarySlip->id) }}"><i
                                class="fa fa-file-alt me-2 text-primary"></i>payslip Layout 2</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{route('salarySlip.printPayslip3', $salarySlip->id) }}"><i
                                class="fa fa-file-alt me-2 text-primary"></i>payslip Layout 3</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{route('salarySlip.printPayslipAr1', $salarySlip->id) }}"><i
                                class="fa fa-file-pdf me-2 text-danger"></i>نموذج 1 قسيمة راتب</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{route('salarySlip.printPayslipAr2', $salarySlip->id) }}"><i
                                class="fa fa-file-pdf me-2 text-danger"></i>نموذج 2 قسيمة راتب</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{route('salarySlip.printPayslipAr3', $salarySlip->id) }}"><i
                                class="fa fa-file-pdf me-2 text-danger"></i>نموذج 3 قسيمة راتب</a></li>
                </ul>
            </div>
            @if($salarySlip->status == "cancel")
    <a href="{{ route('salary.approve', $salarySlip->id) }}"
       class="btn btn-success btn-sm d-inline-flex align-items-center justify-content-center px-3"
       style="min-width: 90px;">
       <i class="fa fa-check-circle me-1"></i> موافقة
    </a>
@else
    <a href="{{ route('salary.cancel', $salarySlip->id) }}"
       class="btn btn-danger btn-sm d-inline-flex align-items-center justify-content-center px-3"
       style="min-width: 90px;">
       <i class="fa fa-times-circle me-1"></i> إلغاء الموافقة
    </a>
@endif

        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#details" role="tab">
                        <span>التفاصيل</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">
                        <span>سجل النشاطات</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content p-3">
                <!-- تبويب التفاصيل -->
                <div class="tab-pane active" id="details" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <!-- معلومات قسيمة الراتب -->
                            <div style="background-color: #f8f9fa;"
                                class="d-flex justify-content-between align-items-center p-2 rounded mb-3">
                                <h5 class="mb-0">معلومات قسيمة الراتب</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: 20%">تاريخ التسجيل:</th>
                                            <td style="width: 30%">{{ date('d/m/Y', strtotime($salarySlip->slip_date)) }}
                                            </td>
                                            <th style="width: 20%">رقم المنصرف:</th>
                                            <td style="width: 30%">{{ $salarySlip->id ?? '--' }}</td>
                                        </tr>
                                        <tr>
                                            <th>المسمى الوظيفي:</th>
                                            <td>{{ $salarySlip->employee->jobTitle->name ?? '--' }}</td>
                                            <th>موظف:</th>
                                            <td>{{ $salarySlip->employee->full_name }} #{{ $salarySlip->employee->id }}
                                                @if ($salarySlip->employee->status == 'active')
                                                    <i class="fa fa-check text-success"></i>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>فرع:</th>
                                            <td>{{ $salarySlip->employee->branch->name ?? '--' }}</td>
                                            <th>قسم:</th>
                                            <td>{{ $salarySlip->employee->department->name ?? '--' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- التواريخ -->
                            <div style="background-color: #f8f9fa;"
                                class="d-flex justify-content-between align-items-center p-2 rounded mb-3">
                                <h5 class="mb-0">التواريخ</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th class="">تاريخ البدء:</th>
                                            <td>{{ date('d/m/Y', strtotime($salarySlip->from_date)) }}</td>
                                            <th class="">تاريخ الإنتهاء:</th>
                                            <td>{{ date('d/m/Y', strtotime($salarySlip->to_date)) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div style="background-color: #f8f9fa;"
                                class="d-flex justify-content-between align-items-center p-2 rounded mb-3">
                                <h5 class="mb-0">مستحق</h5>
                            </div>
                            <div class="table-responsive mb-4">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>بند الراتب</th>
                                            <th>الصيغة الحسابية</th>
                                            <th class="text-end">المبلغ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($additionItems as $item)
                                            <tr>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->calculation_formula ?? '--' }}</td>
                                                <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- مستقطع -->
                            <div style="background-color: #f8f9fa;" class="p-2 rounded mb-2">
                                <h6 class="mb-0">مستقطع</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>بند الراتب</th>
                                            <th>الصيغة الحسابية</th>
                                            <th class="text-end">المبلغ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($deductionItems as $item)
                                            <tr>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->calculation_formula ?? '--' }}</td>
                                                <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- الإجماليات -->
                            <div style="background-color: #f8f9fa;"
                                class="d-flex justify-content-between align-items-center p-2 rounded mb-3">
                                <h5 class="mb-0">الإجماليات</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th>إجمالي الراتب:</th>
                                            <td>{{ number_format($salarySlip->total_salary, 2) }}
                                                {{ $salarySlip->currency }}</td>
                                        </tr>
                                        <tr>
                                            <th>إجمالي الخصم:</th>
                                            <td>{{ number_format($salarySlip->total_deductions, 2) }}
                                                {{ $salarySlip->currency }}</td>
                                        </tr>
                                        <tr>
                                            <th>صافي الراتب:</th>
                                            <td>{{ number_format($salarySlip->net_salary, 2) }}
                                                {{ $salarySlip->currency }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب سجل النشاطات -->
                <div class="tab-pane" id="activity" role="tabpanel">
                    <div class="timeline p-4">
                        <!-- يمكن إضافة سجل النشاطات هنا -->
                        <p class="text-muted text-center">لا توجد نشاطات حتى الآن</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
