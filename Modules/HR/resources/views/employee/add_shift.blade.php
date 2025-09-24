@extends('master')

@section('title')
    اضافة وردية
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">أضافة وردية </h2>
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

    <div class="d-flex justify-content-between align-items-center flex-wrap">

    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                </div>

                <div>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-danger">
                        <i class="fa fa-ban"></i>الغاء
                    </a>
                    <a href="#" onclick="document.getElementById('products_form').submit();" class="btn btn-outline-primary">
                        <i class="fa fa-save"></i>حفظ
                    </a>
                </div>

            </div>
        </div>
    </div>
    <!-- بطاقة معلومات الوردية -->
    <div class="card mt-4">
        <div class="card-content">
            <div class="card-body">
                <div class="card-title">
                    <h3><i class="fas fa-info-circle"></i> معلومات الوردية</h3>
                </div>
                <form id="products_form">
                    <div class="form-body">
                        <!-- الاسم -->
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <label>الاسم *</label>
                                <input type="text" class="form-control" placeholder="أدخل اسم الوردية" required>
                            </div>
                            <!-- النوع -->
                            <div class="form-group col-md-5">
                                <label>النوع *</label>
                                <select class="form-select" required>
                                    <option value="أساسي">أساسي</option>
                                    <option value="إضافي">إضافي</option>
                                </select>
                            </div>
                        </div>

                        <!-- أيام العمل -->
                        <h4 class="mt-4"><i class="fas fa-calendar-alt"></i> أيام العمل</h4>
                        <div class="form-row">
                            @php
                                $days = ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];
                            @endphp

                            @foreach ($days as $day)
                                <div class="col-md-12 mb-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="{{ $day }}" checked>
                                        <label class="form-check-label" for="{{ $day }}">{{ $day }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- بطاقة قواعد الحضور -->
    <div class="card mt-4">
        <div class="card-content">
            <div class="card-body">
                <div class="card-title">
                    <h3><i class="fas fa-user-clock"></i> قواعد الحضور</h3>
                </div>
                <form>
                    <div class="form-body">
                        <!-- بداية ونهاية الوردية -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>بداية الوردية *</label>
                                <input type="time" class="form-control" value="09:00" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>نهاية الوردية *</label>
                                <input type="time" class="form-control" value="17:00" required>
                            </div>
                        </div>

                        <!-- بداية ونهاية تسجيل الدخول -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>بداية تسجيل الدخول *</label>
                                <input type="time" class="form-control" value="07:00" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>نهاية تسجيل الدخول *</label>
                                <input type="time" class="form-control" value="11:00" required>
                            </div>
                        </div>

                        <!-- بداية ونهاية تسجيل الخروج -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>بداية تسجيل الخروج *</label>
                                <input type="time" class="form-control" value="15:00" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>نهاية تسجيل الخروج *</label>
                                <input type="time" class="form-control" value="19:00" required>
                            </div>
                        </div>

                        <!-- فترة سماح التأخير -->
                        <div class="form-group">
                            <label>فترة سماح التأخير *</label>
                            <div class="input-group">
                                <input type="number" class="form-control" value="15" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">دقائق</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
