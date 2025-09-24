@extends('master')

@section('title')
تقرير مبيعات التصنيفات
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تقرير مبيعات التصنيفات</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">الرئيسية</li>
                        <li class="breadcrumb-item active">عرض</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card mt-4">
    <div class="card-body">
        <form>
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="sessionNumber" class="form-label">رقم الجلسة</label>
                    <select class="form-control" id="sessionNumber">
                        <option>الكل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label">التصنيف</label>
                    <select class="form-control" id="category">
                        <option>الكل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="posShift" class="form-label">Pos Shift</label>
                    <select class="form-control" id="posShift">
                        <option>الكل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="posShiftDevice" class="form-label">Pos Shift Device</label>
                    <select class="form-control" id="posShiftDevice">
                        <option>الكل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="orderSource" class="form-label">مصدر الطلب</label>
                    <select class="form-control" id="orderSource">
                        <option>الكل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="store" class="form-label">المخزن</label>
                    <select class="form-control" id="store">
                        <option>الكل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="dateFrom" class="form-label">التاريخ من</label>
                    <input type="date" class="form-control" id="dateFrom" value="2024-12-10">
                </div>
                <div class="col-md-3">
                    <label for="dateTo" class="form-label">التاريخ إلى</label>
                    <input type="date" class="form-control" id="dateTo" value="2025-01-10">
                </div>
                <div class="col-md-3">
                    <label for="currency" class="form-label">العملة</label>
                    <select class="form-control" id="currency">
                        <option>الجميع إلى (SAR)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="groupBy" class="form-label">تجميع حسب</label>
                    <select class="form-control" id="groupBy">
                        <option>الكل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sortBy" class="form-label">ترتيب حسب</label>
                    <select class="form-control" id="sortBy">
                        <option>التصنيف</option>
                    </select>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12 text-left">
                    <button type="submit" class="btn btn-primary">عرض التقرير</button>
                </div>
            </div>
        </form>
    </div>
</div>

</div>
@endsection