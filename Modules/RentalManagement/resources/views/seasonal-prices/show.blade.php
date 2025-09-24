@extends('master')

@section('title')
    عرض الأسعار الموسمية
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض الأسعار الموسمية</h2>
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

    <div class="card mt-5">
        <!-- بطاقة التفاصيل -->
        <div class="card">
            <div class="card-header bg-tertiary text-white d-flex justify-content-between">
                <h5 class="mb-0">تفاصيل السعر الموسمي</h5>
                <div>
                    <button class="btn btn-sm btn-secondary">تعديل</button>
                    <button class="btn btn-sm btn-danger">حذف</button>
                </div>
            </div>
            <div class="card-body">
                <h6 class="text-muted mb-3 text-white p-2">بيانات السعر الموسمي</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>التاريخ من:</strong> {{ $seasonalPrice->date_from }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>التاريخ إلى:</strong> {{ $seasonalPrice->date_to }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>قاعدة التسعير:</strong> {{ $seasonalPrice->pricingRule->pricingName }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>نوع الوحدة:</strong> {{ $seasonalPrice->unitType->name }}</p>
                    </div>
                </div>
                <h6 class="text-muted mb-3">تم تفعيله</h6>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>يوم</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $days = ['sunday' => 'الأحد', 'monday' => 'الإثنين', 'tuesday' => 'الثلاثاء', 'wednesday' => 'الأربعاء', 'thursday' => 'الخميس', 'friday' => 'الجمعة', 'saturday' => 'السبت'];
                        @endphp
                        @foreach ($days as $dayKey => $dayName)
                            <tr>
                                <td>{{ $dayName }}</td>
                                <td>{{ isset($seasonalPrice->working_days[$dayKey]) && $seasonalPrice->working_days[$dayKey] ? '✓' : '✗' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
