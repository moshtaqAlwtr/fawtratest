@extends('master')

@section('title', 'تقارير تتبع الوقت')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تقارير تتبع الوقت</h2>
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

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-graph-up"></i> تقارير متابعة الوقت المقسمة
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-person-fill"></i> تقرير حسب الموظف</span>
                            <div>
                                <a href="{{ route('reports.time_tracking.report_by_employee') }}" class="text-primary">التفاصيل</a>
                                <a href="#" class="text-secondary">الملخص</a>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-clipboard-data-fill"></i> تقرير حسب المشروع</span>
                            <div>
                                <a href="{{ route('reports.time_tracking.report_by_project') }}" class="text-primary">التفاصيل</a>
                                <a href="#" class="text-secondary">الملخص</a>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-exclamation-circle-fill"></i> التقرير حسب الحالة</span>
                            <div>
                                <a href="{{ route('reports.time_tracking.report_by_status') }}" class="text-primary">التفاصيل</a>
                                <a href="#" class="text-secondary">الملخص</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-calendar"></i> تقارير متابعة الوقت بالمدة الزمنية
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-calendar-day"></i> التقرير اليومي</span>
                            <div>
                                <a href="{{ route('reports.time_tracking.daily_report') }}" class="text-primary">التفاصيل</a>
                                <a href="#" class="text-secondary">الملخص</a>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-calendar-week"></i> التقرير الأسبوعي</span>
                            <div>
                                <a href="{{ route('reports.time_tracking.weekly_report') }}" class="text-primary">التفاصيل</a>
                                <a href="#" class="text-secondary">الملخص</a>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-calendar-month"></i> التقرير الشهري</span>
                            <div>
                                <a href="{{ route('reports.time_tracking.monthly_report') }}" class="text-primary">التفاصيل</a>
                                <a href="#" class="text-secondary">الملخص</a>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-calendar-check"></i> التقرير السنوي</span>
                            <div>
                                <a href="{{ route('reports.time_tracking.yearly_report') }}" class="text-primary">التفاصيل</a>
                                <a href="#" class="text-secondary">الملخص</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
