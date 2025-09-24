@extends('master')

@section('title')
    عرض أنواع الوحدات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض أنواع الوحدات</h2>
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






        <div class="container my-5">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary ms-2">تعديل</button>
                <button class="btn btn-outline-secondary ms-2">تعطيل</button>
                <button class="btn btn-outline-danger">حذف</button>
            </div>
            <div class="card">
                <div class="card-header bg-light">
                    <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">تفاصيل</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="activities-tab" data-bs-toggle="tab" data-bs-target="#activities" type="button" role="tab">سجل النشاطات</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="units-tab" data-bs-toggle="tab" data-bs-target="#units" type="button" role="tab">الوحدات</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body tab-content">
                    <!-- تفاصيل -->
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        <h5 class="card-title">معلومات نوع الوحدة</h5>
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <p><strong>الاسم:</strong> {{ $unitType->name }}</p>
                                <p><strong>قاعدة التسعير:</strong> {{ $unitType->pricing_basis }}</p>
                                <p><strong>الضريبة 1:</strong> {{ $unitType->tax1 }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>طريقة التسعير:</strong> {{ $unitType->pricing_method }}</p>
                                <p><strong>الوصف:</strong> {{ $unitType->description }}</p>
                                <p><strong>الضريبة 2:</strong> {{ $unitType->tax2 }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>الحضور:</strong> {{ $unitType->check_in_time }}</p>
                                <p><strong>المغادرة:</strong> {{ $unitType->check_out_time }}</p>
                            </div>
                        </div>
                    </div>
    
                    <!-- سجل النشاطات -->
                    <div class="tab-pane fade" id="activities" role="tabpanel">
                        <h5 class="card-title">سجل النشاطات</h5>
                        <p>لا توجد نشاطات مسجلة حاليًا.</p>
                    </div>
    
                    <!-- الوحدات -->
                    <div class="tab-pane fade" id="units" role="tabpanel">
                        <h5 class="card-title">الوحدات</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>الإتاحة</th>
                                    <th>الحالة</th>
                                    <th>الترتيب</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ثلاث غرف</td>
                                    <td><span class="text-success">متاح <i class="bi bi-check-circle"></i></span></td>
                                    <td><span class="text-success">نشط <i class="bi bi-dot"></i></span></td>
                                    <td>
                                        <div class="btn-group">
                                            <div class="dropdown">
                                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                        type="button" id="dropdownMenuButton303" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false"></button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                    <li>
                                                        <a class="dropdown-item" href="">
                                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="">
                                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="" method="POST"
                                                              onsubmit="return confirm('هل أنت متأكد من أنك تريد حذف هذه الوحدة؟')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger">
                                                                <i class="fa fa-trash"></i> حذف
                                                            </button>
                                                        </form>
                                                    </li>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    

    
    
    

    
    

    

    

@endsection
