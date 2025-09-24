@extends('master')

@section('title')
تقارير SMS
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">الحملات التسويقية SMS</h2>
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
<div class="container my-5">
    <!-- البحث -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">بحث</h5>
        </div>
        <div class="card-body">
            <form class="row g-3">
                <!-- Sms Campaign Id -->
                <div class="col-md-3">
                    <label for="campaignId" class="form-label">Sms Campaign Id</label>
                    <input type="text" class="form-control" id="campaignId" placeholder="أدخل المعرف">
                </div>
                <!-- رقم الهاتف -->
                <div class="col-md-3">
                    <label for="phoneNumber" class="form-label">رقم الهاتف</label>
                    <input type="text" class="form-control" id="phoneNumber" placeholder="أدخل رقم الهاتف">
                </div>
                <!-- الرسالة -->
                <div class="col-md-3">
                    <label for="message" class="form-label">الرسالة</label>
                    <input type="text" class="form-control" id="message" placeholder="أدخل الرسالة">
                </div>
                <!-- أزرار البحث وإلغاء -->
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">بحث</button>
                    <button type="reset" class="btn btn-secondary">إلغاء الفلتر</button>
                </div>
            </form>
        </div>
    </div>

    <!-- النتائج -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">النتائج</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>الرقم التعريفي</th>
                            <th>Sms Campaign Id</th>
                            <th>الرسالة</th>
                            <th>رقم الهاتف</th>
                            <th>Sent Time</th>
                            <th>التكلفة</th>
                            <th>Sms Size</th>
                            <th>Error Number</th>
                            <th>Error Message</th>
                            <th>Campaign Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="10" class="text-center">0-0 من النتائج المعروضة</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection