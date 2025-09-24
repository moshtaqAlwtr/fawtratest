@extends('master')

@section('title')
    فترة المبيعات
@stop

@section('content')
<div class="fs-5">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">فترة المبيعات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- زر إضافة فترة جديدة -->
        <div class="card mb-2">
            <div class="card-body d-flex justify-content-between align-items-center">
                <a href="{{ route('SalesPeriods.create') }}" class="btn btn-success">
                    <i class="fa fa-plus me-2"></i>
                    أضف فترة مبيعات
                </a>
            </div>
        </div>

        <!-- نموذج البحث -->
        <div class="card mb-2">
            <div class="card-content">
                <div class="card-body pb-0">
                    <h4 class="card-title">بحث</h4>
                </div>
                <div class="card-body pt-0">
                    <form class="form" method="get" action="{{ route('SalesPeriods.index') }}">
                        <div class="form-body row">
                            <div class="form-group col-md-3">
                                <label class="">البحث بواسطة الموظف</label>
                                <select name="employee_id" class="form-control select2">
                                    <option value="">اختر الموظف</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="">البحث بواسطة قواعد العمولة</label>
                                <select name="commission_id" class="form-control select2">
                                    <option value="">اختر قاعدة العمولة</option>
                                    @foreach ($commissions as $commission)
                                        <option value="{{ $commission->id }}" {{ request('commission_id') == $commission->id ? 'selected' : '' }}>
                                            {{ $commission->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label>فترة المبيعات (من)</label>
                                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>فترة المبيعات (إلى)</label>
                                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="form-actions mt-2">
                            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
                            <a href="{{ route('SalesPeriods.index') }}" class="btn btn-outline-warning waves-effect waves-light">إلغاء الفلتر</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- جدول عرض الفترات -->
        <div class="card">
            <div class="card-body">
<table class="table fs-5">
    <thead>
        <tr>
            <th>#</th>
            <th>الموظف</th>
            <th>قاعدة العمولة</th>
            <th>الفترة</th>
            <th>المبيعات</th>
            <th>نسبة العمولة</th>
            <th>العمولة المستحقة</th>
            <th>الهدف</th>
            <th>نسبة التقدم</th>
            <th>الحالة</th>
              <th>الضبط</th>
        </tr>
    </thead>
    <tbody>
        @foreach($SalesPeriods as $i => $period)
            @foreach($period->commissionSales as $row)
                @php
                    $employee = $row->employee;
                    $commission = $row->commission;
                    $sales_amount = $row->sales_amount ?? 0;
                    $commission_value = $commission->value ?? 0;
                    $commission_type = $commission->target_type ?? 'amount';
                    $sales_with_vat = $sales_amount * 1.15;

                    // استخراج كل عمليات المبيعات لهذا الموظف والعمولة في هذه الفترة
                    $sales_commissions = \App\Models\SalesCommission::where('employee_id', $row->employee_id)
                        ->where('commission_id', $row->commission_id)
                        ->whereBetween('created_at', [$period->from_date, $period->to_date])
                        ->get();

                    // استخراج نسبة العمولة (المتوسط أو أول قيمة - حسب نظامك)
                    $ratio = $sales_commissions->avg('ratio') ?? 0;

                    // حساب العمولة المستحقة (جمع جميع العمليات × نسبتها)
                    $commission_due = $sales_commissions->sum(function($sale){
                        return ($sale->sales_amount * 1.15) * ($sale->ratio / 100);
                    });

                    $progress = $commission_value > 0
                        ? round(($commission_type == 'amount' ? $sales_with_vat : $sales_amount) / $commission_value * 100)
                        : 0;
                    $progress = $progress > 100 ? 100 : $progress;
                    $is_achieved = $commission_type == 'amount'
                        ? $sales_with_vat >= $commission_value
                        : $sales_amount >= $commission_value;
                @endphp
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $employee->name ?? '-' }}</td>
                    <td>{{ $commission->name ?? '-' }}</td>
                    <td>
                        {{ $period->from_date }} <span class="mx-1"> - </span> {{ $period->to_date }}
                    </td>
                    <td>{{ number_format($sales_with_vat, 2) }} ر.س</td>
                    <td>{{ $ratio ? number_format($ratio, 2).'%' : '-' }}</td>
                    <td>{{ number_format($commission_due, 2) }} ر.س</td>
                    <td>{{ number_format($commission_value, 2) }} ر.س</td>
                    <td style="min-width:180px;">
                        <div class="d-flex align-items-center gap-2">
                            <span>{{ $progress }}%</span>
                            <div class="progress w-100" style="height: 10px;">
                                <div class="progress-bar {{ $is_achieved ? 'bg-success' : 'bg-info' }}"
                                    role="progressbar"
                                    style="width: {{ $progress }}%;"
                                    aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($is_achieved)
                            <span class="badge badge-success">✅ تحقق الهدف</span>
                        @else
                            <span class="badge badge-warning">❌ لم يتحقق بعد</span>
                        @endif
                    </td>
                       <td>
                                        <div class="btn-group">
                                            <div class="dropdown">
                                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                        type="button" id="dropdownMenuButton303" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false"></button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('SalesPeriods.show', 1) }}">
                                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                        </a>
                                                    </li>
                        
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                </tr>
            @endforeach
        @endforeach
        @if($SalesPeriods->isEmpty() || $SalesPeriods->sum(fn($period) => $period->commissionSales->count()) == 0)
            <tr>
                <td colspan="10" class="text-center text-danger">لا توجد بيانات مطابقة للبحث.</td>
            </tr>
        @endif
    </tbody>
</table>



            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $('.select2').select2();
        });
    </script>
@endsection
