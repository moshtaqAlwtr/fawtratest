@extends('master')

@section('title')
    أعدادات الحجوزات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أعدادات الحجوزات</h2>
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

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')
    <div class="container my-5">
        <div class="card">
            <form id="clientForm" action="{{ route('Reservations.setting') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                        </div>
                        <div>
                            <a href="{{ route('Reservations.index') }}" class="btn btn-outline-danger">
                                <i class="fa fa-ban"></i> الغاء
                            </a>
                            <button type="submit" form="clientForm" class="btn btn-outline-primary">
                                <i class="fa fa-save"></i> حفظ
                            </button>
                        </div>
                    </div>
                </div>
            
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="card-body">
                
                    <!-- Booking Slots Division -->
                    <div class="mb-3">
                        <label for="booking-division" class="form-label">تقسيم مواعيد الحجز</label>
                        <input type="number" class="form-control" id="booking-division" name="duration" placeholder="{{$BookingSetting->duration ?? ""}}">
                    </div>

                    <!-- Checkbox Options -->
                    <div class="vs-checkbox-con vs-checkbox-primary">
                        <input name="sending_invoices_to_the_tax_authority" type="checkbox" class="select-all-Saudi-electronic-invoice permission-main-checkbox">
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span class="">تحديد الخدمات للموظف</span>
                    </div>
                    <div class="vs-checkbox-con vs-checkbox-primary">
                        <input name="sending_invoices_to_the_tax_authority" type="checkbox" class="select-all-Saudi-electronic-invoice permission-main-checkbox">
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span class="">أحجز خدمة واحدة</span>
                    </div>

                    <!-- Payment Method Dropdown -->
                    <div class="mb-3">
                        <label for="payment-status" class="form-label">سداد العميل للحجز أونلاين</label>
                        <select class="form-control" id="payment-status" name="payment_online">
                            <option value="deactive">تم تعطيله</option>
                            <option value="active">تم تفعيله</option>
                            <option value="optional">أختياري</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection